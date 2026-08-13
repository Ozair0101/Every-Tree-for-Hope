<?php

namespace App\Http\Resources;

use App\Support\PublicUrl;
use App\Support\Thumb;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Voice */
class VoiceResource extends JsonResource
{
    /**
     * Voice ids the current device has already liked. Set once per request
     * by the controller so the app can render the heart in the right state
     * without a second round-trip.
     *
     * It is static because `VoiceResource::collection()` builds each item
     * itself — there is no per-item hook to pass this through.
     *
     * @var array<int>
     */
    protected static array $likedIds = [];

    /**
     * @param  array<int>  $likedIds
     */
    public static function usingLikedIds(array $likedIds): void
    {
        static::$likedIds = $likedIds;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'body' => $this->body,

            'author_name' => $this->author_name,
            'initials' => $this->author_initials,
            'color' => $this->author_color,
            'country' => $this->country,

            'category' => $this->category,
            'category_label' => $this->category_label,
            // Meaningful on "my voices" (where unapproved posts are visible);
            // always 'approved' on the public wall.
            'status' => $this->status,

            // Request-host origin (not the model's asset() accessor) so the
            // image loads on the same host the app called the API on.
            'image_url' => PublicUrl::for($request, $this->image_path),
            // A small copy for the wall's list cards; the app falls back to
            // `image_url` when this is null.
            'thumbnail_url' => Thumb::url($this->image_path, 400),

            'likes_count' => (int) $this->likes_count,
            'comments_count' => (int) $this->comments_count,
            'views_count' => (int) $this->views_count,
            'is_featured' => (bool) $this->is_featured,
            'has_liked' => in_array($this->id, static::$likedIds, true),

            'comments' => VoiceCommentResource::collection($this->whenLoaded('comments')),

            'created_at' => $this->created_at?->toIso8601String(),
            'time_ago' => $this->created_at?->diffForHumans(),
        ];
    }
}
