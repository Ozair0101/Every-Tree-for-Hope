<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\MaintenanceVisit */
class MaintenanceVisitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'visit_date' => $this->visit_date?->toDateString(),

            'trees_checked' => $this->trees_checked,
            'trees_lost' => (int) $this->trees_lost,
            'notes' => $this->notes,

            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'gps_accuracy' => $this->gps_accuracy,
            'address' => $this->address,

            'status' => $this->status,
            // Only meaningful to whoever can see a rejected visit.
            'rejection_reason' => $this->when($this->status === 'rejected', $this->rejection_reason),

            'inspector_name' => $this->whenLoaded('user', fn () => $this->user
                ? trim($this->user->name.' '.($this->user->lastname ?? ''))
                : null),

            'images' => $this->whenLoaded('images', fn () => $this->images
                ->map(fn ($image) => [
                    'id' => $image->id,
                    'url' => $image->image_url,
                ])->values()),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
