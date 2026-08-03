<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\TreeResource;
use App\Http\Resources\Api\V1\TreeUpdateResource;
use App\Models\Tree;
use App\Models\TreeImage;
use App\Models\User;
use App\Notifications\TreeReviewed;
use App\Notifications\TreeSubmitted;
use App\Providers\AuthServiceProvider;
use App\Services\Media\ImageProcessingService;
use App\Services\Trees\TreeGalleryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * User-planted trees — the mobile-first field-capture feature.
 *
 * The lifecycle mirrors the Voices wall: a signed-in volunteer records a tree
 * (species, note, photo and GPS), which is held `pending` until an admin
 * approves it. Approved trees surface on the planter's profile, the public
 * list and the map. Planters track a tree's growth by adding dated updates.
 *
 * Public:
 *   GET  /trees            — approved trees, newest first (paginated)
 *   GET  /trees/map        — light markers for the world map
 *   GET  /trees/{tree}     — one tree with its progress updates
 * Authenticated:
 *   GET  /trees/mine       — the caller's own trees (every status)
 *   POST /trees            — record a new tree (multipart, held for review)
 *   POST /trees/{tree}/updates — add a progress entry to your own tree
 */
class TreeController extends ApiController
{
    /** The public gallery — approved trees only. */
    public function index(Request $request): JsonResponse
    {
        $trees = Tree::query()
            ->approved()
            ->with('user')
            ->withCount('updates')
            ->newest()
            ->paginate($this->perPage($request, 12));

        return $this->paginated($trees, TreeResource::class, [
            'stats' => [
                'total' => Tree::approved()->count(),
                'planters' => Tree::approved()->distinct('user_id')->count('user_id'),
            ],
        ]);
    }

    /**
     * Minimal marker data for the map: just what a pin needs. Kept separate
     * from index() so the map can load every approved tree cheaply without the
     * pagination and heavier fields the gallery carries.
     */
    public function map(): JsonResponse
    {
        $markers = Tree::query()
            ->approved()
            ->with('user:id,name')
            ->latest()
            ->limit(500)
            ->get()
            ->map(fn (Tree $tree) => [
                'id' => $tree->id,
                'species' => $tree->species,
                'latitude' => (float) $tree->latitude,
                'longitude' => (float) $tree->longitude,
                'location_name' => $tree->location_name,
                'planter_name' => $tree->user?->name,
                'image_url' => $tree->image_url,
            ]);

        return $this->ok(['markers' => $markers]);
    }

    /** The caller's own trees, every status, so they can see drafts/pending. */
    public function mine(Request $request): JsonResponse
    {
        $trees = Tree::query()
            ->where('user_id', $request->user()->id)
            ->withCount('updates')
            ->newest()
            ->paginate($this->perPage($request, 20));

        return $this->paginated($trees, TreeResource::class, [
            'summary' => [
                'total' => $request->user()->trees()->count(),
                'pending' => $request->user()->trees()->where('status', 'pending')->count(),
                'approved' => $request->user()->trees()->where('status', 'approved')->count(),
                'rejected' => $request->user()->trees()->where('status', 'rejected')->count(),
            ],
        ]);
    }

    /**
     * One tree with its progress log. A pending or rejected tree is visible
     * only to the volunteer who planted it; everyone else gets a 404.
     */
    public function show(Request $request, Tree $tree): JsonResponse
    {
        // This route is public, so resolve the bearer token optionally via the
        // sanctum guard rather than requiring it — an owner viewing their own
        // pending tree is authenticated, an anonymous visitor is not.
        $isOwner = $request->user('sanctum')?->id === $tree->user_id;

        abort_unless($tree->status === 'approved' || $isOwner, 404);

        $tree->load(['user', 'updates.images']);

        return $this->ok(['tree' => new TreeResource($tree)]);
    }

