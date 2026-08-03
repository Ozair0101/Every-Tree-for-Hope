<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A comment on a finding.
 *
 * Shaped to match TreeCommentResource on purpose. The app renders both walls
 * with the same threaded component, and the cheapest way to guarantee they keep
 * behaving identically is for the two payloads to be the same shape rather than
 * two shapes the client has to reconcile.
 *
 * The legacy fields — `author_name`, `initials`, `color` — are kept alongside
 * the new `author` block because the web views and older app builds still read
 * them, and because an anonymous comment has no account to draw a name from.
 *
 * @mixin \App\Models\VoiceComment
 */
class VoiceCommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $viewer = $request->user('sanctum');

        return [
            'id' => $this->id,
            'body' => $this->body,
            'parent_id' => $this->parent_id,
            'root_id' => $this->root_id,

            'author' => [
                'id' => $this->user_id,
                // Falls back to the stored name, which is all an anonymous
                // comment has — and all a historical one has either.
                'name' => $this->user
                    ? trim($this->user->name.' '.($this->user->lastname ?? ''))
                    : $this->author_name,
                'avatar_url' => $this->user?->profile_image
                    ? $request->getSchemeAndHttpHost().'/storage/'.ltrim($this->user->profile_image, '/')
                    : null,
            ],

            // Who this was a reply to, by name. The UI draws two levels and
            // flattens anything deeper, so without this a reply-to-a-reply
            // would read as addressing the whole thread.
            'reply_to' => $this->whenLoaded('parent', fn () => $this->parent
                ? ($this->parent->user
                    ? trim($this->parent->user->name.' '.($this->parent->user->lastname ?? ''))
                    : $this->parent->author_name)
                : null),

            'replies' => static::collection($this->whenLoaded('replies')),
            'replies_count' => $this->when(
                $this->replies_count !== null,
                fn () => (int) $this->replies_count,
            ),

            // Decided server-side. Author, finding owner and moderator are three
            // different rules, and duplicating them in the client is how a
            // delete button appears where it does not work.
            'can_delete' => $this->isRemovableBy($viewer),

            /* ── Kept for the web views and older app builds ── */
            'author_name' => $this->author_name,
            'initials' => $this->author_initials,
            'color' => $this->author_color,

            'created_at' => $this->created_at?->toIso8601String(),
            'time_ago' => $this->created_at?->diffForHumans(),
        ];
    }
}
