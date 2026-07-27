<?php

namespace App\Services\Tasks;

use App\Enums\TaskActivityAction;
use App\Enums\TaskAssignmentRole;
use App\Enums\TaskAssignmentStatus;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TaskAttachment;
use App\Models\TaskSubmission;
use App\Models\User;
use App\Notifications\Tasks\GenericTaskNotification;
use App\Services\Tasks\Exceptions\TaskOperationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The volunteer's side of a task: being given it, accepting it, doing it,
 * turning it in.
 *
 * The model owns each individual rule — the assignment cap, the lifecycle
 * transitions. This owns the sequences: submitting is a checklist check, a
 * geofence evaluation, an attempt number, file uploads, a status move and a
 * notification to the reviewer, and half of that happening is worse than none.
 */
class TaskAssignmentService
{
    public function __construct(
        private readonly TaskNotificationService $notifications,
        private readonly GpsVerificationService $gps,
    ) {}

    /**
     * Hand a task to one or more volunteers.
     *
     * @param  array<int, int>  $userIds
     * @return Collection<int, TaskAssignment>
     */
    public function assign(Task $task, array $userIds, User $actor, TaskAssignmentRole $role = TaskAssignmentRole::ASSIGNEE, ?int $primaryUserId = null): Collection
    {
        $users = User::query()->whereIn('id', $userIds)->get();

        if ($users->isEmpty()) {
            throw new TaskOperationException('No valid users were given to assign.', reason: 'no_users');
        }

        $assignments = DB::transaction(function () use ($task, $users, $actor, $role, $primaryUserId) {
            $made = $task->assignMany($users, $actor, $role);

            if ($primaryUserId) {
                $task->assignees()
                    ->where('user_id', $primaryUserId)
                    ->update(['is_primary' => true]);
            }

            return $made;
        });

        if ($role === TaskAssignmentRole::ASSIGNEE) {
            $this->notifications->taskAssigned($task, $users);
        }

        return $assignments;
    }

    public function accept(TaskAssignment $assignment, User $actor): TaskAssignment
    {
        $this->guardOpen($assignment);

        if (! $assignment->accept($actor)) {
            throw TaskOperationException::assignmentClosed($assignment);
        }

        return $assignment->refresh();
    }

    public function decline(TaskAssignment $assignment, User $actor, ?string $reason = null): TaskAssignment
    {
        $this->guardOpen($assignment);

        if (! $assignment->decline($actor, $reason)) {
            throw TaskOperationException::assignmentClosed($assignment);
        }

        // The coordinator needs to know a slot has opened up, and quickly —
        // a declined task with a deadline is now nobody's.
        if ($creator = $assignment->task->creator) {
            $this->notifications->dispatch([$creator], new GenericTaskNotification(
                action: TaskActivityAction::DECLINED,
                title: __('Task declined'),
                body: __(':who declined :task.', ['who' => $actor->name, 'task' => $assignment->task->title])
                    .($reason ? " {$reason}" : ''),
                task: $assignment->task,
                assignment: $assignment,
            ));
        }

        return $assignment->refresh();
    }

    /**
     * Start work.
     *
     * Dependencies are checked here rather than in the model because they are a
     * property of the whole plan, not of the assignment: the volunteer is
     * allowed, but the work is not ready.
     */
    public function start(TaskAssignment $assignment, User $actor): TaskAssignment
    {
        $this->guardOpen($assignment);

        $blocking = $assignment->task->blockingDependencies();

        if ($blocking->isNotEmpty()) {
            throw TaskOperationException::blockedByDependency($assignment->task, $blocking->count());
        }

        if (! $assignment->start($actor)) {
            throw TaskOperationException::assignmentClosed($assignment);
        }

        // Travels upwards, unlike every other notification here. Until someone
        // starts, a coordinator cannot tell an accepted task from an ignored
        // one — this is the first evidence that work handed out days ago is
        // actually happening.
        $this->notifications->taskStarted($assignment);

        return $assignment->refresh();
    }

