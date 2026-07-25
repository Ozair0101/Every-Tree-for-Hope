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
            'image_url' => $this->image_url,

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