    /**
     * Record a newly planted tree. Held for moderation, so the app should show
     * "waiting for review" rather than expecting it public immediately.
     *
     * Sent as multipart/form-data when a `image` is attached.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // Idempotency key — set by the app before its first attempt so an
            // offline planting replayed on reconnect is recorded exactly once.
            'client_uuid' => 'nullable|uuid',
            'species' => 'required|string|max:160',
            // A field record can be a batch — "40 saplings at this spot" — so a
            // count rides alongside the single GPS point. Defaults to 1.
            'tree_count' => 'nullable|integer|min:1|max:100000000',
            'notes' => 'nullable|string|max:2000',
            'location_name' => 'nullable|string|max:200',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'gps_accuracy' => 'nullable|integer|min:0|max:100000',
            'planted_on' => 'required|date|before_or_equal:today',
            // `images[]` is the current shape — a planting is several frames,
            // not one. `image` is still accepted so an app build that has not
            // updated yet keeps working; both land in the same gallery.
            'images' => 'nullable|array|max:'.TreeImage::MAX_PER_PHASE,
            'images.*' => 'image|mimes:jpeg,jpg,png,webp,heic|max:8192',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,heic|max:8192',
            'cover_index' => 'nullable|integer|min:0',
            'captions' => 'nullable|array',
            'captions.*' => 'nullable|string|max:500',
            'taken_at' => 'nullable|date',
            'device_make' => 'nullable|string|max:60',
            'device_model' => 'nullable|string|max:80',
            'device_os' => 'nullable|string|max:60',
        ]);

        // A planting queued offline is replayed when the signal returns. If this
        // client key already produced a tree, return that one rather than
        // re-attaching its photos and re-notifying the moderators — the app sees
        // success either way, and the review queue stays free of duplicates.
        $clientUuid = $validated['client_uuid'] ?? null;

        if (filled($clientUuid) && $existing = Tree::query()->where('client_uuid', $clientUuid)->first()) {
            return $this->created(
                ['tree' => new TreeResource($existing->fresh()->load(['user', 'beforeImages']))],
                __('This tree was already recorded.'),
            );
        }

        // A planting record with no photograph is a claim, not evidence — the
        // whole feature rests on the picture. At least one frame is required,
        // enforced here (not only in the validator) because a planting may
        // arrive as `images[]` or as the legacy single `image`, and the rule is
        // "at least one, either way".
        $files = array_values($request->file('images') ?? array_filter([$request->file('image')]));

        if ($files === []) {
            return $this->fail(
                __('Add at least one photo of the tree you planted.'),
                ['images' => [__('At least one photo is required.')]],
                422,
            );
        }

        $author = $request->user();
        // Staff record trees on behalf of the programme — at an event, from a
        // field report — and their own submission is not something they should
        // then have to queue up and approve. Anyone who may approve a tree is
        // trusted to publish one directly.
        $autoApprove = $author->can('approve_tree');

        $tree = $author->trees()->create([
            'client_uuid' => $clientUuid,
            'species' => $validated['species'],
            'tree_count' => $validated['tree_count'] ?? 1,
            'notes' => $validated['notes'] ?? null,
            'location_name' => $validated['location_name'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'gps_accuracy' => $validated['gps_accuracy'] ?? null,
            'planted_on' => $validated['planted_on'],
            'status' => $autoApprove ? 'approved' : 'pending',
            'approved_at' => $autoApprove ? now() : null,
        ]);

        if ($files !== []) {
            app(TreeGalleryService::class)->attach(
                tree: $tree,
                files: $files,
                phase: TreeImage::PHASE_BEFORE,
                coverIndex: $validated['cover_index'] ?? null,
                clientMetadata: $validated,
                captions: $validated['captions'] ?? [],
            );
        }

        // Nothing to moderate when it is already published.
        if (! $autoApprove) {
            $this->notifyModerators($tree, $author->id);
        }

        return $this->created(
            ['tree' => new TreeResource($tree->fresh()->load(['user', 'beforeImages']))],
            $autoApprove
                ? __('Your tree was published.')
                : __('Your tree was submitted and is waiting for review.'),
        );
    }

    /**
     * Edit a tree the caller planted.
     *
     * Only the descriptive fields. Coordinates are deliberately not editable:
     * they were captured on site at the moment of planting and are the evidence
     * the whole record rests on — letting them be typed in afterwards would turn
     * a measurement into a claim. A tree in the wrong place is a moderation
     * matter, not a form field.
     *
     * Editing does not send an approved tree back for review. The species name
     * or a note being corrected is not a reason to hide a published record, and
     * treating it as one would teach planters not to fix their own mistakes.
     */
    public function update(Request $request, Tree $tree): JsonResponse
    {
        abort_unless($request->user()->id === $tree->user_id, 403);

        $validated = $request->validate([
            'species' => 'sometimes|required|string|max:160',
            'notes' => 'sometimes|nullable|string|max:2000',
            'location_name' => 'sometimes|nullable|string|max:200',
            'planted_on' => 'sometimes|required|date|before_or_equal:today',
        ]);

        $tree->update($validated);

        return $this->ok(
            ['tree' => new TreeResource($tree->fresh()->load(['user', 'beforeImages', 'afterImages']))],
            __('Your tree was updated.'),
        );
    }

