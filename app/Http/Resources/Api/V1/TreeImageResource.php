<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TreeImage */
class TreeImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'phase' => $this->phase,
            // Built from the host the client actually connected on, not the
            // model's asset() accessor — see the note in TreeResource. The app
            // reaches the API on a different host per platform.
            'url' => $this->publicUrl($request, $this->path),
            'thumbnail_url' => $this->publicUrl($request, $this->thumbnail_path ?? $this->path),
            'caption' => $this->caption,
            'is_cover' => (bool) $this->is_cover,
            'sort_order' => (int) $this->sort_order,

            'width' => $this->width,
            'height' => $this->height,
            'captured_at' => $this->captured_at?->toIso8601String(),

            // Provenance, so the app can show the "camera verified" badge and
            // the admin can see how far from the tree a frame was taken.
            'has_location' => $this->hasLocation(),
            'camera_verified' => $this->isCameraVerified(),
            'distance_meters' => $this->distance_meters,
        ];
    }

    private function publicUrl(Request $request, ?string $path): ?string
    {
        return $path
            ? $request->getSchemeAndHttpHost().'/storage/'.ltrim($path, '/')
            : null;
    }
}
