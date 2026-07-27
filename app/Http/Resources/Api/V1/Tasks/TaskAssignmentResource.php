<?php

namespace App\Http\Resources\Api\V1\Tasks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TaskAssignment */
class TaskAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'role' => [
                'value' => $this->role->value,
                'label' => $this->role->label(),
            ],
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
                'is_active' => $this->status->isActive(),
            ],
            'is_primary' => (bool) $this->is_primary,

            // The lifecycle clock, exactly as stored. The client renders a
            // timeline from these rather than deriving them from the activity
            // log, which would be a second request for data already here.
            'timeline' => [
                'assigned_at' => $this->assigned_at?->toIso8601String(),
                'accepted_at' => $this->accepted_at?->toIso8601String(),
                'declined_at' => $this->declined_at?->toIso8601String(),
                'started_at' => $this->started_at?->toIso8601String(),
                'submitted_at' => $this->submitted_at?->toIso8601String(),
                'completed_at' => $this->completed_at?->toIso8601String(),
            ],
            'duration_minutes' => $this->durationMinutes(),
            'response_minutes' => $this->responseMinutes(),

            'remarks' => $this->remarks,

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => trim($this->user->name.' '.($this->user->lastname ?? '')),
                'avatar_url' => $this->user->profile_image
                    ? $request->getSchemeAndHttpHost().'/storage/'.ltrim($this->user->profile_image, '/')
                    : null,
            ]),

            'assigned_by' => $this->whenLoaded('assigner', fn () => $this->assigner ? [
                'id' => $this->assigner->id,
                'name' => $this->assigner->name,
            ] : null),

            'latest_submission' => new TaskSubmissionResource($this->whenLoaded('latestSubmission')),
            'latest_review' => new TaskReviewResource($this->whenLoaded('latestReview')),

            'submissions_count' => $this->when(isset($this->submissions_count), fn () => (int) $this->submissions_count),
        ];
    }
}
