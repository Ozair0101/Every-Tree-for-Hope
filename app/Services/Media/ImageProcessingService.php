<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Reading, compressing and thumbnailing an uploaded photograph.
 *
 * Built on PHP's bundled GD and exif extensions rather than Intervention Image
 * or Imagick. Both are better libraries; neither is installed, and adding a
 * composer dependency for resize-and-encode — which GD does natively — is a
 * deployment burden this project does not need. If the image work ever grows
 * beyond this, swapping the two private methods at the bottom is the whole
 * change.
 *
 * Every method degrades rather than throws. A photograph that cannot be
 * thumbnailed is still evidence, and losing a volunteer's submission because a
 * corrupt JPEG defeated the encoder would be the worst possible trade.
 */
class ImageProcessingService
{
    /** Longest edge of the compressed copy. Beyond this, nobody is looking closer. */
    private const MAX_DIMENSION = 1600;

    /** Square thumbnail edge — sized for a 3-across grid on a phone at 2× density. */
    private const THUMBNAIL_SIZE = 320;

    /**
     * JPEG quality for the compressed copy.
     *
     * 78 is where the artefacts stop being visible on photographs of foliage
     * and soil, which is what this app is full of. The original is kept
     * untouched regardless, so this is a viewing copy, not a lossy archive.
     */
    private const QUALITY = 78;

    /** Below this there is nothing to gain — re-encoding would only add loss. */
    private const MIN_BYTES_TO_COMPRESS = 300 * 1024;

