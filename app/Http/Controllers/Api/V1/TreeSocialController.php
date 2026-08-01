<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\TreeCommentResource;
use App\Http\Resources\Api\V1\TreeResource;
use App\Models\Tree;
use App\Models\TreeComment;
use App\Models\TreeLike;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * The community wall: the feed, and reacting to what is on it.
 *
 * Reading is open to anyone — the point of a plantation programme is that its
 * work is public. Reacting is not: likes, comments and shares all require a
 * signed-in account, enforced by the route middleware rather than by a check
 * repeated in each method.
 */
class TreeSocialController extends ApiController
{
    /**
     * The feed of approved plantings.
     *
     * Only approved trees, newest first. A pending tree is not yet a public
     * record and a rejected one never will be, so neither belongs in a feed
     * other people scroll.
     */
    public function feed(Request $request): AnonymousResourceCollection
    {
        $viewer = $request->user('sanctum');

        $trees = Tree::query()
            ->approved()
            ->with([
                'user:id,name,lastname,profile_image',
                // Only the viewer's own like is loaded, not every like on every
                // post. Twenty posts with three hundred likes each would
                // otherwise pull six thousand rows to answer twenty booleans.
                'likes' => fn ($q) => $viewer
                    ? $q->where('user_id', $viewer->id)
                    : $q->whereRaw('1 = 0'),
            ])
            ->withCount(['likes', 'comments', 'beforeImages', 'afterImages'])
            ->when($request->filled('mine') && $viewer, fn ($q) => $q->where('user_id', $viewer->id))
            ->when($request->filled('planter'), fn ($q) => $q->where('user_id', $request->integer('planter')))
            // "Only posts that show the difference" — the comparison is the
            // thing worth looking at, so it gets its own filter.
            ->when($request->boolean('with_comparison'), fn ($q) => $q->withComparison())
            ->newest()
            ->paginate(min($request->integer('per_page', 10) ?: 10, 30));

        return TreeResource::collection($trees);
    }

    /**
     * Like a tree, or take the like back.
     *
     * A single toggling endpoint rather than a POST/DELETE pair: the client has
     * one button whose meaning depends on current state, and splitting it in
     * two means the app must know that state before it can act — which it does
     * not, on the first render after a cold start.
     */
    public function toggleLike(Request $request, Tree $tree): JsonResponse
    {
        abort_unless($tree->status === 'approved', 404);

        $user = $request->user();
        $existing = $tree->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            try {
                TreeLike::create(['tree_id' => $tree->id, 'user_id' => $user->id]);
            } catch (UniqueConstraintViolationException) {
                // A double-tap on a slow connection sends this twice. The
                // second arrival is not an error — the intent was "liked", and
                // that is already the state.
            }

            $liked = true;
        }

        return $this->ok([
            'is_liked' => $liked,
            'likes_count' => $tree->likes()->count(),
        ]);
    }

    /**
     * Record that a post was shared.
     *
     * There is nothing to store about "opened the OS share sheet" beyond that it
     * happened, so this increments a counter and returns it. Not authenticated
     * state — a share that fails to register is not worth failing the share for.
     */
    public function share(Request $request, Tree $tree): JsonResponse
    {
        abort_unless($tree->status === 'approved', 404);

        $tree->increment('shares_count');

        return $this->ok(['shares_count' => (int) $tree->fresh()->shares_count]);
    }

    /* ══════════════ Comments ══════════════ */

    /**
     * The comment thread.
     *
     * Roots are paginated; replies are eager-loaded whole. That asymmetry is
     * deliberate — a thread with four hundred roots must page, but a root with
     * six replies wants them all at once, because "show 3 more replies" on a
     * six-reply thread is friction for nothing.
     */
    public function comments(Request $request, Tree $tree): AnonymousResourceCollection
    {
        abort_unless($tree->status === 'approved' || $request->user('sanctum')?->id === $tree->user_id, 404);

        $comments = $tree->comments()
            ->visible()
            ->roots()
            ->with([
                'user:id,name,lastname,profile_image',
                'replies' => fn ($q) => $q->visible()->with([
                    'user:id,name,lastname,profile_image',
                    'parent.user:id,name,lastname',
                ]),
            ])
            ->withCount(['replies' => fn ($q) => $q->visible()])
            ->oldest()
            ->paginate(min($request->integer('per_page', 20) ?: 20, 50));

        return TreeCommentResource::collection($comments);
    }

    /** Post a comment, or a reply to one. */
    public function storeComment(Request $request, Tree $tree): JsonResponse
    {
        abort_unless($tree->status === 'approved', 404);

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
            'parent_id' => 'nullable|integer|exists:tree_comments,id',
        ]);

        $replyTo = null;

        if (! empty($validated['parent_id'])) {
            $replyTo = TreeComment::find($validated['parent_id']);

            // A reply must belong to the thread it claims. Without this a
            // client could graft a comment onto another tree's conversation.
            if (! $replyTo || $replyTo->tree_id !== $tree->id) {
                return $this->fail(__('That comment is not on this post.'), status: 422);
            }
        }

        $comment = TreeComment::post($tree, $request->user(), $validated['body'], $replyTo);
        $comment->load(['user:id,name,lastname,profile_image', 'parent.user:id,name,lastname']);

        return $this->created(
            ['comment' => new TreeCommentResource($comment)],
            __('Comment posted.'),
        );
    }

    /**
     * Remove a comment.
     *
     * Soft-deleted, so the replies underneath it survive. Hard-deleting would
     * cascade them away, which reads to everyone else as the replies having
     * been censored along with the comment.
     */
    public function destroyComment(Request $request, TreeComment $comment): JsonResponse
    {
        $comment->loadMissing('tree');

        abort_unless($comment->isRemovableBy($request->user()), 403);

        $comment->delete();

        return $this->ok(null, __('Comment removed.'));
    }
}
