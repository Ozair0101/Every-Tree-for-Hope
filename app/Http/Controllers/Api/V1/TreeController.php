<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\TreeResource;
use App\Http\Resources\Api\V1\TreeUpdateResource;
use App\Models\Tree;
use App\Models\User;
use App\Notifications\TreeReviewed;
use App\Notifications\TreeSubmitted;
use App\Providers\AuthServiceProvider;
use App\Services\Media\ImageProcessingService;
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

        $tree->load(['user', 'updates']);

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
            'species' => 'required|string|max:160',
            'notes' => 'nullable|string|max:2000',
            'location_name' => 'nullable|string|max:200',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'gps_accuracy' => 'nullable|integer|min:0|max:100000',
            'planted_on' => 'required|date|before_or_equal:today',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:8192',
        ]);

        $path = $request->hasFile('image')
            ? $request->file('image')->store('trees', 'public')
            : null;

        $tree = $request->user()->trees()->create([
            'species' => $validated['species'],
            'notes' => $validated['notes'] ?? null,
            'location_name' => $validated['location_name'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'gps_accuracy' => $validated['gps_accuracy'] ?? null,
            'planted_on' => $validated['planted_on'],
            'image_path' => $path,
            'status' => 'pending',
        ]);

        $this->notifyModerators($tree, $request->user()->id);

        return $this->created(
            ['tree' => new TreeResource($tree)],
            __('Your tree was submitted and is waiting for review.'),
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
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:8192',
        ]);

        $path = $request->hasFile('image')
            ? $request->file('image')->store('tree-updates', 'public')
            : null;

        $update = $tree->updates()->create([
            'note' => $validated['note'],
            'height_cm' => $validated['height_cm'] ?? null,
            'image_path' => $path,
        ]);

        return $this->created(
            ['update' => new TreeUpdateResource($update)],
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
