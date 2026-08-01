<?php

namespace App\Enums;

/**
 * A reviewer's verdict on a submitted attempt.
 *
 * Three outcomes, not two. "Needs revision" is the difference between a system
 * volunteers tolerate and one they abandon: a rejection reads as "your work was
 * bad", while a revision request reads as "nearly — fix this one thing". They
 * drive different pushes, different wording, and a different assignment status.
 */
enum TaskReviewStatus: string
{
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case NEEDS_REVISION = 'needs_revision';

    public function label(): string
    {
        return match ($this) {
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::NEEDS_REVISION => 'Needs Revision',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::APPROVED => '#059669',
            self::REJECTED => '#dc2626',
            self::NEEDS_REVISION => '#f59e0b',
        };
    }

    /** The verdict ends the work. */
    public function isFinal(): bool
    {
        return $this !== self::NEEDS_REVISION;
    }

    /** The volunteer is expected to act again on the same assignment. */
    public function requiresRework(): bool
    {
        return $this === self::NEEDS_REVISION;
    }

    /**
     * What this verdict does to the attempt.
     *
     * A revision request leaves the attempt marked `needs_revision` rather than
     * `rejected`, so the review queue can tell "sent back for a fix" apart from
     * "refused" without reading the comments.
     */
    public function submissionStatus(): TaskSubmissionStatus
    {
        return match ($this) {
            self::APPROVED => TaskSubmissionStatus::APPROVED,
            self::REJECTED => TaskSubmissionStatus::REJECTED,
            self::NEEDS_REVISION => TaskSubmissionStatus::NEEDS_REVISION,
        };
    }

    /**
     * What this verdict does to the volunteer's assignment.
     *
     * Both rejection and revision return the assignment to the volunteer —
     * the task-level machine allows rework from Rejected — but the wording and
     * the push differ, which is exactly why the two verdicts are separate.
     */
    public function assignmentStatus(): TaskAssignmentStatus
    {
        return match ($this) {
            self::APPROVED => TaskAssignmentStatus::APPROVED,
            self::REJECTED, self::NEEDS_REVISION => TaskAssignmentStatus::REJECTED,
        };
    }

    /** The notification event key this verdict emits. */
    public function notificationKey(): string
    {
        return match ($this) {
            self::APPROVED => 'task.approved',
            self::REJECTED => 'task.rejected',
            self::NEEDS_REVISION => 'task.needs_revision',
        };
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
