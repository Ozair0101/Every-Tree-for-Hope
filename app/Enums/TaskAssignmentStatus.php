<?php

namespace App\Enums;

/**
 * One assignee's personal progress on a task.
 *
 * Deliberately separate from {@see TaskStatus}: a task handed to five
 * volunteers has one task-level status but five independent assignment
 * statuses. Volunteer A can be `submitted` while volunteer B has not even
 * accepted. The task-level status is the roll-up of these (see
 * `Task::recalculateStatus()`), never the other way round.
 */
enum TaskAssignmentStatus: string
{
    /** Pushed to the volunteer, not yet acknowledged. */
    case PENDING = 'pending';

    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';
    case IN_PROGRESS = 'in_progress';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    /** Taken off this volunteer and given to someone else. */
    case REASSIGNED = 'reassigned';

    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ACCEPTED => 'Accepted',
            self::DECLINED => 'Declined',
            self::IN_PROGRESS => 'In Progress',
            self::SUBMITTED => 'Submitted',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::REASSIGNED => 'Reassigned',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => '#6b7280',
            self::ACCEPTED => '#3b82f6',
            self::DECLINED => '#dc2626',
            self::IN_PROGRESS => '#0ea5e9',
            self::SUBMITTED => '#8b5cf6',
            self::APPROVED => '#059669',
            self::REJECTED => '#dc2626',
            self::REASSIGNED => '#4b5563',
            self::CANCELLED => '#4b5563',
        };
    }

    /**
     * The states this assignment may legally move to.
     *
     * The per-volunteer counterpart of {@see TaskStatus::allowedTransitions()}.
     * Guarding this matters because the lifecycle timestamps are stamped by the
     * transition: without it a client could submit work that was never started
     * and leave `started_at` null while `submitted_at` is set, quietly
     * corrupting every duration report built on those columns.
     *
     *   Pending ─ accept ─▶ Accepted ─ start ─▶ In Progress ─ submit ─▶ Submitted
     *      │                                        ▲                      │
     *   decline                                   rework                 review
     *      ▼                                        │                      ▼
     *   Declined                              Rejected ◀───────────── Approved
     *
     * Reassigned and Cancelled are reachable from any live state.
     *
     * @return array<int, self>
     */
    public function allowedTransitions(): array
    {
        $offRamps = [self::REASSIGNED, self::CANCELLED];

        return match ($this) {
            self::PENDING => [self::ACCEPTED, self::DECLINED, self::IN_PROGRESS, ...$offRamps],
            // Starting straight from Pending is allowed on purpose: a volunteer
            // who taps "Start" without tapping "Accept" first has plainly
            // accepted, and the app should not make them do it twice.
            self::ACCEPTED => [self::IN_PROGRESS, self::DECLINED, ...$offRamps],
            self::IN_PROGRESS => [self::SUBMITTED, ...$offRamps],
            self::SUBMITTED => [self::APPROVED, self::REJECTED, ...$offRamps],
            // Approving rejected work is an appeal, not a mistake: a reviewer
            // who asked for a revision and then sees the missing photos in a
            // comment must be able to sign it off without making the volunteer
            // stage a pointless resubmission. The earlier verdict survives in
            // `task_reviews`, so the reversal is on the record.
            self::REJECTED => [self::IN_PROGRESS, self::APPROVED, ...$offRamps],
            self::APPROVED, self::DECLINED, self::REASSIGNED, self::CANCELLED => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), strict: true);
    }

    public function isTerminal(): bool
    {
        return $this->allowedTransitions() === [];
    }

    /**
     * This volunteer is still expected to do something. Drives the "My Tasks"
     * badge count in the app and the daily reminder job.
     */
    public function isActive(): bool
    {
        return in_array($this, [self::PENDING, self::ACCEPTED, self::IN_PROGRESS, self::REJECTED], strict: true);
    }

    /** The volunteer is out of the picture — excluded from workload counts. */
    public function isDisengaged(): bool
    {
        return in_array($this, [self::DECLINED, self::REASSIGNED, self::CANCELLED], strict: true);
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
