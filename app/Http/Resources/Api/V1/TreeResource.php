<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Tree */
class TreeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'species' => $this->species,
            'notes' => $this->notes,
            'location_name' => $this->location_name,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'gps_accuracy' => $this->gps_accuracy,
            'planted_on' => $this->planted_on?->toDateString(),
            'image_url' => $this->publicUrl($request, $this->image_path),

            /*
             | The comparison pair.
             |
             | These two are the covers of the `before` and `after` galleries,
             | mirrored onto the parent row by Tree::syncCover(). They are sent
             | flat, and always, so a feed card can render the side-by-side
             | without loading either gallery — twenty posts would otherwise
             | mean twenty gallery loads to draw two thumbnails each.
            */
            'after_image_url' => $this->publicUrl($request, $this->after_image_path),
            'has_comparison' => $this->hasComparison(),
            'growth_days' => $this->growthDays(),
            'accepts_after_images' => $this->acceptsAfterImages(),

            'status' => $this->status,
            // Only meaningful to the owner viewing their own rejected tree.
            'rejection_reason' => $this->when($this->status === 'rejected', $this->rejection_reason),

            'planter_name' => $this->whenLoaded('user', fn () => trim(
                $this->user->name.' '.($this->user->lastname ?? '')
            )),
            'planter_id' => $this->user_id,
            'planter_avatar_url' => $this->whenLoaded('user', fn () => $this->publicUrl(
                $request, $this->user->profile_image,
            )),

            'updates_count' => $this->when($this->updates_count !== null, (int) $this->updates_count),
            'updates' => TreeUpdateResource::collection($this->whenLoaded('updates')),

            // Galleries, only when the caller asked for them. A feed row does
            // not carry every photograph of every post.
            'images' => TreeImageResource::collection($this->whenLoaded('images')),
            'before_images' => TreeImageResource::collection($this->whenLoaded('beforeImages')),
            'after_images' => TreeImageResource::collection($this->whenLoaded('afterImages')),
            'before_images_count' => $this->when(
                $this->before_images_count !== null,
                fn () => (int) $this->before_images_count,
            ),
            'after_images_count' => $this->when(
                $this->after_images_count !== null,
                fn () => (int) $this->after_images_count,
            ),

            /* ── Social ── */
            'likes_count' => (int) ($this->likes_count ?? 0),
            'comments_count' => (int) ($this->comments_count ?? 0),
            'shares_count' => (int) ($this->shares_count ?? 0),
            // Resolved against the bearer token rather than a client guess, so
            // the heart is filled correctly on a fresh install where the app
            // holds no local state.
            'is_liked' => $this->isLikedBy($request->user('sanctum')),
            // Private to the caller — no count accompanies it, because a
            // favourite says something about the reader, not about the tree.
            'is_favourited' => $this->isFavouritedBy($request->user('sanctum')),
            'is_mine' => $request->user('sanctum')?->id === $this->user_id,

            'created_at' => $this->created_at?->toIso8601String(),
            'time_ago' => $this->created_at?->diffForHumans(),
        ];
    }

    /**
     * A storage path as a URL the calling client can actually reach.
     *
     * Built from the host the request arrived on rather than the model's
     * asset() accessor, which resolves against APP_URL. The app reaches the API
     * on a different host per platform, so a single APP_URL cannot be right for
     * all of them. Same approach as UserResource.
     */
    private function publicUrl(Request $request, ?string $path): ?string
    {
        return $path
            ? $request->getSchemeAndHttpHost().'/storage/'.ltrim($path, '/')
            : null;
    }
}
