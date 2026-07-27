<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TreeUpdate */
class TreeUpdateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'note' => $this->note,
            // Request-host based, for the reason documented in TreeResource.
            'image_url' => $this->image_path
                ? $request->getSchemeAndHttpHost().'/storage/'.ltrim($this->image_path, '/')
                : null,
            'height_cm' => $this->height_cm,
            'created_at' => $this->created_at?->toIso8601String(),
            'time_ago' => $this->created_at?->diffForHumans(),
        ];
    }
}
