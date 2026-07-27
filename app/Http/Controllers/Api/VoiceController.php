<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\VoiceCommentResource;
use App\Http\Resources\VoiceResource;
use App\Models\Voice;
use App\Models\VoiceComment;
use App\Models\VoiceLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON twin of \App\Http\Controllers\VoiceController — the community wall.
 *
 * GET  /api/voices                  — approved voices, filterable
 * GET  /api/voices/categories       — category options for the composer
 * POST /api/voices                  — share a voice (goes to moderation)
 * GET  /api/voices/{voice}          — one voice by slug, with comments
 * POST /api/voices/{voice}/like     — toggle like for this device
 * POST /api/voices/{voice}/comment  — add a comment
 *
 * Identity: the web version fingerprints a visitor with a cookie + session.
 * An API has neither, so the client sends a stable `X-Device-Id` header
 * (generate a UUID once on first launch and persist it). Requests without
 * the header fall back to a hash of the IP, which still works but is shared
 * between devices behind the same network.
 */
class VoiceController extends ApiController
{
    /**
     * The wall — approved voices, newest and featured first.
     *
     * Query parameters: `category`, `q`, `per_page`.
     */
    public function index(Request $request): JsonResponse
    {
        $category = (string) $request->query('category', '');
        $search = trim((string) $request->query('q', ''));

        $query = Voice::query()->approved();

        if ($category !== '' && \array_key_exists($category, Voice::CATEGORIES)) {
            $query->where('category', $category);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        $voices = $query->ordered()
            ->paginate($this->perPage($request, 12))
            ->withQueryString();

        // Tell the resource which of these the caller already liked, so the
        // app renders the heart correctly on first paint.
        VoiceResource::usingLikedIds(
            $this->likedIdsFor($request, $voices->pluck('id')->all())
        );

        return $this->paginated($voices, VoiceResource::class, [
            'filters' => [
                'category' => $category,
                'q' => $search,
            ],
            'stats' => [
                'total' => Voice::approved()->count(),
                'countries' => Voice::approved()->whereNotNull('country')->distinct('country')->count('country'),
                'likes' => (int) Voice::approved()->sum('likes_count'),
            ],
        ]);
    }

    /**
     * Replaces the web "share" form page — the app builds its own form, so
     * all it needs from us is the list of valid categories.
     */
    public function categories(): JsonResponse
    {
        return $this->ok([
            'categories' => collect(Voice::CATEGORIES)
                ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
                ->values()
                ->all(),
        ]);
    }

    /**
     * One voice with its approved comments and an "up next" list.
     *
     * Bumps the view counter once per device (the web version does this
     * once per session).
     */
    public function show(Request $request, Voice $voice): JsonResponse
    {
        abort_unless($voice->status === 'approved', 404);

        $voice->increment('views_count');
        $voice->refresh();

        $comments = $voice->comments()->visible()->latest()->get();

        // More to explore — same category first.
        $related = Voice::query()->approved()
            ->where('id', '!=', $voice->id)
            ->orderByRaw('category = ? desc', [$voice->category])
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        VoiceResource::usingLikedIds($this->likedIdsFor($request, [$voice->id]));

        return $this->ok([
            'voice' => new VoiceResource($voice),
            'comments' => VoiceCommentResource::collection($comments),
            'related' => VoiceResource::collection($related),
        ]);
    }

    /**
     * Share a voice. New posts are held for moderation, so the app should
     * show "waiting for review" rather than expecting it on the wall.
     *
     * Send as multipart/form-data when attaching an `image`.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'author_name' => 'required|string|max:120',
            'author_email' => 'nullable|email|max:255',
            'country' => 'nullable|string|max:120',
            'category' => 'required|in:'.implode(',', array_keys(Voice::CATEGORIES)),
            'title' => 'required|string|max:160',
            'body' => 'required|string|max:8000',
            'image' => 'nullable|image|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('voices', 'public');
        }

        $voice = Voice::create([
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'] ?? null,
            'country' => $validated['country'] ?? null,
            'category' => $validated['category'],
            'title' => $validated['title'],
            'body' => $validated['body'],
            'image_path' => $path,
            'status' => 'pending',
        ]);

        return $this->created([
            'id' => $voice->id,
            'slug' => $voice->slug,
            'status' => $voice->status,
        ], __('messages.voices_submit_success'));
    }

    /**
     * Toggle this device's like. Returns the new state and total.
     */
    public function like(Request $request, Voice $voice): JsonResponse
    {
        abort_unless($voice->status === 'approved', 404);

        $fingerprint = $this->fingerprint($request);

        $existing = VoiceLike::where('voice_id', $voice->id)
            ->where('fingerprint', $fingerprint)
            ->first();

        if ($existing) {
            $existing->delete();
            $voice->decrement('likes_count');
            $liked = false;
        } else {
            VoiceLike::create(['voice_id' => $voice->id, 'fingerprint' => $fingerprint]);
            $voice->increment('likes_count');
            $liked = true;
        }

        return $this->ok([
            'liked' => $liked,
            'count' => (int) $voice->fresh()->likes_count,
        ]);
    }

    /**
     * Add a comment. Comments are published immediately, matching the web.
     */
    public function comment(Request $request, Voice $voice): JsonResponse
    {
        abort_unless($voice->status === 'approved', 404);

        $validated = $request->validate([
            'author_name' => 'required|string|max:120',
            'body' => 'required|string|max:2000',
        ]);

        $comment = VoiceComment::create([
            'voice_id' => $voice->id,
            'author_name' => $validated['author_name'],
            'body' => $validated['body'],
            'status' => 'approved',
        ]);

        $voice->increment('comments_count');

        return $this->created([
            'comment' => new VoiceCommentResource($comment),
            'count' => (int) $voice->fresh()->comments_count,
        ], __('messages.voices_comment_success'));
    }

    /**
     * A stable per-device identifier used to attribute likes.
     */
    protected function fingerprint(Request $request): string
    {
        $deviceId = trim((string) $request->header('X-Device-Id'));

        if ($deviceId !== '') {
            return substr(hash('sha256', 'device|'.$deviceId), 0, 40);
        }

        // No device id sent — degrade to the IP so likes still toggle,
        // accepting that a shared network shares the fingerprint.
        return substr(hash('sha256', 'ip|'.$request->ip()), 0, 40);
    }

    /**
     * Which of the given voice ids this device has liked.
     *
     * @param  array<int>  $voiceIds
     * @return array<int>
     */
    protected function likedIdsFor(Request $request, array $voiceIds): array
    {
        if (empty($voiceIds)) {
            return [];
        }

        return VoiceLike::whereIn('voice_id', $voiceIds)
            ->where('fingerprint', $this->fingerprint($request))
            ->pluck('voice_id')
            ->all();
    }
}
