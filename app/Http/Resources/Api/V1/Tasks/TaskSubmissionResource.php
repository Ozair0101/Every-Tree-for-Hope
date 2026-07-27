<?php

namespace App\Http\Resources\Api\V1\Tasks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TaskSubmission */
class TaskSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'attempt' => (int) $this->attempt,
            'note' => $this->note,
            'hours_spent' => $this->hours_spent !== null ? (float) $this->hours_spent : null,

            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
            ],

            // The proof-of-presence block. `is_within_geofence` is null when the
            // task had no geofence at all — which is different from "outside
            // it", and the client must not render those the same way.
            'location' => $this->latitude === null ? null : [
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
                'gps_accuracy' => $this->gps_accuracy,
                'distance_meters' => $this->distance_meters,
                'is_within_geofence' => $this->is_within_geofence,
            ],
            'device_captured_at' => $this->device_captured_at?->toIso8601String(),

            'attachments' => TaskAttachmentResource::collection($this->whenLoaded('attachments')),
            'reviews' => TaskReviewResource::collection($this->whenLoaded('reviews')),

            'submitted_by' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => trim($this->user->name.' '.($this->user->lastname ?? '')),
            ]),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
