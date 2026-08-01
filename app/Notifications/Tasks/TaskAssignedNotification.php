<?php

namespace App\Notifications\Tasks;

use App\Enums\TaskActivityAction;

/** Sent to a volunteer when a coordinator hands them a task. */
class TaskAssignedNotification extends TaskEventNotification
{
    public function eventKey(): string
    {
        return TaskActivityAction::ASSIGNED->value;
    }

    public function title(object $notifiable): string
    {
        return __('New task assigned');
    }

    public function body(object $notifiable): string
    {
        $body = $this->task?->title ?? __('You have a new task.');

        // The deadline is the single most useful thing on a lock screen: it is
        // what decides whether the volunteer opens the app now or this evening.
        if ($this->task?->due_date) {
            $body .= ' — '.__('due :when', ['when' => $this->task->due_date->diffForHumans()]);
        }

        return $body;
    }
}
