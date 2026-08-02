<?php

namespace App\Services\Trees;

use App\Models\Tree;
use App\Models\TreeImage;
use App\Services\Media\ImageProcessingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Everything that writes to a tree's gallery.
 *
 * One place, because adding a photograph is never just a file write: it has to
 * be processed into three copies, have its EXIF read, have its distance from
 * the tree computed, be given a position in the order, possibly become the
 * cover, and then have that cover mirrored onto the parent row. Spread across
 * two controllers those steps drift apart, and the half that gets forgotten is
 * always the mirroring — which is invisible until the map shows a stale photo.
 */
class TreeGalleryService
{
    public function __construct(private readonly ImageProcessingService $images) {}

    /**
     * Attach photographs to a tree, in one phase.
     *
     * @param  array<int, UploadedFile>  $files
     * @param  int|null  $coverIndex  which of `$files` should represent the phase
     * @param  array<string, mixed>  $clientMetadata  device readings, used only where EXIF is silent
     * @return Collection<int, TreeImage>
     */
    public function attach(
        Tree $tree,
        array $files,
        string $phase = TreeImage::PHASE_BEFORE,
        ?int $coverIndex = null,
        array $clientMetadata = [],
        array $captions = [],
    ): Collection {
        $files = array_values($files);

        if ($files === []) {
            return collect();
        }

        $existing = $tree->images()->newQuery()
            ->where('tree_id', $tree->id)
            ->where('phase', $phase);

        $alreadyHeld = (clone $existing)->count();
        $room = max(0, TreeImage::MAX_PER_PHASE - $alreadyHeld);

        if ($room === 0) {
            throw new \RuntimeException(__('This tree already holds the maximum number of photographs.'));
        }

        // Silently taking the first N would leave the planter believing all ten
        // uploaded. The caller validates the count first; this is the backstop.
        $files = array_slice($files, 0, $room);

        $nextOrder = (int) (clone $existing)->max('sort_order') + 1;
        // The first photograph of a phase always becomes its cover — a phase
        // with images but no cover would render as an empty card.
        $isFirstOfPhase = $alreadyHeld === 0;

        $stored = DB::transaction(function () use (
            $tree, $files, $phase, $clientMetadata, $captions, $nextOrder
        ) {
            $rows = collect();

            foreach ($files as $index => $file) {
                $rows->push($this->storeOne(
                    $tree,
                    $file,
                    $phase,
                    $nextOrder + $index,
                    $clientMetadata,
                    $captions[$index] ?? null,
                ));
            }

            return $rows;
        });

        $this->applyCover($tree, $stored, $phase, $coverIndex, $isFirstOfPhase);

        return $stored;
    }

    /**
     * Choose which of the freshly uploaded images represents the phase.
     *
     * An out-of-range index falls back to the first image rather than throwing:
     * the photographs are already stored and safe, and refusing the whole upload
     * over a bad cover pointer would lose them for nothing.
     */
    private function applyCover(
        Tree $tree,
        Collection $stored,
        string $phase,
        ?int $coverIndex,
        bool $isFirstOfPhase,
    ): void {
        $chosen = $coverIndex !== null ? $stored->get($coverIndex) : null;

        if (! $chosen && $isFirstOfPhase) {
            $chosen = $stored->first();
        }

        if ($chosen) {
            $tree->setCover($chosen);

            return;
        }

        // No new cover, but the parent's mirrored columns may still be stale —
        // this phase might have had none at all before now.
        $tree->syncCover($phase);
    }

    /** Process and record a single photograph. */
    private function storeOne(
        Tree $tree,
        UploadedFile $file,
        string $phase,
        int $order,
        array $clientMetadata,
        ?string $caption,
    ): TreeImage {
        $processed = $this->images->store($file, "trees/{$phase}", 'public', $clientMetadata);

        $metadata = $processed['metadata'];
        $columns = $metadata->toColumns();

        // Clamp the capture time into a window that can actually be true. A
        // phone with a wrong clock would otherwise produce a follow-up
        // photographed before the tree was planted, and a negative growth
        // interval on the comparison screen.
        $columns['captured_at'] = $this->sensibleCaptureTime($tree, $metadata->capturedAt, $phase);

        return $tree->images()->create(array_merge($columns, [
            'phase' => $phase,
            'path' => $processed['paths']['compressed'],
            'thumbnail_path' => $processed['paths']['thumbnail'],
            'original_path' => $processed['paths']['original'],
            'caption' => $caption,
            'sort_order' => $order,
            'is_cover' => false,
            'width' => $metadata->width,
            'height' => $metadata->height,
            'size_bytes' => $processed['sizes']['compressed'] ?? null,
            'distance_meters' => $this->distanceFromTree($tree, $metadata),
        ]));
    }

    private function sensibleCaptureTime(Tree $tree, ?Carbon $captured, string $phase): ?Carbon
    {
        /*
         | No EXIF time — fall back to now.
         |
         | Most gallery photos lose their EXIF to the on-device compression
         | before they ever reach us, so leaving this null would leave
         | `growthDays()` null for nearly every follow-up, and the growth figure
         | is the headline of the whole comparison.
         |
         | This is not a claim that the camera said so: `metadata_source` stays
         | whatever the extractor decided, so a reviewer can still tell a
         | camera-stamped time from an assumed one. The endpoint this replaced
         | did the same.
        */
        if (! $captured) {
            return now();
        }

        if ($captured->isFuture()) {
            return now();
        }

        // Only the follow-up is bounded below by the planting date. A planting
        // photo taken the morning before the record was filed is perfectly
        // ordinary and must not be dragged forward.
        if ($phase === TreeImage::PHASE_AFTER && $tree->planted_on && $captured->lt($tree->planted_on)) {
            return $tree->planted_on->copy();
        }

        return $captured;
    }

    /** How far from the tree the photograph was taken, in metres. */
    private function distanceFromTree(Tree $tree, $metadata): ?int
    {
        if (! $metadata->hasLocation() || $tree->latitude === null || $tree->longitude === null) {
            return null;
        }

        $earthRadius = 6371000;
        $dLat = deg2rad($metadata->latitude - (float) $tree->latitude);
        $dLon = deg2rad($metadata->longitude - (float) $tree->longitude);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad((float) $tree->latitude))
            * cos(deg2rad($metadata->latitude))
            * sin($dLon / 2) ** 2;

        return (int) round($earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    /**
     * Remove a photograph and re-point the cover if it was the one.
     *
     * The last image of a phase may be deleted; the phase simply becomes empty
     * again and the parent's columns are nulled, which correctly returns an
     * approved tree to "awaiting its after photo".
     */
    public function remove(Tree $tree, TreeImage $image): void
    {
        $phase = $image->phase;

        DB::transaction(function () use ($image) {
            $image->purge();
        });

        $tree->syncCover($phase);
    }
}
