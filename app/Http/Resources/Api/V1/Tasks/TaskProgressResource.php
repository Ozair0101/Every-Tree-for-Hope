<?php

namespace App\Http\Resources\Api\V1\Tasks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TaskProgress */
class TaskProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'progress_percentage' => (int) $this->progress_percentage,
            'note' => $this->note,
            'location' => $this->hasLocation() ? [
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
                'gps_accuracy' => $this->gps_accuracy,
            ] : null,
            'reported_by' => $this->whenLoaded('creator', fn () => $this->creator ? [
                'id' => $this->creator->id,
                'name' => trim($this->creator->name.' '.($this->creator->lastname ?? '')),
            ] : null),
            'created_at' => $this->created_at?->toIso8601String(),
            'time_ago' => $this->created_at?->diffForHumans(),
        ];
    }
}
