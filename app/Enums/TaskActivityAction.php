<?php

namespace App\Enums;

/**
 * Every kind of action recorded in `task_activity_logs`.
 *
 * Dot-namespaced strings rather than bare words, so the values read the same in
 * the log table, the notification `type` column and the FCM payload — one
 * vocabulary across the whole module, greppable end to end.
 *
 * The seven the requirements name are all here (created, updated, assigned,
 * started, submitted, approved, rejected); the rest exist because they are the
 * events a coordinator asks about afterwards — who declined, who reassigned it,
 * why it was cancelled.
 */
enum TaskActivityAction: string
{
    // ── The seven required ───────────────────────────────────────────────
    case CREATED = 'task.created';
    case UPDATED = 'task.updated';
    case ASSIGNED = 'task.assigned';
    case STARTED = 'task.started';
    case SUBMITTED = 'task.submitted';
    case APPROVED = 'task.approved';
    case REJECTED = 'task.rejected';

    // ── The rest of the real lifecycle ───────────────────────────────────
    case ACCEPTED = 'task.accepted';
    case DECLINED = 'task.declined';
    case REASSIGNED = 'task.reassigned';
    case UNASSIGNED = 'task.unassigned';
    case NEEDS_REVISION = 'task.needs_revision';
    case PROGRESS_UPDATED = 'task.progress_updated';
    case CHECKLIST_TICKED = 'task.checklist_ticked';
    case COMMENTED = 'task.commented';
    case ATTACHMENT_ADDED = 'task.attachment_added';
    case CANCELLED = 'task.cancelled';
    case DELETED = 'task.deleted';
    case STATUS_CHANGED = 'task.status_changed';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Task created',
            self::UPDATED => 'Task updated',
            self::ASSIGNED => 'Task assigned',
            self::STARTED => 'Task started',
            self::SUBMITTED => 'Task submitted',
            self::APPROVED => 'Task approved',
            self::REJECTED => 'Task rejected',
            self::ACCEPTED => 'Assignment accepted',
            self::DECLINED => 'Assignment declined',
            self::REASSIGNED => 'Task reassigned',
            self::UNASSIGNED => 'Assignee removed',
            self::NEEDS_REVISION => 'Revision requested',
            self::PROGRESS_UPDATED => 'Progress updated',
            self::CHECKLIST_TICKED => 'Checklist step completed',
            self::COMMENTED => 'Comment added',
            self::ATTACHMENT_ADDED => 'File attached',
            self::CANCELLED => 'Task cancelled',
            self::DELETED => 'Task deleted',
            self::STATUS_CHANGED => 'Status changed',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CREATED => 'heroicon-o-plus-circle',
            self::UPDATED => 'heroicon-o-pencil-square',
            self::ASSIGNED, self::REASSIGNED => 'heroicon-o-user-plus',
            self::UNASSIGNED => 'heroicon-o-user-minus',
            self::STARTED => 'heroicon-o-play',
            self::SUBMITTED => 'heroicon-o-paper-airplane',
            self::APPROVED => 'heroicon-o-check-badge',
            self::REJECTED => 'heroicon-o-x-circle',
            self::ACCEPTED => 'heroicon-o-hand-thumb-up',
            self::DECLINED => 'heroicon-o-hand-thumb-down',
            self::NEEDS_REVISION => 'heroicon-o-arrow-path',
            self::PROGRESS_UPDATED => 'heroicon-o-chart-bar',
            self::CHECKLIST_TICKED => 'heroicon-o-check',
            self::COMMENTED => 'heroicon-o-chat-bubble-left',
            self::ATTACHMENT_ADDED => 'heroicon-o-paper-clip',
            self::CANCELLED => 'heroicon-o-no-symbol',
            self::DELETED => 'heroicon-o-trash',
            self::STATUS_CHANGED => 'heroicon-o-arrows-right-left',
        };
    }

    /**
     * The action a status transition should be logged under.
     *
     * The named actions the requirements call for take precedence, so the
     * timeline reads "Task approved" rather than the generic "Status changed"
     * wherever a specific word exists.
     */
    public static function forStatus(TaskStatus $status): self
    {
        return match ($status) {
            TaskStatus::ASSIGNED => self::ASSIGNED,
            TaskStatus::IN_PROGRESS => self::STARTED,
            TaskStatus::SUBMITTED => self::SUBMITTED,
            TaskStatus::APPROVED => self::APPROVED,
            TaskStatus::REJECTED => self::REJECTED,
            TaskStatus::CANCELLED => self::CANCELLED,
            TaskStatus::DRAFT, TaskStatus::UNDER_REVIEW => self::STATUS_CHANGED,
        };
    }

    /**
     * The action one volunteer's own move should be logged under.
     *
     * Assignment moves are what a *person did* — "Ahmad started", "Ahmad
     * submitted". The task-level status change they cause is a consequence, and
     * is logged separately as a generic status change, so the timeline never
     * says the same thing twice in two voices.
     */
    public static function forAssignmentStatus(TaskAssignmentStatus $status): self
    {
        return match ($status) {
            TaskAssignmentStatus::PENDING => self::ASSIGNED,
            TaskAssignmentStatus::ACCEPTED => self::ACCEPTED,
            TaskAssignmentStatus::DECLINED => self::DECLINED,
            TaskAssignmentStatus::IN_PROGRESS => self::STARTED,
            TaskAssignmentStatus::SUBMITTED => self::SUBMITTED,
            TaskAssignmentStatus::APPROVED => self::APPROVED,
            TaskAssignmentStatus::REJECTED => self::REJECTED,
            TaskAssignmentStatus::REASSIGNED => self::REASSIGNED,
            TaskAssignmentStatus::CANCELLED => self::CANCELLED,
        };
    }

    /** Actions that should also land in the user's notification inbox. */
    public function isNotifiable(): bool
    {
        return in_array($this, [
            self::ASSIGNED,
            self::REASSIGNED,
            self::SUBMITTED,
            self::APPROVED,
            self::REJECTED,
            self::NEEDS_REVISION,
            self::COMMENTED,
            self::CANCELLED,
        ], strict: true);
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
