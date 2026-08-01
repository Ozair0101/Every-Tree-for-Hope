<?php

namespace App\Notifications\Channels;

use App\Jobs\Push\SendPushNotificationJob;
use App\Models\PushNotification;
use App\Models\PushToken;
use App\Models\Task;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

/**
 * Queues a push for every device the user has registered.
 *
 * Registered as the `task-push` channel. It writes rows and dispatches jobs; it
 * never talks to FCM itself. That separation is the point: an FCM outage becomes
 * a queue that drains late rather than a notification that was lost, and the
 * request that triggered the push never waits on a third-party HTTP call.
 *
 * One row per device, so "did Ahmad's phone get it?" has an answer, and each
 * device retries independently of the others.
 */
class TaskPushChannel
{
    /**
     * How recently the same event for the same subject must have gone out
     * before this one is suppressed.
     *
     * Guards against a scheduler that fires twice or a status that flaps —
     * without it, a volunteer's phone buzzes twice for one event.
     */
    private const DUPLICATE_WINDOW_MINUTES = 5;

    public function send(object $notifiable, Notification $notification): void
    {
        if (! $notifiable instanceof User || ! method_exists($notification, 'toPush')) {
            return;
        }

        $payload = $notification->toPush($notifiable);
        $task = $payload['task'] ?? null;

        if ($this->alreadySent($notifiable, $payload['type'], $task)) {
            return;
        }

        $tokens = PushToken::query()
            ->where('user_id', $notifiable->id)
            ->active()
            ->get();

        if ($tokens->isEmpty()) {
            // Recorded rather than dropped silently, so "why did I not get a
            // push?" is answerable from the data instead of guessed at.
            $this->recordSkipped($notifiable, $payload, $task);

            return;
        }

        // The rows are committed before any job is dispatched: a job that runs
        // on another worker before this transaction commits would find nothing.
        $ids = DB::transaction(function () use ($tokens, $notifiable, $payload, $task) {
            return $tokens->map(fn (PushToken $token) => PushNotification::create([
                'user_id' => $notifiable->id,
                'push_token_id' => $token->id,
                'task_notification_id' => $payload['task_notification_id'] ?? null,
                'related_type' => $task ? Task::class : null,
                'related_id' => $task?->id,
                'event_key' => $payload['type'],
                'title' => $payload['title'],
                'body' => $payload['body'],
                'data' => $payload['data'] ?? null,
            ])->id)->all();
        });

        foreach ($ids as $id) {
            SendPushNotificationJob::dispatch($id);
        }
    }

    private function alreadySent(User $user, string $eventKey, ?Task $task): bool
    {
        return $task !== null && PushNotification::recentlySent(
            userId: $user->id,
            eventKey: $eventKey,
            relatedType: Task::class,
            relatedId: $task->id,
            withinMinutes: self::DUPLICATE_WINDOW_MINUTES,
        );
    }

    /** @param array<string, mixed> $payload */
    private function recordSkipped(User $user, array $payload, ?Task $task): void
    {
        PushNotification::create([
            'user_id' => $user->id,
            'task_notification_id' => $payload['task_notification_id'] ?? null,
            'related_type' => $task ? Task::class : null,
            'related_id' => $task?->id,
            'event_key' => $payload['type'],
            'title' => $payload['title'],
            'body' => $payload['body'],
            'status' => PushNotification::STATUS_SKIPPED,
            'error_code' => 'NO_ACTIVE_DEVICE',
        ]);
    }
}
