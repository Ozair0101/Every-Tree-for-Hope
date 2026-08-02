<?php

namespace App\Services\Tasks;

use App\Enums\TaskAssignmentRole;
use App\Enums\TaskReviewStatus;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use App\Notifications\Tasks\TaskApprovedNotification;
use App\Notifications\Tasks\TaskAssignedNotification;
use App\Notifications\Tasks\TaskEventNotification;
use App\Notifications\Tasks\TaskProgressReportedNotification;
use App\Notifications\Tasks\TaskRejectedNotification;
use App\Notifications\Tasks\TaskStartedNotification;
use App\Notifications\Tasks\TaskSubmittedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

/**
 * Decides *who* hears about a task event.
 *
 * The Notification classes own what is said and the channels own how it is
 * delivered; this owns the audience, which is the part that differs per event
 * and is easiest to get subtly wrong — notifying the person who caused the
 * event, or missing the coordinator when a task has no named reviewer.
 *
 * Everything dispatched from here is queued, so a controller never waits on the
 * fan-out. Sending to thirty assignees costs the request nothing.
 */
class TaskNotificationService
{
    /* ══════════════ The six required events ══════════════ */

    /** Admin assigned a task → tell the volunteers. */
    public function taskAssigned(Task $task, iterable $users): int
    {
        return $this->dispatch($users, new TaskAssignedNotification($task));
    }

    /**
     * Volunteer started work → tell the coordinator.
     *
     * Goes to whoever assigned it, falling back to whoever created the task. A
     * "started" notice with nobody to receive it is the common failure here:
     * the assigner may be a coordinator who has since left, so the creator is
     * the backstop.
     */
    public function taskStarted(TaskAssignment $assignment): int
    {
        $task = $assignment->task;

        $recipients = collect([$assignment->assigner, $task->creator])
            ->filter()
            ->unique('id')
            // The volunteer knows they just started; only the office needs telling.
            ->reject(fn (User $user) => $user->id === $assignment->user_id);

        return $this->dispatch($recipients, new TaskStartedNotification($task, $assignment));
    }

    /** Volunteer submitted → tell whoever can review it. */
    /**
     * Tell the people accountable for a task that a volunteer has filed
     * progress on it.
     *
     * Same recipients as a submission — the task's reviewers, falling back to
     * its creator — minus the person reporting, who does not need telling what
     * they just did.
     */
    public function taskProgressReported(
        TaskAssignment $assignment,
        int $percentage,
        ?string $note = null,
    ): int {
        $task = $assignment->task;

        if (! $task) {
            return 0;
        }

        return $this->dispatch(
            $this->reviewersFor($task, except: $assignment->user),
            new TaskProgressReportedNotification($task, $assignment, $percentage, $note),
        );
    }

    public function taskSubmitted(TaskAssignment $assignment): int
    {
        $task = $assignment->task;

        return $this->dispatch(
            $this->reviewersFor($task, except: $assignment->user),
            new TaskSubmittedNotification($task, $assignment),
        );
    }

    /** Admin approved → tell the volunteer. */
    public function taskApproved(TaskAssignment $assignment, ?string $comments = null): int
    {
        return $this->dispatch(
            [$assignment->user],
            new TaskApprovedNotification($assignment->task, $assignment, $comments),
        );
    }

    /** Admin rejected, or asked for a revision → tell the volunteer. */
    public function taskRejected(TaskAssignment $assignment, TaskReviewStatus $status, ?string $comments = null): int
    {
        return $this->dispatch(
            [$assignment->user],
            new TaskRejectedNotification($assignment->task, $assignment, $status, $comments),
        );
    }

    /* ══════════════ Audience helpers ══════════════ */

    /**
     * Whoever may act on a submission.
     *
     * Falls back to the task's creator when nobody is named as reviewer —
     * otherwise submitted work would sit in a queue nobody is watching, which
     * is indistinguishable to the volunteer from being ignored.
     *
     * @return Collection<int, User>
     */
    public function reviewersFor(Task $task, ?User $except = null): Collection
    {
        $reviewers = $task->reviewers()->with('user')->get()->pluck('user')->filter();

        if ($reviewers->isEmpty() && $task->creator) {
            $reviewers = collect([$task->creator]);
        }

        return $reviewers
            ->unique('id')
            ->reject(fn (User $user) => $except && $user->id === $except->id)
            ->values();
    }

    /**
     * Everyone attached to a task, optionally excluding the actor.
     *
     * @param  array<int, TaskAssignmentRole>  $roles
     */
    public function notifyTaskParticipants(
        Task $task,
        TaskEventNotification $notification,
        ?User $except = null,
        array $roles = [TaskAssignmentRole::ASSIGNEE, TaskAssignmentRole::WATCHER],
    ): int {
        $users = $task->assignments()
            ->whereIn('role', array_map(fn (TaskAssignmentRole $r) => $r->value, $roles))
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            // The person who caused the event does not need telling about it.
            ->reject(fn (User $user) => $except && $user->id === $except->id);

        return $this->dispatch($users, $notification);
    }

    /**
     * Send one notification to a set of users.
     *
     * @param  iterable<User>  $users
     * @return int how many recipients it went to
     */
    public function dispatch(iterable $users, TaskEventNotification $notification): int
    {
        $users = collect($users)->filter()->unique('id')->values();

        if ($users->isEmpty()) {
            return 0;
        }

        Notification::send($users, $notification);

        return $users->count();
    }
}
