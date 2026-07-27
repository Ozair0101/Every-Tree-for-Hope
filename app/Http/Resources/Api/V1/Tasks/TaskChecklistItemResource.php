<?php

namespace App\Http\Resources\Api\V1\Tasks;

use App\Models\TaskAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A checklist step, with whether *this* caller has ticked it.
 *
 * `is_completed` is per-assignment, not per-task: a team task tracks each
 * volunteer's own progress through the same steps, so the answer depends on who
 * is asking. The assignment is passed in via `additional()` from the controller
 * rather than looked up here, which would be a query per step.
 *
 * @mixin \App\Models\TaskChecklistItem
 */
class TaskChecklistItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var TaskAssignment|null $assignment */
        $assignment = $this->additional['assignment'] ?? null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'is_required' => (bool) $this->is_required,
            'requires_photo' => (bool) $this->requires_photo,
            'position' => (int) $this->position,

            'is_completed' => $assignment
                ? $this->completions->contains('task_assignment_id', $assignment->id)
                : null,
        ];
    }
}
