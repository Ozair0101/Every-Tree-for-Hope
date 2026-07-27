<?php

namespace App\Services\Tasks\Exceptions;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\TaskAssignment;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * A task operation was refused for a business reason.
 *
 * Carries its own HTTP status so the exception handler needs no `instanceof`
 * ladder, and a message written for the person who will read it in the app
 * rather than for a log file. Genuine faults are deliberately NOT wrapped in
 * this — they keep bubbling to the 500 handler where they belong.
 */
class TaskOperationException extends RuntimeException implements HttpExceptionInterface
{
    public function __construct(
        string $message,
        private readonly int $status = 422,
        private readonly string $reason = 'operation_refused',
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    /** @return array<string, string> */
    public function getHeaders(): array
    {
        return [];
    }

    /** Machine-readable, so the app can branch without parsing English. */
    public function reason(): string
    {
        return $this->reason;
    }

    public static function illegalTransition(Task $task, TaskStatus $target): self
    {
        return new self(
            "Task {$task->reference} is {$task->status->label()} and cannot move to {$target->label()}.",
            reason: 'illegal_transition',
        );
    }

    public static function closed(Task $task): self
    {
        return new self(
            "Task {$task->reference} is {$task->status->label()} and can no longer be edited.",
            reason: 'task_closed',
        );
    }

    public static function hasSubmissions(Task $task): self
    {
        return new self(
            "Task {$task->reference} has submitted work and cannot be deleted. Cancel it instead.",
            reason: 'has_submissions',
        );
    }

    public static function notAssigned(Task $task): self
    {
        return new self(
            "You are not assigned to task {$task->reference}.",
            status: 403,
            reason: 'not_assigned',
        );
    }

    public static function checklistIncomplete(TaskAssignment $assignment): self
    {
        return new self(
            'Every required checklist step must be completed before submitting.',
            reason: 'checklist_incomplete',
        );
    }

    public static function photoRequired(): self
    {
        return new self(
            'This task requires at least one photo with the submission.',
            reason: 'photo_required',
        );
    }

    /**
     * The volunteer is not where the task is.
     *
     * The message is composed by GpsVerificationService because it knows the
     * actual distance and radius — a generic "you are too far away" leaves the
     * volunteer with no idea whether to walk ten metres or ten kilometres.
     */
    public static function outsideGeofence(string $message): self
    {
        return new self($message, reason: 'outside_geofence');
    }

    public static function blockedByDependency(Task $task, int $count): self
    {
        return new self(
            "Task {$task->reference} is waiting on {$count} unfinished ".($count === 1 ? 'task' : 'tasks').'.',
            reason: 'blocked_by_dependency',
        );
    }

    public static function nothingToReview(Task $task): self
    {
        return new self(
            "There is no submitted work to review on task {$task->reference}.",
            reason: 'nothing_to_review',
        );
    }

    public static function assignmentClosed(TaskAssignment $assignment): self
    {
        return new self(
            "This assignment is {$assignment->status->label()} and no longer accepts changes.",
            reason: 'assignment_closed',
        );
    }
}
