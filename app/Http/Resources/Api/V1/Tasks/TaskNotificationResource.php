<?php

namespace App\Http\Resources\Api\V1\Tasks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TaskNotification */
class TaskNotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'body' => $this->body,
            'is_read' => (bool) $this->is_read,
            'read_at' => $this->read_at?->toIso8601String(),

            // Everything the app needs to deep-link on tap. Null `task` means
            // the task was purged — the client hides the "open" button rather
            // than navigating to a 404.
            'task' => $this->whenLoaded('task', fn () => $this->task ? [
                'id' => $this->task->uuid,
                'reference' => $this->task->reference,
                'title' => $this->task->title,
                'status' => $this->task->status->value,
            ] : null),

            'data' => $this->data,

            'created_at' => $this->created_at?->toIso8601String(),
            'time_ago' => $this->created_at?->diffForHumans(),
        ];
    }
}
