<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\TreeResource;
use App\Http\Resources\Api\V1\TreeUpdateResource;
use App\Models\Tree;
use App\Models\User;
use App\Notifications\TreeSubmitted;
use App\Providers\AuthServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * Notify everyone who can review a tree that a new one has arrived.
     *
     * "Reviewers" are Super Admins (whose access is a Gate bypass, so they hold
     * no explicit permission and must be found by role) plus anyone whose role
     * grants `approve_tree`. The submitting user is excluded — they know they
     * just submitted it.
     */
    protected function notifyModerators(Tree $tree, int $submitterId): void
    {
        $ids = User::role(AuthServiceProvider::SUPER_ADMIN)->pluck('id')
            ->merge(User::permission('approve_tree')->pluck('id'))
            ->unique()
            ->reject(fn ($id) => $id === $submitterId);

        if ($ids->isEmpty()) {
            return;
        }

        $tree->loadMissing('user');

        User::whereIn('id', $ids)->get()->each->notify(new TreeSubmitted($tree));
    }
}