    /**
     * Add a progress entry to a tree the caller planted.
     */
    public function storeUpdate(Request $request, Tree $tree): JsonResponse
    {
        abort_unless($request->user()->id === $tree->user_id, 403);

        $validated = $request->validate([
            'note' => 'required|string|max:2000',
            'height_cm' => 'nullable|integer|min:0|max:20000',
            // `images[]` is the current shape — a progress entry is often several
            // frames (the trunk, the canopy, a ruler against the stem). `image`
            // is still accepted so an app build that has not updated yet keeps
            // working; both land in the same gallery.
            'images' => 'nullable|array|max:'.\App\Models\TreeUpdate::MAX_IMAGES,
            'images.*' => 'image|mimes:jpeg,jpg,png,webp,heic|max:8192',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,heic|max:8192',
        ]);

        $files = $request->file('images') ?? array_filter([$request->file('image')]);

        $update = $tree->updates()->create([
            'note' => $validated['note'],
            'height_cm' => $validated['height_cm'] ?? null,
        ]);

        foreach (array_values($files) as $index => $file) {
            $path = $file->store('tree-updates', 'public');

            $update->images()->create([
                'image_path' => $path,
                'sort_order' => $index,
            ]);

            // Mirror the first frame into the legacy single column so older
            // clients and any code still reading `image_path` keep working.
            if ($index === 0) {
                $update->update(['image_path' => $path]);
            }
        }

        return $this->created(
            ['update' => new TreeUpdateResource($update->load('images'))],
            __('Progress added.'),
        );
    }

