<?php

namespace App\Http\Controllers;

use App\Services\Media\ImageProcessingService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * On-the-fly, cached image thumbnails.
 *
 * List screens (events, findings, upcoming events) show photographs in small
 * cards. Serving the full-resolution originals into them is the single biggest
 * cause of jank on a mid-range phone — each card holds several megabytes of
 * image it renders at a couple of hundred pixels.
 *
 * This resizes on first request and caches the result on the public disk, so
 * every later request — from any device — is a plain static file. It needs no
 * database column and no backfill: it works for every image already uploaded.
 *
 * GET /thumb/{width}/{path}
 */
class ThumbnailController extends Controller
{
    /** The widths the app is allowed to ask for — anything else is refused. */
    private const WIDTHS = [200, 400, 800];

    public function show(int $width, string $path): BinaryFileResponse
    {
        abort_unless(in_array($width, self::WIDTHS, true), 404);

        // Defend against path traversal before touching the filesystem: a
        // request must name a real file inside the public disk, nothing above it.
        if (str_contains($path, '..')) {
            abort(404);
        }

        $path = ltrim($path, '/');
        $disk = Storage::disk('public');

        abort_unless($disk->exists($path), 404);

        // The cached derivative, keyed by width and mirrored under the source's
        // own path so it is easy to find and to purge. Always a .jpg.
        $cacheKey = 'thumbnails/'.$width.'/'.preg_replace('/\.[^.\/]+$/', '.jpg', $path);

        if (! $disk->exists($cacheKey)) {
            try {
                $bytes = app(ImageProcessingService::class)->resizeStoredToWidth($disk->path($path), $width);
            } catch (\Throwable $e) {
                // Any failure in the resize pipeline (no GD extension, an
                // unreadable/HEIC source, out of memory): never 500 on an image
                // that is otherwise fine — the client is showing a real card.
                $bytes = null;
            }

            // Could not resize: serve the original so the card still gets its
            // image. The app prefers this endpoint for list cards, so it must
            // always return a picture when the source exists.
            if ($bytes === null) {
                return response()->file($disk->path($path), self::HEADERS);
            }

            $disk->put($cacheKey, $bytes);
        }

        return response()->file($disk->path($cacheKey), self::HEADERS);
    }

    /** Cache hard: a thumbnail at a path never changes (a new upload = a new path). */
    private const HEADERS = ['Cache-Control' => 'public, max-age=31536000, immutable'];
}