    /**
     * Store an image in three forms and return everything learned about it.
     *
     * @param  array<string, mixed>  $clientMetadata  what the device reported
     * @return array{
     *     paths: array{original: string, compressed: string, thumbnail: ?string},
     *     metadata: ImageMetadata,
     *     sizes: array{original: int, compressed: int}
     * }
     */
    public function store(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        array $clientMetadata = [],
    ): array {
        // Read EXIF from the temporary file, before anything touches it —
        // storing first and reading later works, but only until someone
        // reorders these two lines.
        $metadata = $this->extract($file, $clientMetadata);

        $originalPath = $file->store("{$directory}/original", $disk);
        $originalSize = (int) Storage::disk($disk)->size($originalPath);

        $compressedPath = $this->compress($file, $directory, $disk) ?? $originalPath;
        $thumbnailPath = $this->thumbnail($file, $directory, $disk);

        return [
            'paths' => [
                'original' => $originalPath,
                'compressed' => $compressedPath,
                'thumbnail' => $thumbnailPath,
            ],
            'metadata' => $metadata,
            'sizes' => [
                'original' => $originalSize,
                'compressed' => (int) Storage::disk($disk)->size($compressedPath),
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Metadata
    |--------------------------------------------------------------------------
    */

    /**
     * Everything the file and the client can tell us.
     *
     * EXIF wins wherever it exists: it was written by the camera at the instant
     * of capture and the app cannot revise it afterwards. Client values fill the
     * gaps, which is most of the time — Android's photo picker and every social
     * app strip EXIF by default.
     *
     * @param  array<string, mixed>  $clientMetadata
     */
    public function extract(UploadedFile $file, array $clientMetadata = []): ImageMetadata
    {
        return $this->fromExif($file)->mergedWith($this->fromClient($clientMetadata));
    }

    private function fromExif(UploadedFile $file): ImageMetadata
    {
        // Only JPEG and TIFF carry EXIF. Calling exif_read_data on a PNG emits
        // a warning and returns false, which is noise in the log for nothing.
        if (! in_array($file->getMimeType(), ['image/jpeg', 'image/tiff'], true)) {
            return new ImageMetadata;
        }

        $exif = @exif_read_data($file->getRealPath(), null, true);

        if ($exif === false || ! is_array($exif)) {
            return new ImageMetadata;
        }

        // The embedded preview is binary and can be hundreds of KB — storing it
        // in a JSON column would bloat every row for no reason.
        unset($exif['THUMBNAIL']);

        $gps = $this->gpsFromExif($exif['GPS'] ?? []);
        $capturedAt = $this->capturedAtFromExif($exif);
        $make = $this->clean($exif['IFD0']['Make'] ?? null);
        $model = $this->clean($exif['IFD0']['Model'] ?? null);
        $software = $this->clean($exif['IFD0']['Software'] ?? null);

        // `source` is only 'exif' when the camera actually told us something
        // worth having. Every JPEG carries FILE and COMPUTED sections whatever
        // wrote it, so claiming 'exif' merely because exif_read_data succeeded
        // would label client-supplied coordinates as camera-verified — exactly
        // the distinction this field exists to make.
        $hasCameraMetadata = $gps['latitude'] !== null
            || $capturedAt !== null
            || $make !== null
            || $model !== null;

        return new ImageMetadata(
            latitude: $gps['latitude'],
            longitude: $gps['longitude'],
            capturedAt: $capturedAt,
            deviceMake: $make,
            deviceModel: $model,
            deviceOs: $software,
            source: $hasCameraMetadata ? 'exif' : 'none',
            // The raw block is kept regardless — dimensions and colour data are
            // still useful even when there is no camera identity in it.
            exif: $this->sanitiseExif($exif),
            width: $exif['COMPUTED']['Width'] ?? null,
            height: $exif['COMPUTED']['Height'] ?? null,
        );
    }

    /** @param array<string, mixed> $client */
    private function fromClient(array $client): ImageMetadata
    {
        $captured = $client['captured_at'] ?? null;

        return new ImageMetadata(
            latitude: isset($client['latitude']) ? (float) $client['latitude'] : null,
            longitude: isset($client['longitude']) ? (float) $client['longitude'] : null,
            gpsAccuracy: isset($client['gps_accuracy']) ? (int) round((float) $client['gps_accuracy']) : null,
            capturedAt: $captured ? $this->parseDate((string) $captured) : null,
            deviceMake: $this->clean($client['device_make'] ?? null),
            deviceModel: $this->clean($client['device_model'] ?? null),
            deviceOs: $this->clean($client['device_os'] ?? null),
            appVersion: $this->clean($client['app_version'] ?? null),
            source: $client === [] ? 'none' : 'client',
        );
    }

    /**
     * EXIF GPS is degrees/minutes/seconds as rational strings ("34/1", "33/1",
     * "11/1"), with the hemisphere in a separate tag. Decimal degrees is what
     * every consumer wants.
     *
     * @param  array<string, mixed>  $gps
     * @return array{latitude: ?float, longitude: ?float}
     */
    private function gpsFromExif(array $gps): array
    {
        if (empty($gps['GPSLatitude']) || empty($gps['GPSLongitude'])) {
            return ['latitude' => null, 'longitude' => null];
        }

        $latitude = $this->dmsToDecimal($gps['GPSLatitude']);
        $longitude = $this->dmsToDecimal($gps['GPSLongitude']);

        if ($latitude === null || $longitude === null) {
            return ['latitude' => null, 'longitude' => null];
        }

        // South and West are negative. Missing the hemisphere tag would put
        // Kabul in the southern ocean, so both are checked explicitly.
        if (strtoupper((string) ($gps['GPSLatitudeRef'] ?? 'N')) === 'S') {
            $latitude = -$latitude;
        }

        if (strtoupper((string) ($gps['GPSLongitudeRef'] ?? 'E')) === 'W') {
            $longitude = -$longitude;
        }

        // A camera with no fix commonly writes 0,0. That is the Atlantic, not a
        // reading, and treating it as one would put pins in the ocean.
        if (abs($latitude) < 0.0001 && abs($longitude) < 0.0001) {
            return ['latitude' => null, 'longitude' => null];
        }

        return ['latitude' => round($latitude, 7), 'longitude' => round($longitude, 7)];
    }

    /** @param array<int, string>|mixed $dms */
    private function dmsToDecimal(mixed $dms): ?float
    {
        if (! is_array($dms) || count($dms) < 3) {
            return null;
        }

        $parts = array_map(fn ($part) => $this->rationalToFloat($part), array_slice($dms, 0, 3));

        if (in_array(null, $parts, true)) {
            return null;
        }

        return $parts[0] + ($parts[1] / 60) + ($parts[2] / 3600);
    }

    /** EXIF numbers arrive as "num/den" strings. */
    private function rationalToFloat(mixed $value): ?float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        if (! is_string($value) || ! str_contains($value, '/')) {
            return null;
        }

        [$numerator, $denominator] = array_pad(explode('/', $value, 2), 2, '1');

        if (! is_numeric($numerator) || ! is_numeric($denominator) || (float) $denominator === 0.0) {
            return null;
        }

        return (float) $numerator / (float) $denominator;
    }

    /** @param array<string, mixed> $exif */
    private function capturedAtFromExif(array $exif): ?Carbon
    {
        // DateTimeOriginal first: DateTime is when the file was last written,
        // which any edit updates. Only the first describes the moment of capture.
        $candidates = [
            $exif['EXIF']['DateTimeOriginal'] ?? null,
            $exif['EXIF']['DateTimeDigitized'] ?? null,
            $exif['IFD0']['DateTime'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (blank($candidate)) {
                continue;
            }

            // EXIF uses "2026:07:27 14:32:10" — colons in the date, which no
            // standard parser accepts without help.
            $normalised = preg_replace('/^(\d{4}):(\d{2}):(\d{2})/', '$1-$2-$3', (string) $candidate);

            if ($parsed = $this->parseDate((string) $normalised)) {
                return $parsed;
            }
        }

        return null;
    }

    private function parseDate(string $value): ?Carbon
    {
        try {
            $date = Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }

        // Cameras with a dead backup battery report 1970 or 2000. A date
        // decades adrift is not a capture time, it is a flat coin cell.
        return $date->year < 2000 || $date->isAfter(now()->addDay()) ? null : $date;
    }

    /**
     * Trim the EXIF block down to something worth storing.
     *
     * @param  array<string, mixed>  $exif
     * @return array<string, mixed>
     */
    private function sanitiseExif(array $exif): array
    {
        $keep = ['FILE', 'COMPUTED', 'IFD0', 'EXIF', 'GPS'];

        $trimmed = array_intersect_key($exif, array_flip($keep));

        // Anything non-UTF8 (maker notes, user comments in odd encodings) makes
        // json_encode fail silently and store null for the whole column.
        array_walk_recursive($trimmed, function (&$value) {
            if (is_string($value) && ! mb_check_encoding($value, 'UTF-8')) {
                $value = null;
            }
        });

        return $trimmed;
    }

    private function clean(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $clean = trim($value);

        return $clean === '' ? null : Str::limit($clean, 100, '');
    }

    /*
    |--------------------------------------------------------------------------
    | Compression and thumbnails
    |--------------------------------------------------------------------------
    */

    /** The compressed viewing copy, or null when the original should be used. */
    private function compress(UploadedFile $file, string $directory, string $disk): ?string
    {
        // Two independent reasons to re-encode, and either is enough.
        //
        // Bytes: a multi-megabyte photo is slow to download.
        //
        // Dimensions: a 4000×3000 image is slow to *decode and paint* even when
        // it happens to compress small — every viewer allocates width × height ×
        // 4 bytes to render it, which is 48MB of memory for that example
        // regardless of what the file weighs. Judging on size alone would serve
        // it at full resolution forever.
        $oversized = false;

        if ($path = $file->getRealPath()) {
            $dimensions = @getimagesize($path);

            $oversized = is_array($dimensions)
                && max($dimensions[0] ?? 0, $dimensions[1] ?? 0) > self::MAX_DIMENSION;
        }

        // Small in both respects: re-encoding would strip EXIF and add loss for
        // nothing, so the original is served directly.
        if (! $oversized && $file->getSize() < self::MIN_BYTES_TO_COMPRESS) {
            return null;
        }

        return $this->render($file, $directory.'/compressed', $disk, self::MAX_DIMENSION, false);
    }

    private function thumbnail(UploadedFile $file, string $directory, string $disk): ?string
    {
        return $this->render($file, $directory.'/thumb', $disk, self::THUMBNAIL_SIZE, true);
    }

    /**
     * Resize an already-stored image to a target width, returning JPEG bytes.
     *
     * The engine behind the on-the-fly thumbnail endpoint. Unlike {@see render()}
     * it reads a file on disk rather than an upload, scales down to `$maxWidth`
     * keeping aspect, and never enlarges a smaller source. Returns null when GD
     * cannot read the format (e.g. HEIC), so the caller can serve the original.
     */
    public function resizeStoredToWidth(string $absolutePath, int $maxWidth): ?string
    {
        if (! extension_loaded('gd') || ! is_readable($absolutePath)) {
            return null;
        }

        $info = @getimagesize($absolutePath);
        if ($info === false) {
            return null;
        }

        $source = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($absolutePath),
            IMAGETYPE_PNG => @imagecreatefrompng($absolutePath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($absolutePath),
            IMAGETYPE_GIF => @imagecreatefromgif($absolutePath),
            default => false,
        };

        if ($source === false) {
            return null;
        }

        try {
            $source = $this->applyOrientation($source, $absolutePath);

            $width = imagesx($source);
            $height = imagesy($source);

            $scale = min(1, $maxWidth / max(1, $width));
            $targetW = max(1, (int) round($width * $scale));
            $targetH = max(1, (int) round($height * $scale));

            $canvas = imagecreatetruecolor($targetW, $targetH);
            imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetW, $targetH, $width, $height);

            ob_start();
            imagejpeg($canvas, null, self::QUALITY);
            $bytes = (string) ob_get_clean();

            imagedestroy($canvas);

            return $bytes;
        } catch (\Throwable $e) {
            Log::warning('[images] Could not resize a stored image.', ['error' => $e->getMessage()]);

            return null;
        } finally {
            imagedestroy($source);
        }
    }

    /**
     * Resize and re-encode with GD.
     *
     * @param  bool  $square  centre-crop to a square, for grid thumbnails
     */
    private function render(
        UploadedFile $file,
        string $directory,
        string $disk,
        int $maxEdge,
        bool $square,
    ): ?string {
        $source = $this->readImage($file);

        if ($source === null) {
            return null;
        }

        try {
            $width = imagesx($source);
            $height = imagesy($source);

            if ($square) {
                // Centre crop first, so the subject is not squashed into a
                // square — a stretched sapling is a useless thumbnail.
                $edge = min($width, $height);
                $srcX = (int) (($width - $edge) / 2);
                $srcY = (int) (($height - $edge) / 2);
                $srcW = $srcH = $edge;
                $targetW = $targetH = min($maxEdge, $edge);
            } else {
                $srcX = $srcY = 0;
                $srcW = $width;
                $srcH = $height;

                $scale = min(1, $maxEdge / max($width, $height));
                $targetW = max(1, (int) round($width * $scale));
                $targetH = max(1, (int) round($height * $scale));
            }

            $canvas = imagecreatetruecolor($targetW, $targetH);

            // A white floor rather than black: photographs of soil and foliage
            // against black look like a rendering fault when a transparent PNG
            // is flattened.
            imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));

            imagecopyresampled($canvas, $source, 0, 0, $srcX, $srcY, $targetW, $targetH, $srcW, $srcH);

            $path = $directory.'/'.Str::random(40).'.jpg';

            ob_start();
            imagejpeg($canvas, null, self::QUALITY);
            $bytes = (string) ob_get_clean();

            imagedestroy($canvas);

            Storage::disk($disk)->put($path, $bytes);

            return $path;
        } catch (\Throwable $e) {
            Log::warning('[images] Could not render a derivative.', ['error' => $e->getMessage()]);

            return null;
        } finally {
            imagedestroy($source);
        }
    }

