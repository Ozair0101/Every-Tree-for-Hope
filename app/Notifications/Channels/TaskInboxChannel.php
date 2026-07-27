<?php

namespace App\Notifications\Channels;

use App\Models\TaskNotification;
use App\Models\User;
use Illuminate\Notifications\Notification;

/**
 * Writes a notification into the task module's in-app inbox.
 *
 * Registered as the `task-inbox` channel. Deliberately separate from Laravel's
 * built-in `database` channel, which writes the generic `notifications` table
 * the tree and voice features already use — that table has a different shape
 * and a different reader.
 *
 * This channel always runs, whatever the user's push preferences say. Opting
 * out of a buzz is not opting out of ever being told: the entry is written, and
 * only the delivery to the device is suppressed.
 */
class TaskInboxChannel
{
    public function send(object $notifiable, Notification $notification): ?TaskNotification
    {
        if (! $notifiable instanceof User || ! method_exists($notification, 'toInbox')) {
            return null;
        }

        $payload = $notification->toInbox($notifiable);

        return TaskNotification::create([
            'user_id' => $notifiable->id,
            'type' => $payload['type'],
            'title' => $payload['title'],
            'body' => $payload['body'],
            'task_id' => $payload['task_id'] ?? null,
            'task_assignment_id' => $payload['task_assignment_id'] ?? null,
            'data' => $payload['data'] ?? null,
        ]);
    }
}
