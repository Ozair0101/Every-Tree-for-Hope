<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\TreeImageResource;
use App\Http\Resources\Api\V1\TreeResource;
use App\Models\Tree;
use App\Models\TreeImage;
use App\Services\Trees\TreeGalleryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * The photographs on a tree, in both phases.
 *
 * A planting is documented with several frames — the site, the hole, the
 * sapling — and the follow-up months later with several more. One of each set
 * is the cover: the frame that represents that phase wherever the tree appears
 * as a single picture.
 *
 * Every write here belongs to the planter. Moderators approve and reject trees;
 * they do not curate someone else's photographs.
 */
class TreeImageController extends ApiController
{
    public function __construct(private readonly TreeGalleryService $gallery) {}

    /** Both galleries of one tree. */
    public function index(Request $request, Tree $tree): JsonResponse
    {
        $isOwner = $request->user('sanctum')?->id === $tree->user_id;

        abort_unless($tree->status === 'approved' || $isOwner, 404);

        return $this->ok([
            'before' => TreeImageResource::collection($tree->beforeImages()->get()),
            'after' => TreeImageResource::collection($tree->afterImages()->get()),
        ]);
    }

    /**
     * Add follow-up photographs to a tree already recorded.
     *
     * The second half of the record: the same row holds the planting frames and
     * the ones taken a season later, so "which after belongs to which before?"
     * is never a guess.
     *
     * Gated on approval — until the planting has been accepted there is nothing
     * for the follow-up to be compared against.
     */
    public function storeAfter(Request $request, Tree $tree): JsonResponse
    {
        abort_unless($request->user()->id === $tree->user_id, 403);

        if (! $tree->acceptsAfterImages()) {
            return $this->fail(
                __('You can add follow-up photos once this tree has been approved.'),
                status: 422,
            );
        }

        if ($tree->beforeImages()->doesntExist()) {
            return $this->fail(
                __('This tree has no planting photo to compare against.'),
                status: 422,
            );
        }

        return $this->attach($request, $tree, TreeImage::PHASE_AFTER);
    }

    /** Add further planting photographs to a tree the caller recorded. */
    public function storeBefore(Request $request, Tree $tree): JsonResponse
    {
        abort_unless($request->user()->id === $tree->user_id, 403);

        return $this->attach($request, $tree, TreeImage::PHASE_BEFORE);
    }

    /** Shared upload path for both phases. */
    private function attach(Request $request, Tree $tree, string $phase): JsonResponse
    {
        $validated = $request->validate([
            'images' => 'required|array|min:1|max:'.TreeImage::MAX_PER_PHASE,
            'images.*' => 'image|mimes:jpeg,jpg,png,webp,heic|max:8192',
            // Which of the uploaded files should represent the phase. An index
            // into `images`, not an id — the rows do not exist yet.
            'cover_index' => 'nullable|integer|min:0',
            'captions' => 'nullable|array',
            'captions.*' => 'nullable|string|max:500',
            // The device's own readings, used only where EXIF is silent.
            'latitude' => 'nullable|numeric|between:-90,90|required_with:longitude',
            'longitude' => 'nullable|numeric|between:-180,180|required_with:latitude',
            'gps_accuracy' => 'nullable|integer|min:0|max:100000',
            'taken_at' => 'nullable|date',
            'device_make' => 'nullable|string|max:60',
            'device_model' => 'nullable|string|max:80',
            'device_os' => 'nullable|string|max:60',
        ]);

        $held = $tree->images()->newQuery()
            ->where('tree_id', $tree->id)
            ->where('phase', $phase)
            ->count();

        // Checked before storing anything, so a planter is told the limit
        // rather than discovering half their upload was dropped.
        if ($held + count($validated['images']) > TreeImage::MAX_PER_PHASE) {
            return $this->fail(
                __('You can attach up to :max photos here. This tree already has :held.', [
                    'max' => TreeImage::MAX_PER_PHASE,
                    'held' => $held,
                ]),
                status: 422,
            );
        }

        $stored = $this->gallery->attach(
            tree: $tree,
            files: $request->file('images'),
            phase: $phase,
            coverIndex: $validated['cover_index'] ?? null,
            clientMetadata: $validated,
            captions: $validated['captions'] ?? [],
        );

        return $this->created([
            'images' => TreeImageResource::collection($stored),
            'tree' => new TreeResource($tree->fresh()),
        ], __(':count photo(s) added.', ['count' => $stored->count()]));
    }

    /**
     * Choose which photograph represents a phase.
     *
     * The change is mirrored onto the parent row, so the map, the admin panel
     * and every list that shows one picture per tree all follow immediately.
     */
    public function setCover(Request $request, Tree $tree, TreeImage $image): JsonResponse
    {
        abort_unless($request->user()->id === $tree->user_id, 403);
        abort_unless($image->tree_id === $tree->id, 404);

        $tree->setCover($image);

        return $this->ok([
            'image' => new TreeImageResource($image->fresh()),
            'tree' => new TreeResource($tree->fresh()),
        ], __('Cover photo updated.'));
    }

    /**
     * Remove a photograph.
     *
     * Deleting the cover is allowed; the next photograph in order takes its
     * place. Deleting the last one of a phase empties it, which correctly
     * returns an approved tree to "awaiting its follow-up photo".
     */
    public function destroy(Request $request, Tree $tree, TreeImage $image): JsonResponse
    {
        abort_unless($request->user()->id === $tree->user_id, 403);
        abort_unless($image->tree_id === $tree->id, 404);

        // The planting gallery may not be emptied while the record is public —
        // an approved tree with no photograph is a claim with no evidence.
        if ($image->phase === TreeImage::PHASE_BEFORE
            && $tree->status === 'approved'
            && $tree->beforeImages()->count() <= 1) {
            return $this->fail(
                __('An approved tree must keep at least one planting photo.'),
                status: 422,
            );
        }

        $this->gallery->remove($tree, $image);

        return $this->ok(['tree' => new TreeResource($tree->fresh())], __('Photo removed.'));
    }
}