    /** Decode an upload into a GD resource, honouring EXIF orientation. */
    private function readImage(UploadedFile $file): ?\GdImage
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $path = $file->getRealPath();

        if ($path === false || ! is_readable($path)) {
            return null;
        }

        try {
            $image = match ($file->getMimeType()) {
                'image/jpeg' => @imagecreatefromjpeg($path),
                'image/png' => @imagecreatefrompng($path),
                'image/webp' => @imagecreatefromwebp($path),
                // HEIC is the iPhone default and GD cannot read it. Returning
                // null here means the original is served as-is — correct, since
                // iOS converts to JPEG on upload in most flows anyway.
                default => false,
            };
        } catch (\Throwable) {
            return null;
        }

        if ($image === false) {
            return null;
        }

        return $this->applyOrientation($image, $path);
    }

    /**
     * Rotate to match the EXIF orientation tag.
     *
     * Phones record orientation as metadata rather than rotating the pixels.
     * GD ignores that tag, so without this every portrait photograph would
     * appear sideways in the panel — which looks like a bug in the app and
     * makes a reviewer's job harder than it needs to be.
     */
    private function applyOrientation(\GdImage $image, string $path): \GdImage
    {
        if (! extension_loaded('exif')) {
            return $image;
        }

        $exif = @exif_read_data($path);
        $orientation = $exif['Orientation'] ?? null;

        $degrees = match ($orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($degrees === 0) {
            return $image;
        }

        $rotated = @imagerotate($image, $degrees, 0);

        if ($rotated === false) {
            return $image;
        }

        imagedestroy($image);

        return $rotated;
    }
}
