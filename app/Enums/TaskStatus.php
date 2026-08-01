<?php

namespace App\Enums;

/**
 * The lifecycle of a task, and the only legal moves between its states.
 *
 * This enum — not the controllers — owns the state machine. Every write path
 * (admin panel action, mobile API endpoint, scheduled job) funnels through
 * `canTransitionTo()`, so an illegal jump such as Draft → Approved is
 * impossible no matter which client attempts it. Every accepted move is written
 * to `task_status_histories`, giving a complete audit trail.
 *
 *   Draft ─ publish ─▶ Assigned ─ accept ─▶ In Progress ─ submit ─▶ Submitted
 *                                                                      │
 *                                                              take for review
 *                                                                      ▼
 *                          Approved ◀── approve ── Under Review ── reject ──▶ Rejected
 *                                                                              │
 *                                                                    rework ───┘
 *                                                                              ▼
 *                                                                        In Progress
 *
 * Cancelled is reachable from any non-terminal state. Approved and Cancelled
 * are terminal.
 */
enum TaskStatus: string
{
    case DRAFT = 'draft';
    case ASSIGNED = 'assigned';
    case IN_PROGRESS = 'in_progress';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ASSIGNED => 'Assigned',
            self::IN_PROGRESS => 'In Progress',
            self::SUBMITTED => 'Submitted',
            self::UNDER_REVIEW => 'Under Review',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => '#6b7280',
            self::ASSIGNED => '#3b82f6',
            self::IN_PROGRESS => '#0ea5e9',
            self::SUBMITTED => '#8b5cf6',
            self::UNDER_REVIEW => '#f59e0b',
            self::APPROVED => '#059669',
            self::REJECTED => '#dc2626',
            self::CANCELLED => '#4b5563',
        };
    }

    /**
     * The states this one may legally move to.
     *
     * @return array<int, self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::DRAFT => [self::ASSIGNED, self::CANCELLED],
            self::ASSIGNED => [self::IN_PROGRESS, self::DRAFT, self::CANCELLED],
            self::IN_PROGRESS => [self::SUBMITTED, self::ASSIGNED, self::CANCELLED],
            self::SUBMITTED => [self::UNDER_REVIEW, self::IN_PROGRESS, self::CANCELLED],
            self::UNDER_REVIEW => [self::APPROVED, self::REJECTED, self::IN_PROGRESS, self::CANCELLED],
            // A rejection is not the end of the road. Two ways back: rework,
            // which returns it to the assignee, or a fresh look — a reviewer who
            // sees the missing evidence in a comment can reopen the review and
            // approve without making the volunteer stage a pointless
            // resubmission. Both earlier verdicts survive in `task_reviews`.
            self::REJECTED => [self::IN_PROGRESS, self::UNDER_REVIEW, self::CANCELLED],
            self::APPROVED, self::CANCELLED => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), strict: true);
    }

    /** No further movement is possible from here. */
    public function isTerminal(): bool
    {
        return $this->allowedTransitions() === [];
    }

    /** The task is live work someone is expected to act on. */
    public function isOpen(): bool
    {
        return in_array($this, [self::ASSIGNED, self::IN_PROGRESS, self::REJECTED], strict: true);
    }

    /** The task is sitting in a reviewer's queue. */
    public function awaitsReview(): bool
    {
        return in_array($this, [self::SUBMITTED, self::UNDER_REVIEW], strict: true);
    }

    /**
     * Whether a task in this state should still be counted as overdue when its
     * due date passes. Finished and abandoned work never goes overdue.
     */
    public function countsTowardsOverdue(): bool
    {
        return ! $this->isTerminal() && $this !== self::DRAFT;
    }

    /** Statuses the mobile app shows a volunteer under "My Tasks". */
    public static function visibleToAssignee(): array
    {
        return array_map(
            fn (self $case) => $case->value,
            [self::ASSIGNED, self::IN_PROGRESS, self::SUBMITTED, self::UNDER_REVIEW, self::APPROVED, self::REJECTED],
        );
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
