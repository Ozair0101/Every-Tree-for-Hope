<?php

namespace App\Http\Controllers;

use App\Models\Voice;
use App\Models\VoiceComment;
use App\Models\VoiceLike;
use Illuminate\Http\Request;

class VoiceController extends Controller
{
    /**
     * The community wall — a stream of approved "voices" with optional
     * category filter and keyword search.
     */
    public function index(Request $request)
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

        $voices = $query->ordered()->paginate(12)->withQueryString();

        $stats = [
            'total' => Voice::approved()->count(),
            'countries' => Voice::approved()->whereNotNull('country')->distinct('country')->count('country'),
            'likes' => (int) Voice::approved()->sum('likes_count'),
        ];

        return view('pages.voices', [
            'voices' => $voices,
            'category' => $category,
            'search' => $search,
            'categories' => Voice::CATEGORIES,
            'stats' => $stats,
            // Which of the voices on this page the current visitor has already liked.
            'likedIds' => $this->likedIdsFor($request, $voices->pluck('id')->all()),
        ]);
    }

    /**
     * Dedicated full page for sharing a new voice (replaces the old modal).
     */
    public function create()
    {
        return view('pages.voice-create', [
            'categories' => Voice::CATEGORIES,
        ]);
    }

    /**
     * A single voice — laid out like a video watch page.
     */
    public function show(Request $request, Voice $voice)
    {
        abort_unless($voice->status === 'approved', 404);

        // Cheap, non-blocking view counter (one bump per session per voice).
        $viewedKey = 'voice_viewed_' . $voice->id;
        if (! $request->session()->has($viewedKey)) {
            $voice->increment('views_count');
            $request->session()->put($viewedKey, true);
        }

        $comments = $voice->comments()->visible()->latest()->get();

        // "Up next" — more voices to explore, same category first.
        $related = Voice::query()->approved()
            ->where('id', '!=', $voice->id)
            ->orderByRaw('category = ? desc', [$voice->category])
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        return view('pages.voice-show', [
            'voice' => $voice,
            'comments' => $comments,
            'related' => $related,
            'hasLiked' => $this->hasLiked($request, $voice),
        ]);
    }

    /**
     * Anyone can share a finding. New posts wait for moderation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'author_name' => 'required|string|max:120',
            'author_email' => 'nullable|email|max:255',
            'country' => 'nullable|string|max:120',
            'category' => 'required|in:' . implode(',', array_keys(Voice::CATEGORIES)),
            'title' => 'required|string|max:160',
            'body' => 'required|string|max:8000',
            'image' => 'nullable|image|max:5120',
            'website' => 'nullable|max:0', // honeypot: bots fill it, humans never see it
        ], [
            'website.max' => 'Submission rejected.',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('voices', 'public');
        }

        Voice::create([
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'] ?? null,
            'country' => $validated['country'] ?? null,
            'category' => $validated['category'],
            'title' => $validated['title'],
            'body' => $validated['body'],
            'image_path' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('voices.index')
            ->with('voice_submitted', true)
            ->with('success', __('messages.voices_submit_success'));
    }

    /**
     * Toggle a like for the current visitor. Returns JSON for the AJAX button.
     */
    public function like(Request $request, Voice $voice)
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

        $count = (int) $voice->fresh()->likes_count;

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['liked' => $liked, 'count' => $count]);
        }

        return back();
    }

    /**
     * Add a comment to a voice.
     */
    public function comment(Request $request, Voice $voice)
    {
        abort_unless($voice->status === 'approved', 404);

        $validated = $request->validate([
            'author_name' => 'required|string|max:120',
            'body' => 'required|string|max:2000',
            'website' => 'nullable|max:0', // honeypot
        ]);

        $comment = VoiceComment::create([
            'voice_id' => $voice->id,
            'author_name' => $validated['author_name'],
            'body' => $validated['body'],
            'status' => 'approved',
        ]);

        $voice->increment('comments_count');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'count' => (int) $voice->fresh()->comments_count,
                'comment' => [
                    'author_name' => $comment->author_name,
                    'initials' => $comment->author_initials,
                    'color' => $comment->author_color,
                    'body' => $comment->body,
                    'time' => $comment->created_at->diffForHumans(),
                ],
            ]);
        }

        return back()->with('success', __('messages.voices_comment_success'))->withFragment('comments');
    }

    /**
     * Stable per-visitor fingerprint, persisted in a long-lived cookie so a
     * like survives across sessions without requiring an account.
     */
    protected function fingerprint(Request $request): string
    {
        $cookie = $request->cookie('voice_fp');

        if ($cookie) {
            return $cookie;
        }

        $fp = substr(hash('sha256', $request->session()->getId() . '|' . $request->ip()), 0, 40);

        // Keep it for ~1 year.
        cookie()->queue(cookie('voice_fp', $fp, 60 * 24 * 365));

        return $fp;
    }

    protected function hasLiked(Request $request, Voice $voice): bool
    {
        $cookie = $request->cookie('voice_fp');
        if (! $cookie) {
            return false;
        }

        return VoiceLike::where('voice_id', $voice->id)
            ->where('fingerprint', $cookie)
            ->exists();
    }

    /**
     * Which of the given voice ids the current visitor has liked.
     *
     * @param  array<int>  $voiceIds
     * @return array<int>
     */
    protected function likedIdsFor(Request $request, array $voiceIds): array
    {
        $cookie = $request->cookie('voice_fp');
        if (! $cookie || empty($voiceIds)) {
            return [];
        }

        return VoiceLike::whereIn('voice_id', $voiceIds)
            ->where('fingerprint', $cookie)
            ->pluck('voice_id')
            ->all();
    }
}
