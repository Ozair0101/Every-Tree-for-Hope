<?php

namespace App\Notifications\Tasks;

use App\Models\NotificationPreference;

/**
 * The day-before reminder.
 *
 * Sent by the scheduled sweep, not by a user action, which is why it is the one
 * notification with no actor behind it.
 */
class TaskDueTomorrowNotification extends TaskEventNotification
{
    public function eventKey(): string
    {
        return NotificationPreference::EVENT_TASK_DUE_SOON;
    }

    public function title(object $notifiable): string
    {
        return __('Task due tomorrow');
    }

    public function body(object $notifiable): string
    {
        $when = $this->task?->due_date?->format('g:ia');

        return __(':task is due tomorrow:at.', [
            'task' => $this->task?->title ?? __('A task'),
            'at' => $when ? __(' at :time', ['time' => $when]) : '',
        ]);
    }
}
