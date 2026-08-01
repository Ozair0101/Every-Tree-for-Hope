<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TreeUpdate */
class TreeUpdateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $host = $request->getSchemeAndHttpHost();
        $url = fn (?string $path) => $path
            ? $host.'/storage/'.ltrim($path, '/')
            : null;

        // Every photograph on this entry. Falls back to the legacy single
        // column when the relation was not loaded or the row predates galleries,
        // so a progress entry saved either way renders the same.
        $images = $this->relationLoaded('images') && $this->images->isNotEmpty()
            ? $this->images->map(fn ($img) => ['id' => $img->id, 'url' => $url($img->image_path)])->all()
            : ($this->image_path ? [['id' => 0, 'url' => $url($this->image_path)]] : []);

        return [
            'id' => $this->id,
            'note' => $this->note,
            // Kept for older app builds — the first frame, or the legacy single.
            'image_url' => $images[0]['url'] ?? null,
            'images' => $images,
            'height_cm' => $this->height_cm,
            'created_at' => $this->created_at?->toIso8601String(),
            'time_ago' => $this->created_at?->diffForHumans(),
        ];
    }
}