    /**
     * Turn the work in.
     *
     * @param  array<int, UploadedFile>  $files
     */
    public function submit(
        TaskAssignment $assignment,
        User $actor,
        array $payload = [],
        array $files = [],
        ?string $clientUuid = null,
    ): TaskSubmission {
        $task = $assignment->task;

        // A replayed request returns the original attempt untouched: no second
        // row in the review queue, no second notification to the reviewer.
        if (TaskSubmission::alreadyRecorded($clientUuid)) {
            return TaskSubmission::byClientUuid($clientUuid)->firstOrFail();
        }

        if (! $assignment->role->canSubmit()) {
            throw TaskOperationException::notAssigned($task);
        }

        if (! $assignment->hasCompletedRequiredChecklist()) {
            throw TaskOperationException::checklistIncomplete($assignment);
        }

        if ($task->requires_photo && $files === []) {
            throw TaskOperationException::photoRequired();
        }

        // The geofence is enforced here, not merely recorded. A task that asks
        // for a geo check is asking for proof the volunteer stood on the site,
        // and accepting a submission from twenty kilometres away would make that
        // requirement decorative.
        //
        // The check allows the device's own reported accuracy on top of the
        // radius — see GpsVerificationService for why refusing a ±40m handset
        // standing 20m outside a fence punishes the phone, not the person.
        $radius = $this->gps->checkRadius(
            task: $task,
            latitude: $payload['latitude'] ?? null,
            longitude: $payload['longitude'] ?? null,
            accuracy: $payload['gps_accuracy'] ?? null,
        );

        if (! $radius['allowed']) {
            throw TaskOperationException::outsideGeofence($radius['reason']);
        }

        $capturedAt = ! empty($payload['device_captured_at'])
            ? \Illuminate\Support\Carbon::parse($payload['device_captured_at'])
            : null;

        // Advisory only. Every signal has an innocent explanation, so these are
        // recorded for the reviewer rather than used to refuse the work.
        $findings = $this->gps->findings(
            user: $actor,
            payload: $payload,
            capturedAt: $capturedAt,
            distance: $radius['distance'],
            task: $task,
        );

        $submission = DB::transaction(function () use ($assignment, $task, $actor, $payload, $files, $clientUuid, $findings) {
            $submission = new TaskSubmission([
                'client_uuid' => $clientUuid,
                'task_id' => $task->id,
                'task_assignment_id' => $assignment->id,
                'user_id' => $actor->id,
                'attempt' => $assignment->nextAttemptNumber(),
                'note' => $payload['note'] ?? null,
                'hours_spent' => $payload['hours_spent'] ?? null,
                'latitude' => $payload['latitude'] ?? null,
                'longitude' => $payload['longitude'] ?? null,
                'gps_accuracy' => $payload['gps_accuracy'] ?? null,
                'address' => $payload['address'] ?? null,
                'is_mocked' => $payload['is_mocked'] ?? null,
                'device_captured_at' => $payload['device_captured_at'] ?? null,
                'verification' => $findings ?: null,
            ]);

            // Stamped before the insert so the distance is written once, at the
            // moment the reading was taken.
            $submission->evaluateGeofence($task);
            $submission->save();

            foreach ($files as $file) {
                TaskAttachment::storeFor($submission, $file, $actor, $assignment);
            }

            $assignment->transitionTo(TaskAssignmentStatus::SUBMITTED, $actor);

            // Auto-approval for tasks that need no sign-off — a watering round
            // does not need a desk review, and forcing one only ages the queue.
            if (! $task->requires_review) {
                $assignment->transitionTo(TaskAssignmentStatus::APPROVED, $actor);
            }

            return $submission;
        });

        if ($task->requires_review) {
            $this->notifications->taskSubmitted($assignment);
        }

        return $submission->refresh();
    }

    /** Move a task from one volunteer to another, keeping the trail. */
    public function reassign(Task $task, User $from, User $to, User $actor, ?string $reason = null): TaskAssignment
    {
        $assignment = $task->reassign($from, $to, $actor, $reason);

        $this->notifications->dispatch([$to], new GenericTaskNotification(
            action: TaskActivityAction::REASSIGNED,
            title: __('Task reassigned to you'),
            body: $task->title,
            task: $task,
            assignment: $assignment,
        ));

        return $assignment;
    }

    /** Reject anything already finished or handed on. */
    private function guardOpen(TaskAssignment $assignment): void
    {
        if ($assignment->status->isTerminal()) {
            throw TaskOperationException::assignmentClosed($assignment);
        }
    }
}
