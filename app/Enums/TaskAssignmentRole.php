<?php

namespace App\Enums;

/**
 * Why a user is attached to a task.
 *
 * One join table (`task_assignments`) carries all three roles rather than three
 * separate pivots — the lifecycle columns (accepted_at, started_at, …) and the
 * push-notification fan-out are identical for each, and a single table keeps
 * "everyone connected to this task" a one-query answer.
 */
enum TaskAssignmentRole: string
{
    /** Does the work and submits it. Only assignees may create submissions. */
    case ASSIGNEE = 'assignee';

    /** Approves or rejects the submission. Never counts toward `max_assignees`. */
    case REVIEWER = 'reviewer';

    /** Read-only follower — receives pushes, cannot act. */
    case WATCHER = 'watcher';

    public function label(): string
    {
        return match ($this) {
            self::ASSIGNEE => 'Assignee',
            self::REVIEWER => 'Reviewer',
            self::WATCHER => 'Watcher',
        };
    }

    /** Roles whose members are permitted to submit work. */
    public function canSubmit(): bool
    {
        return $this === self::ASSIGNEE;
    }

    /** Roles whose members are permitted to approve or reject a submission. */
    public function canReview(): bool
    {
        return $this === self::REVIEWER;
    }

    /** @return array<string, string> value => label, for select inputs. */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
