<?php

namespace App\Http\Resources\Api\V1\Tasks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TaskReview */
class TaskReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'score' => $this->score !== null ? (float) $this->score : null,
            'rating' => $this->rating,
            'comments' => $this->comments,
            'status' => [
                'value' => $this->review_status->value,
                'label' => $this->review_status->label(),
                'color' => $this->review_status->color(),
                'requires_rework' => $this->review_status->requiresRework(),
            ],
            'reviewed_at' => $this->reviewed_at?->toIso8601String(),
            'reviewed_by' => $this->whenLoaded('reviewer', fn () => $this->reviewer ? [
                'id' => $this->reviewer->id,
                'name' => trim($this->reviewer->name.' '.($this->reviewer->lastname ?? '')),
            ] : null),
            'attempt' => $this->whenLoaded('submission', fn () => $this->submission?->attempt),
        ];
    }
}
