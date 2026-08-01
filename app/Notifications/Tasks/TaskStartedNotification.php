<?php

namespace App\Notifications\Tasks;

use App\Enums\TaskActivityAction;

/**
 * Sent to the coordinator when a volunteer starts work.
 *
 * The one notification in the set that travels upwards. It matters because it
 * is the first confirmation that a task handed out days ago is actually being
 * done — until someone starts, a coordinator cannot tell an accepted task from
 * an ignored one.
 */
class TaskStartedNotification extends TaskEventNotification
{
    public function eventKey(): string
    {
        return TaskActivityAction::STARTED->value;
    }

    public function title(object $notifiable): string
    {
        return __('Work started');
    }

    public function body(object $notifiable): string
    {
        $who = $this->assignment?->user?->name ?? __('A volunteer');

        return __(':who started :task.', [
            'who' => $who,
            'task' => $this->task?->title ?? __('a task'),
        ]);
    }
}
