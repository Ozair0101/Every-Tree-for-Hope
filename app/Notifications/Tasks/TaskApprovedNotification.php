<?php

namespace App\Notifications\Tasks;

use App\Enums\TaskActivityAction;

/** Sent to the volunteer when their work is signed off. */
class TaskApprovedNotification extends TaskEventNotification
{
    public function __construct($task = null, $assignment = null, public readonly ?string $comments = null)
    {
        parent::__construct($task, $assignment);
    }

    public function eventKey(): string
    {
        return TaskActivityAction::APPROVED->value;
    }

    public function title(object $notifiable): string
    {
        return __('Your work was approved');
    }

    public function body(object $notifiable): string
    {
        // Thanks first. This is the only push in the set that carries good news,
        // and the volunteer should see that before any detail.
        $body = __('Thank you — :task is complete.', ['task' => $this->task?->title ?? __('your task')]);

        return $this->comments ? "{$body} {$this->comments}" : $body;
    }
}
