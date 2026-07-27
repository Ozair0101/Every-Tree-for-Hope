<?php

namespace App\Notifications\Tasks;

use App\Models\NotificationPreference;
use App\Models\Task;
use App\Models\TaskAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Base for every task notification.
 *
 * Owns the three things all of them share: which channels to use, how the
 * inbox entry and the push payload relate, and the fact that both are built
 * from the same title and body so they can never drift apart in wording.
 *
 * Queued. Composing a notification means writing rows and dispatching send
 * jobs, and none of that should happen inside the HTTP request that triggered
 * it — a volunteer tapping "Start" waits for their own task to start, not for
 * thirty coordinators to be notified.
 *
 * Subclasses supply `eventKey()`, `title()` and `body()`. Everything else is
 * handled here.
 */
abstract class TaskEventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** Set by the inbox channel so the push rows can point back at the entry. */
    public ?int $taskNotificationId = null;

    public function __construct(
        public readonly ?Task $task = null,
        public readonly ?TaskAssignment $assignment = null,
    ) {
        $this->onQueue('notifications');
    }

    /** The dot-namespaced event key: task.assigned, task.approved, … */
    abstract public function eventKey(): string;

    abstract public function title(object $notifiable): string;

    abstract public function body(object $notifiable): string;

    /**
     * Channels for this notification.
     *
     * The inbox always runs. The push is filtered by the user's own preference:
     * opting out of a buzz must not also delete the record that something
     * happened, which is why the two are separate channels rather than one.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['task-inbox'];

        $allowed = NotificationPreference::allows(
            $notifiable->getKey(),
            NotificationPreference::CHANNEL_PUSH,
            $this->eventKey(),
        );

        if ($allowed) {
            $channels[] = 'task-push';
        }

        return $channels;
    }

    /**
     * The inbox entry.
     *
     * @return array<string, mixed>
     */
    public function toInbox(object $notifiable): array
    {
        return [
            'type' => $this->eventKey(),
            'title' => $this->title($notifiable),
            'body' => $this->body($notifiable),
            'task_id' => $this->task?->id,
            'task_assignment_id' => $this->assignment?->id,
            'data' => $this->deepLink(),
        ];
    }

    /**
     * The push payload.
     *
     * Same title and body as the inbox entry by construction — a push that says
     * something different from what the user finds when they open the app is
     * worse than no push.
     *
     * @return array<string, mixed>
     */
    public function toPush(object $notifiable): array
    {
        return [
            'type' => $this->eventKey(),
            'title' => $this->title($notifiable),
            'body' => $this->body($notifiable),
            'task' => $this->task,
            'task_notification_id' => $this->taskNotificationId,
            'data' => $this->deepLink(),
        ];
    }

    /**
     * What the app needs to open the right screen when the notification is
     * tapped. The UUID, never the integer id — the client only ever sees UUIDs.
     *
     * @return array<string, mixed>
     */
    protected function deepLink(): array
    {
        return array_filter([
            'type' => $this->eventKey(),
            'screen' => 'TaskDetail',
            'task_uuid' => $this->task?->uuid,
            'assignment_id' => $this->assignment?->id,
        ], fn ($value) => $value !== null);
    }

    /** Kept for the generic `database` channel and for tests. */
    public function toArray(object $notifiable): array
    {
        return $this->toInbox($notifiable);
    }
}
