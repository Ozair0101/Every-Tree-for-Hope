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
            // Built from the host the client actually connected on rather than
            // the model's asset() accessor, which resolves against APP_URL. The
            // app reaches the API on a different host per platform, so a single
            // APP_URL cannot be right for all of them. Same approach as
            // UserResource — see the note there.
            'image_url' => $this->image_path
                ? $request->getSchemeAndHttpHost().'/storage/'.ltrim($this->image_path, '/')
                : null,

            'status' => $this->status,
            // Only meaningful to the owner viewing their own rejected tree.
            'rejection_reason' => $this->when($this->status === 'rejected', $this->rejection_reason),

            'planter_name' => $this->whenLoaded('user', fn () => $this->user?->name),
            'updates_count' => $this->when($this->updates_count !== null, (int) $this->updates_count),
            'updates' => TreeUpdateResource::collection($this->whenLoaded('updates')),

            'created_at' => $this->created_at?->toIso8601String(),
            'time_ago' => $this->created_at?->diffForHumans(),
        ];
    }
}
