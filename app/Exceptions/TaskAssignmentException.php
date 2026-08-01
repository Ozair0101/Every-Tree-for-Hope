<?php

namespace App\Exceptions;

use App\Models\Task;
use App\Models\User;
use RuntimeException;

/**
 * An assignment was refused for a domain reason, not a technical one.
 *
 * A dedicated type so the API layer can map these to a 422 with the message
 * intact, while genuine faults keep bubbling to the 500 handler. Thrown rather
 * than returned because assigning is a command a caller expects to succeed —
 * silently doing nothing would leave a coordinator believing the work went out.
 */
class TaskAssignmentException extends RuntimeException
{
    public static function limitReached(Task $task): self
    {
        $limit = $task->max_assignees;

        return new self(
            $limit === 1
                ? "Task {$task->reference} takes a single assignee and already has one. Reassign it instead."
                : "Task {$task->reference} already has its maximum of {$limit} assignees."
        );
    }

    public static function notOpenForAssignment(Task $task): self
    {
        return new self(
            "Task {$task->reference} is {$task->status->label()} and cannot take new assignees."
        );
    }

    public static function alreadyAssigned(Task $task, User $user): self
    {
        return new self("{$user->name} is already an assignee on task {$task->reference}.");
    }
}
