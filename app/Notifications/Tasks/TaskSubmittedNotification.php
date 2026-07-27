<?php

namespace App\Notifications\Tasks;

use App\Enums\TaskActivityAction;

/** Sent to reviewers when a volunteer turns work in. */
class TaskSubmittedNotification extends TaskEventNotification
{
    public function eventKey(): string
    {
        return TaskActivityAction::SUBMITTED->value;
    }

    public function title(object $notifiable): string
    {
        return __('Work submitted for review');
    }

    public function body(object $notifiable): string
    {
        $who = $this->assignment?->user?->name ?? __('A volunteer');

        return __(':who submitted :task and is waiting for review.', [
            'who' => $who,
            'task' => $this->task?->title ?? __('a task'),
        ]);
    }
}
