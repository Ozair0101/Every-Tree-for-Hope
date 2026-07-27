<?php

namespace App\Models;

use App\Enums\TaskActivityAction;
use App\Enums\TaskAssignmentRole;
use App\Enums\TaskAssignmentStatus;
use App\Enums\TaskAttachmentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

/**
 * One user's attachment to one task, and their personal progress on it.
 *
 * This is where a volunteer's own clock runs: accepted, started, submitted.
 * The task's status is a roll-up of these (see `Task::recalculateStatus()`),
 * so a five-person watering round has one definition and five independent
 * lifecycles.
 */
class TaskAssignment extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'assigned_by',
        'role',
        'status',
        'is_primary',
        'assigned_at',
        'accepted_at',
        'declined_at',
        'started_at',
        'submitted_at',
        'completed_at',
        'remarks',
        'last_notified_at',
        'reminders_sent',
    ];

    protected $casts = [
        'role' => TaskAssignmentRole::class,
        'status' => TaskAssignmentStatus::class,
        'is_primary' => 'boolean',
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_notified_at' => 'datetime',
        'reminders_sent' => 'integer',
    ];

    /** Mirror the column defaults so a new assignment is usable before reload. */
    protected $attributes = [
        'role' => TaskAssignmentRole::ASSIGNEE->value,
        'status' => TaskAssignmentStatus::PENDING->value,
        'is_primary' => false,
        'reminders_sent' => 0,
    ];

    /**
     * Default `assigned_at` to now when the caller did not supply it.
     *
     * Left overridable rather than forced, so a coordinator can backdate an
     * assignment that was actually agreed in the field yesterday.
     */
    protected static function booted(): void
    {
        static::creating(function (self $assignment) {
            $assignment->assigned_at ??= now();
        });
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class)->orderBy('attempt');
    }

    /** The most recent attempt — what a reviewer opens. */
    public function latestSubmission(): HasOne
    {
        return $this->hasOne(TaskSubmission::class)->latestOfMany('attempt');
    }

    public function checklistCompletions(): HasMany
    {
        return $this->hasMany(TaskChecklistCompletion::class);
    }

    /** Interim progress reports, newest first. */
    public function progressUpdates(): HasMany
    {
        return $this->hasMany(TaskProgress::class)->latest();
    }

    /** Every verdict passed on this volunteer's work, newest first. */
    public function reviews(): HasMany
    {
        return $this->hasMany(TaskReview::class)->orderByDesc('reviewed_at');
    }

    /** The verdict that currently stands. */
    public function latestReview(): HasOne
    {
        return $this->hasOne(TaskReview::class)->latestOfMany('reviewed_at');
    }

    /**
     * File a progress report and refresh the task's cached percentage.
     *
     * Returns the row so the caller can attach photos to it or queue a push.
     */
    public function reportProgress(
        int $percentage,
        ?User $actor = null,
        ?string $note = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $clientUuid = null,
    ): TaskProgress {
        // A device that lost the response to its last attempt will send the same
        // client key again. Returning the original row — without re-running the
        // transition, the log entry or the roll-up — is what makes the retry
        // safe rather than a duplicate report skewing the percentage.
        if (TaskProgress::alreadyRecorded($clientUuid)) {
            return TaskProgress::byClientUuid($clientUuid)->firstOrFail();
        }

        return DB::transaction(function () use ($percentage, $actor, $note, $latitude, $longitude, $clientUuid) {
            $progress = TaskProgress::createOnce($clientUuid, [
                'task_assignment_id' => $this->id,
                'task_id' => $this->task_id,
                'progress_percentage' => $percentage,
                'note' => $note,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'created_by' => $actor?->id,
            ]);

            // Reporting progress is starting work. Doing it here means a
            // volunteer who skips "Start" and just files 20% still moves the
            // task off Assigned, which is what the coordinator expects to see.
            if ($this->status === TaskAssignmentStatus::PENDING
                || $this->status === TaskAssignmentStatus::ACCEPTED) {
                $this->transitionTo(TaskAssignmentStatus::IN_PROGRESS, $actor);
            }

            $this->task->logActivity(
                TaskActivityAction::PROGRESS_UPDATED,
                $actor,
                assignment: $this,
                meta: ['percentage' => $progress->progress_percentage],
            );

            $this->task->recalculateProgress();

            return $progress;
        });
    }

    /**
     * Every file this volunteer uploaded for this job, across all attempts.
     *
     * The submission-level `attachments()` relation is the precise one — it says
     * which attempt a photo belongs to. This is the flat view the app's gallery
     * and the reviewer's sidebar want.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class)->latest();
    }

    public function photos(): HasMany
    {
        return $this->hasMany(TaskAttachment::class)
            ->where('file_type', TaskAttachmentType::IMAGE->value)
            ->latest();
    }

    /**
     * Whether the photo requirement is satisfied.
     *
     * Checked against the current attempt only: a task that demands a photo is
     * not satisfied by one uploaded for a rejected earlier attempt, which is
     * usually the very thing that was wrong with it.
     */
    public function hasRequiredPhoto(): bool
    {
        if (! $this->task->requires_photo) {
            return true;
        }

        $current = $this->latestSubmission;

        if ($current === null) {
            return $this->photos()->exists();
        }

        return $current->attachments()
            ->where('file_type', TaskAttachmentType::IMAGE->value)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Lifecycle
    |--------------------------------------------------------------------------
    */

    /**
     * Move this assignment to a new status, stamping the matching timestamp.
     *
     * The single write path for `status`. Returns false rather than throwing on
     * an illegal move, so an API controller can turn it into a 422 without a
     * try/catch — the same contract as {@see Task::transitionTo()}.
     *
     * The timestamp and the status are written together: that is what keeps
     * `started_at` from ever being null on a row that claims to be submitted.
     * The task-level roll-up runs afterwards, in the same transaction, so a
     * task can never disagree with the assignees it is derived from.
     */
    public function transitionTo(
        TaskAssignmentStatus $target,
        ?User $actor = null,
        ?string $remarks = null,
    ): bool {
        if (! $this->status->canTransitionTo($target)) {
            return false;
        }

        DB::transaction(function () use ($target, $actor, $remarks) {
            $this->status = $target;

            match ($target) {
                TaskAssignmentStatus::ACCEPTED => $this->accepted_at ??= now(),
                TaskAssignmentStatus::DECLINED => $this->declined_at ??= now(),
                // `??=` on purpose: rework returns an assignment to In Progress,
                // and started_at must keep pointing at the first start so the
                // total elapsed time stays honest.
                TaskAssignmentStatus::IN_PROGRESS => $this->started_at ??= now(),
                TaskAssignmentStatus::SUBMITTED => $this->submitted_at = now(),
                TaskAssignmentStatus::APPROVED => $this->completed_at ??= now(),
                default => null,
            };

            if ($remarks !== null) {
                $this->remarks = $remarks;
            }

            $this->save();

            // What the *person* did — "Ahmad started", "Ahmad submitted". The
            // task-level consequence is logged separately by the roll-up as a
            // status change, so the timeline never tells one event twice.
            $this->task->logActivity(
                TaskActivityAction::forAssignmentStatus($target),
                $actor,
                assignment: $this,
            );

            // Keep the task in step with the people doing it.
            $this->task->recalculateStatus($actor, $this);
        });

        return true;
    }

    /** Convenience wrappers for the mobile API's action endpoints. */
    public function accept(?User $actor = null): bool
    {
        return $this->transitionTo(TaskAssignmentStatus::ACCEPTED, $actor);
    }

    public function decline(?User $actor = null, ?string $remarks = null): bool
    {
        return $this->transitionTo(TaskAssignmentStatus::DECLINED, $actor, $remarks);
    }

    public function start(?User $actor = null): bool
    {
        return $this->transitionTo(TaskAssignmentStatus::IN_PROGRESS, $actor);
    }

    /**
     * Elapsed working time in minutes, or null while still unfinished.
     *
     * Reads straight off the lifecycle columns — no history scan.
     */
    public function durationMinutes(): ?int
    {
        if ($this->started_at === null || $this->completed_at === null) {
            return null;
        }

        return (int) $this->started_at->diffInMinutes($this->completed_at);
    }

    /** How long the volunteer sat on the assignment before starting it. */
    public function responseMinutes(): ?int
    {
        if ($this->assigned_at === null || $this->started_at === null) {
            return null;
        }

        return (int) $this->assigned_at->diffInMinutes($this->started_at);
    }

    /**
     * The attempt number the next submission should carry.
     *
     * Read at submit time inside the same transaction as the insert; the unique
     * (task_assignment_id, attempt) index is what actually guarantees no
     * duplicate, this just picks the number.
     */
    public function nextAttemptNumber(): int
    {
        return (int) $this->submissions()->max('attempt') + 1;
    }

    /**
     * Whether every required checklist item has been ticked by this assignee.
     * The submit endpoint refuses an incomplete checklist.
     */
    public function hasCompletedRequiredChecklist(): bool
    {
        $requiredIds = $this->task->checklistItems()->where('is_required', true)->pluck('id');

        if ($requiredIds->isEmpty()) {
            return true;
        }

        $done = $this->checklistCompletions()
            ->whereIn('task_checklist_item_id', $requiredIds)
            ->count();

        return $done >= $requiredIds->count();
    }

    /** Assignments still expecting action from the volunteer. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            TaskAssignmentStatus::PENDING->value,
            TaskAssignmentStatus::ACCEPTED->value,
            TaskAssignmentStatus::IN_PROGRESS->value,
            TaskAssignmentStatus::REJECTED->value,
        ]);
    }

    public function scopeAssignees(Builder $query): Builder
    {
        return $query->where('role', TaskAssignmentRole::ASSIGNEE->value);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
