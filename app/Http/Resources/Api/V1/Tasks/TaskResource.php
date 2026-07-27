<?php

namespace App\Http\Resources\Api\V1\Tasks;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A task in full, for the detail screen.
 *
 * Carries an `abilities` block so the client can render the right buttons
 * without re-implementing the policy. The server already knows whether this
 * user may submit or review; making the app guess produces buttons that fail on
 * tap, which is worse than no button at all.
 *
 * @mixin Task
 */
class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id' => $this->uuid,
            'reference' => $this->reference,
            'title' => $this->title,
            'description' => $this->description,
            'instructions' => $this->instructions,

            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
                'is_terminal' => $this->status->isTerminal(),
            ],
            'priority' => [
                'value' => $this->priority->value,
                'label' => $this->priority->label(),
                'color' => $this->priority->color(),
            ],

            'start_date' => $this->start_date?->toIso8601String(),
            'due_date' => $this->due_date?->toIso8601String(),
            'is_overdue' => $this->is_overdue,
            'due_in_words' => $this->due_date?->diffForHumans(),
            'completed_at' => $this->completed_at?->toIso8601String(),

            'estimated_hours' => $this->estimated_hours !== null ? (float) $this->estimated_hours : null,
            'actual_hours' => $this->actual_hours !== null ? (float) $this->actual_hours : null,
            'progress' => (int) $this->progress,

            'location' => $this->latitude === null ? null : [
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
                'radius' => $this->radius,
                'name' => $this->location_name,
            ],

            'requirements' => [
                'photo' => (bool) $this->requires_photo,
                'geo_check' => (bool) $this->requires_geo_check,
                'review' => (bool) $this->requires_review,
                'max_assignees' => $this->max_assignees,
            ],

            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'color' => $this->category?->color,
                'icon' => $this->category?->icon,
            ]),

            'event' => $this->whenLoaded('event', fn () => $this->event ? [
                'id' => $this->event->id,
                'title' => $this->event->title,
            ] : null),

            'created_by' => $this->whenLoaded('creator', fn () => $this->creator ? [
                'id' => $this->creator->id,
                'name' => trim($this->creator->name.' '.($this->creator->lastname ?? '')),
            ] : null),

            'assignments' => TaskAssignmentResource::collection($this->whenLoaded('assignments')),
            'checklist' => TaskChecklistItemResource::collection($this->whenLoaded('checklistItems')),
            'attachments' => TaskAttachmentResource::collection($this->whenLoaded('attachments')),

            // Only the unsatisfied ones. A list of dependencies the volunteer
            // has already cleared is noise on a screen they read in the field.
            'blocked_by' => $this->when(
                $this->relationLoaded('dependencies'),
                fn () => $this->blockingDependencies()->map(fn ($dependency) => [
                    'id' => $dependency->dependsOn?->uuid,
                    'reference' => $dependency->dependsOn?->reference,
                    'title' => $dependency->dependsOn?->title,
                    'status' => $dependency->dependsOn?->status->value,
                ])->values(),
            ),

            // The caller's own assignment, so the app knows which buttons apply
            // without scanning the assignments array itself.
            'my_assignment' => $this->when(
                $user && $this->relationLoaded('assignments'),
                fn () => optional(
                    $this->assignments->firstWhere('user_id', $user->id),
                    fn ($assignment) => (new TaskAssignmentResource($assignment))->toArray($request),
                ),
            ),

            'abilities' => $this->when($user !== null, fn () => [
                'update' => $user->can('update', $this->resource),
                'assign' => $user->can('assign', $this->resource),
                'review' => $user->can('review', $this->resource),
                'cancel' => $user->can('cancel', $this->resource),
                'submit' => $user->can('submit', $this->resource),
                'report_progress' => $user->can('reportProgress', $this->resource),
            ]),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