    /**
     * Add the "after" photograph to a tree already recorded.
     *
     * The point of the whole feature: the same record holds the planting photo
     * and the one taken a season later, so the pair is unambiguous. A second
     * row would leave "which after belongs to which before?" as a guess.
     *
     * Only the planter may do this, and only once — a second call replaces the
     * photo rather than creating another, because a record has exactly one
     * "now". Ongoing growth belongs in `tree_updates`, which is a log.
     */
    public function storeAfterImage(Request $request, Tree $tree): JsonResponse
    {
        abort_unless($request->user()->id === $tree->user_id, 403);

        // A before photo is the thing being compared against. Without one the
        // "after" is just a photo, and the comparison view has nothing to show.
        if (blank($tree->image_path)) {
            return $this->fail(
                __('This tree has no planting photo to compare against.'),
                status: 422,
            );
        }

        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,webp,heic|max:8192',
            'note' => 'nullable|string|max:2000',
            // The device's own capture time. Trusted for display but clamped
            // below, because a wrong phone clock must not produce a tree that
            // grew before it was planted.
            'taken_at' => 'nullable|date',
            // Where the photographer stood. The tree's own coordinates say where
            // it grows; these say where the follow-up was shot, and a shot taken
            // kilometres away is the single most useful thing to catch here.
            'latitude' => 'nullable|numeric|between:-90,90|required_with:longitude',
            'longitude' => 'nullable|numeric|between:-180,180|required_with:latitude',
            'device_make' => 'nullable|string|max:60',
            'device_model' => 'nullable|string|max:80',
            'device_os' => 'nullable|string|max:60',
        ]);

        $processed = app(ImageProcessingService::class)->store(
            $request->file('image'),
            'tree-after',
            'public',
            $validated,
        );

        $path = $processed['paths']['compressed'];
        $metadata = $processed['metadata'];

        // EXIF wins over the client's reading, for the same reason it does on
        // task photos: the camera wrote it at the moment of capture and the app
        // cannot revise it afterwards.
        $takenAt = $metadata->capturedAt
            ?? (isset($validated['taken_at']) ? Carbon::parse($validated['taken_at']) : now());

        // Clamped into a sane window: never before planting, never in the
        // future. A nonsensical date would make growthDays() negative and the
        // comparison caption absurd.
        if ($tree->planted_on && $takenAt->lt($tree->planted_on)) {
            $takenAt = $tree->planted_on;
        }

        if ($takenAt->isFuture()) {
            $takenAt = now();
        }

        // The previous after-photo is unlinked so replacing one does not leak
        // storage — the same rule TaskAttachment follows.
        $previous = array_filter([$tree->after_image_path, $tree->after_image_thumbnail_path]);

        // How far the photographer was from the tree they are documenting.
        // Computed once, at write time, so the comparison screen never has to.
        $distance = $metadata->hasLocation() && $tree->latitude !== null
            ? (int) round($this->metresBetween(
                (float) $tree->latitude,
                (float) $tree->longitude,
                $metadata->latitude,
                $metadata->longitude,
            ))
            : null;

        $tree->update([
            'after_image_path' => $path,
            'after_image_thumbnail_path' => $processed['paths']['thumbnail'],
            'after_image_note' => $validated['note'] ?? null,
            'after_image_taken_at' => $takenAt,
            'after_image_latitude' => $metadata->latitude,
            'after_image_longitude' => $metadata->longitude,
            'after_image_distance' => $distance,
            'after_image_device' => $metadata->deviceLabel(),
        ]);

        if ($previous !== []) {
            Storage::disk('public')->delete($previous);
        }

        return $this->created(
            ['tree' => new TreeResource($tree->fresh())],
            __('Growth photo added.'),
        );
    }

    /**
     * Great-circle distance in metres.
     *
     * The same haversine used by Task::distanceTo and GpsVerificationService.
     * Duplicated here rather than reached for across a service boundary — this
     * controller has one use for it, and a shared "GeoHelper" that three
     * unrelated things import is its own kind of mess.
     */
    private function metresBetween(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6_371_000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Delete a tree you planted.
     *
     * The planter or a moderator, nobody else. Moderators are included because
     * the alternative to removing an abusive post is leaving it up.
     *
     * Photographs go with it: the rows cascade, but the files on disk do not, so
     * they are removed explicitly. A deleted post whose images stay served is
     * still public to anyone holding the URL.
     */
    public function destroy(Request $request, Tree $tree): JsonResponse
    {
        $isOwner = $request->user()->id === $tree->user_id;

        abort_unless($isOwner || $request->user()->can('delete_any_tree'), 403);

        $paths = $tree->images()->pluck('image_path')
            ->push($tree->image_path)
            ->filter()
            ->all();

        $tree->delete();

        foreach ($paths as $path) {
            // Best effort: the record is already gone, and a file that resists
            // deletion must not turn a successful delete into an error.
            try {
                Storage::disk('public')->delete($path);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $this->ok(null, __('Your tree was deleted.'));
    }

    /**
     * The moderation queue — trees awaiting a decision, oldest first.
     *
     * Oldest first on purpose: this is a work queue, and the tree that has been
     * waiting longest is the one a reviewer should see first. Every other tree
     * listing in this controller is newest-first, because those are feeds.
     */
    public function pending(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('approve_tree'), 403);

        $trees = Tree::query()
            ->where('status', 'pending')
            ->with('user')
            ->withCount('updates')
            ->orderBy('created_at')
            ->paginate($this->perPage($request, 20));

        return $this->paginated($trees, TreeResource::class);
    }

    /** Publish a tree: it becomes visible on the profile, list and map. */
    public function approve(Request $request, Tree $tree): JsonResponse
    {
        abort_unless($request->user()->can('approve_tree'), 403);

        // Clearing the reason matters when a previously rejected tree is being
        // approved on a second look — a stale "blurry photo" note would
        // otherwise sit on an approved record forever.
        $tree->update([
            'status' => 'approved',
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $this->notifyPlanter($tree, 'approved');

        return $this->ok(
            ['tree' => new TreeResource($tree->load('user'))],
            __('Tree approved.'),
        );
    }

    /**
     * Decline a tree, optionally saying why.
     *
     * The reason is optional but strongly worth sending: it is the only thing
     * the planter receives explaining the decision, and "not approved" with no
     * cause reads as arbitrary to someone who walked out to plant it.
     */
    public function reject(Request $request, Tree $tree): JsonResponse
    {
        abort_unless($request->user()->can('reject_tree'), 403);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $tree->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['reason'] ?? null,
        ]);

        $this->notifyPlanter($tree, 'rejected');

        return $this->ok(
            ['tree' => new TreeResource($tree->load('user'))],
            __('Tree rejected.'),
        );
    }

    /**
     * Tell the planter the outcome. Same reasoning as {@see notifyModerators}:
     * the decision is already committed, so a notification failure must not
     * turn a successful review into an error the reviewer will retry.
     */
    protected function notifyPlanter(Tree $tree, string $outcome): void
    {
        try {
            $tree->user?->notify(new TreeReviewed($tree, $outcome));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Notify everyone who can review a tree that a new one has arrived.
     *
     * "Reviewers" are Super Admins (whose access is a Gate bypass, so they hold
     * no explicit permission and must be found by role) plus anyone whose role
     * grants `approve_tree`. The submitting user is excluded — they know they
     * just submitted it.
     */
    protected function notifyModerators(Tree $tree, int $submitterId): void
    {
        /*
         * The tree is already saved by the time we get here, so nothing in this
         * method may be allowed to fail the request. Telling a volunteer their
         * submission failed when it did not is the worst outcome available: they
         * resubmit, and the moderation queue fills with duplicates of a tree
         * that was recorded correctly the first time.
         *
         * `User::permission()` in particular throws PermissionDoesNotExist when
         * the permission is absent from the table — which is exactly the state
         * of a database whose RBAC seeder has not been re-run after a new
         * feature added its permissions. That is an operator problem to fix (run
         * RolesAndPermissionsSeeder), not a reason to reject field data.
         */
        try {
            $ids = User::role(AuthServiceProvider::SUPER_ADMIN)->pluck('id')
                ->merge(User::permission('approve_tree')->pluck('id'))
                ->unique()
                ->reject(fn ($id) => $id === $submitterId);

            if ($ids->isEmpty()) {
                return;
            }

            $tree->loadMissing('user');

            User::whereIn('id', $ids)->get()->each->notify(new TreeSubmitted($tree));
        } catch (\Throwable $e) {
            // Logged, not surfaced: the submission stands, and the tree still
            // appears in the pending queue for anyone who opens it.
            report($e);
        }
    }
}
