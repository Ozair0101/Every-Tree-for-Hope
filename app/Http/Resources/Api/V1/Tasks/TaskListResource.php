<?php

namespace App\Http\Resources\Api\V1\Tasks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A task as it appears in a list.
 *
 * Deliberately separate from {@see TaskResource}. The list endpoint selects a
 * column subset (`Task::LIST_COLUMNS`) and a full resource would happily read
 * `description` off a model that never loaded it — returning null and teaching
 * the client that the field is empty. Two resources for two shapes is clearer
 * than one resource full of `whenLoaded` guards.
 *
 * `id` is the UUID throughout the API. The integer key is internal and is never
 * exposed: it would leak how many tasks exist and invite walking the range.
 *
 * @mixin \App\Models\Task
 */
class TaskListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'reference' => $this->reference,
            'title' => $this->title,

            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
            ],
            'priority' => [
                'value' => $this->priority->value,
                'label' => $this->priority->label(),
                'color' => $this->priority->color(),
            ],

            'start_date' => $this->start_date?->toIso8601String(),
            'due_date' => $this->due_date?->toIso8601String(),
            // Computed server-side so every client agrees on what "overdue"
            // means, rather than each one comparing against its own clock.
            'is_overdue' => $this->is_overdue,
            'due_in_words' => $this->due_date?->diffForHumans(),

            'progress' => (int) $this->progress,
            'estimated_hours' => $this->estimated_hours !== null ? (float) $this->estimated_hours : null,

            'location' => $this->latitude === null ? null : [
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
                'radius' => $this->radius,
                'name' => $this->location_name,
            ],

            'requires_photo' => (bool) $this->requires_photo,
            'requires_geo_check' => (bool) $this->requires_geo_check,

            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'color' => $this->category->color,
                'icon' => $this->category->icon,
            ]),

            'assignees' => TaskAssigneeStubResource::collection($this->whenLoaded('assignees')),

            'counts' => [
                'assignees' => $this->when(isset($this->assignees_count), fn () => (int) $this->assignees_count),
                'comments' => $this->when(isset($this->comments_count), fn () => (int) $this->comments_count),
                'attachments' => $this->when(isset($this->attachments_count), fn () => (int) $this->attachments_count),
            ],

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
