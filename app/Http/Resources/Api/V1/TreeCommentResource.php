<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TreeComment */
class TreeCommentResource extends JsonResource
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
                'name' => $this->whenLoaded('user', fn () => trim(
                    $this->user->name.' '.($this->user->lastname ?? '')
                )),
                'avatar_url' => $this->whenLoaded('user', fn () => $this->user->profile_image
                    ? $request->getSchemeAndHttpHost().'/storage/'.ltrim($this->user->profile_image, '/')
                    : null),
            ],

            // Who this was a reply to, by name. The UI renders two levels and
            // flattens anything deeper, so without this a reply-to-a-reply
            // would read as though it addressed the whole thread.
            'reply_to' => $this->whenLoaded('parent', fn () => $this->parent?->user
                ? trim($this->parent->user->name.' '.($this->parent->user->lastname ?? ''))
                : null),

            'replies' => static::collection($this->whenLoaded('replies')),
            'replies_count' => $this->when(
                $this->replies_count !== null,
                fn () => (int) $this->replies_count,
            ),

            // Computed server-side so the app never has to work out the rules —
            // author, planter and moderator all differ, and duplicating that
            // logic on the client is how the delete button appears where it
            // does not work.
            'can_delete' => $this->isRemovableBy($viewer),

            'created_at' => $this->created_at?->toIso8601String(),
            'time_ago' => $this->created_at?->diffForHumans(),
        ];
    }
}
