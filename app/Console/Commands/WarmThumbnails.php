<?php

namespace App\Console\Commands;

use App\Models\EventImage;
use App\Models\UpcomingEvent;
use App\Models\Voice;
use App\Services\Media\ImageProcessingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Pre-builds the cached thumbnails the app's list cards ask for.
 *
 * The thumbnail endpoint {@see \App\Http\Controllers\ThumbnailController} makes
 * a small copy on first request and caches it. That is fine in the long run,
 * but it means the *first* person to open a screen pays a GD resize for every
 * image at once — on shared hosting that is slow and some images can time out
 * ("it doesn't show all of them"). Running this once after a deploy resizes
 * everything up front, so every real request is a plain static file.
 *
 * Safe to re-run: it skips a thumbnail that already exists.
 *
 *   php artisan thumbnails:warm
 */
class WarmThumbnails extends Command
{
    protected $signature = 'thumbnails:warm {--force : Rebuild thumbnails that already exist}';

    protected $description = 'Pre-generate cached thumbnails for event, voice and upcoming-event images';

    /** The widths the app requests: 400 for feed cards, 800 for posters. */
    private const WIDTHS = [400, 800];

    public function handle(ImageProcessingService $images): int
    {
        $disk = Storage::disk('public');
        $paths = $this->collectPaths();

        $this->info(count($paths).' source image(s) found. Widths: '.implode(', ', self::WIDTHS));

        $made = 0;
        $skipped = 0;
        $missing = 0;
        $failed = 0;

        foreach ($paths as $path) {
            $path = ltrim((string) $path, '/');

            // Externally hosted images (already absolute URLs) and files that
            // are not actually on disk have nothing to resize.
            if ($path === '' || str_starts_with($path, 'http') || ! $disk->exists($path)) {
                $missing++;
                continue;
            }

            foreach (self::WIDTHS as $width) {
                $cacheKey = 'thumbnails/'.$width.'/'.preg_replace('/\.[^.\/]+$/', '.jpg', $path);

                if ($disk->exists($cacheKey) && ! $this->option('force')) {
                    $skipped++;
                    continue;
                }

                $bytes = $images->resizeStoredToWidth($disk->path($path), $width);
                if ($bytes === null) {
                    // GD could not read it (e.g. HEIC); the endpoint serves the
                    // original for these, so there is nothing to cache.
                    $failed++;
                    continue;
                }

                $disk->put($cacheKey, $bytes);
                $made++;
            }
        }

        $this->info("Done. Made: {$made}, skipped (already cached): {$skipped}, sources off-disk: {$missing}, unreadable: {$failed}.");

        return self::SUCCESS;
    }

    /**
     * Every stored image path the app shows in a list card.
     *
     * @return list<string>
     */
    private function collectPaths(): array
    {
        $paths = [];

        foreach (EventImage::query()->pluck('image_path') as $p) {
            $paths[] = $p;
        }

        foreach (Voice::query()->whereNotNull('image_path')->pluck('image_path') as $p) {
            $paths[] = $p;
        }

        // UpcomingEvent.images is a JSON array of paths.
        foreach (UpcomingEvent::query()->pluck('images') as $list) {
            foreach ((array) $list as $p) {
                $paths[] = $p;
            }
        }

        return array_values(array_filter(array_unique($paths)));
    }
}
