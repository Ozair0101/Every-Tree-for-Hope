<?php

namespace App\Jobs\Tasks;

use App\Enums\TaskAssignmentRole;
use App\Enums\TaskAssignmentStatus;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Notifications\Tasks\TaskDueTomorrowNotification;
use App\Services\Tasks\TaskNotificationService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * The day-before reminder sweep.
 *
 * Runs once each morning and tells every volunteer with work due tomorrow. A
 * reminder that arrives the morning before is actionable — one that arrives an
 * hour before the deadline is only a reproach.
 *
 * ── Why the window is a whole day, not `= tomorrow` ──────────────────────────
 * "Due tomorrow" is a range, not an instant: a task due at 09:00 and one due at
 * 23:30 are both due tomorrow. The sweep takes everything between tomorrow's
 * start and end of day.
 *
 * ── Not sending twice ────────────────────────────────────────────────────────
 * Two guards, because a scheduler can fire twice and a queue can replay a job.
 * `ShouldBeUnique` stops two copies running at once, and `last_notified_at`
 * stops a second run today reminding anyone who was already told. Without the
 * second guard, a manual re-run would buzz every volunteer again.
 */
class SendDueTomorrowRemindersJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /** Only one sweep at a time, for at most an hour. */
    public int $uniqueFor = 3600;

    /** Rows per chunk. Small enough that a failure re-does little work. */
    private const CHUNK = 200;

    public function __construct()
    {
        $this->onQueue('notifications');
    }

    public function handle(TaskNotificationService $notifications): void
    {
        $from = now()->addDay()->startOfDay();
        $to = now()->addDay()->endOfDay();
        $reminded = 0;

        TaskAssignment::query()
            ->where('role', TaskAssignmentRole::ASSIGNEE->value)
            // Only people who still owe the work. Someone who already submitted
            // does not need reminding, and someone who declined is not on it.
            ->whereIn('status', [
                TaskAssignmentStatus::PENDING->value,
                TaskAssignmentStatus::ACCEPTED->value,
                TaskAssignmentStatus::IN_PROGRESS->value,
                TaskAssignmentStatus::REJECTED->value,
            ])
            // Already reminded today? Leave them alone.
            ->where(fn (Builder $q) => $q
                ->whereNull('last_notified_at')
                ->orWhere('last_notified_at', '<', now()->startOfDay()))
            ->whereHas('task', fn (Builder $q) => $q
                ->whereBetween('due_date', [$from, $to])
                ->whereNotIn('status', [
                    TaskStatus::DRAFT->value,
                    TaskStatus::APPROVED->value,
                    TaskStatus::CANCELLED->value,
                ]))
            ->with(['task', 'user'])
            // chunkById, not chunk: the loop updates `last_notified_at`, which
            // is part of the filter, so offset-based paging would skip rows as
            // the result set shifted under it.
            ->chunkById(self::CHUNK, function ($assignments) use ($notifications, &$reminded) {
                foreach ($assignments as $assignment) {
                    if ($assignment->user === null || $assignment->task === null) {
                        continue;
                    }

                    $notifications->dispatch(
                        [$assignment->user],
                        new TaskDueTomorrowNotification($assignment->task, $assignment),
                    );

                    // Stamped immediately, not at the end: if the job dies
                    // halfway, the people already told stay told.
                    $assignment->forceFill([
                        'last_notified_at' => now(),
                        'reminders_sent' => $assignment->reminders_sent + 1,
                    ])->saveQuietly();

                    $reminded++;
                }
            });

        Log::info('[tasks] Due-tomorrow reminders sent.', [
            'reminded' => $reminded,
            'window' => [$from->toDateTimeString(), $to->toDateTimeString()],
        ]);
    }

    /** One sweep per day, whatever schedules or dispatches it. */
    public function uniqueId(): string
    {
        return 'task-due-reminders:'.now()->toDateString();
    }
}
