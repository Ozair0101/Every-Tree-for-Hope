<?php

namespace App\Models;

use App\Enums\TaskActivityAction;
use App\Enums\TaskAssignmentRole;
use App\Enums\TaskAssignmentStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Exceptions\TaskAssignmentException;
use App\Models\Concerns\HasPublicUuid;
use App\Models\Concerns\HasTaskAttachments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * A unit of field work handed to one or more volunteers.
 *
 * The task owns the *definition* of the work — what, where, by when, to what
 * standard. Who does it lives in {@see TaskAssignment}; what they turned in
 * lives in {@see TaskSubmission}. Status changes go through
 * {@see self::transitionTo()} so the state machine in {@see TaskStatus} is
 * enforced and every move is recorded in `task_status_histories`.
 */
class Task extends Model
{
    use HasPublicUuid, HasTaskAttachments, SoftDeletes;

    /**
     * Columns a list screen actually needs.
     *
     * `description` and `instructions` are TEXT and LONGTEXT: a 50-row page
     * would drag fifty long bodies out of MySQL, over the wire and into memory
     * to render rows that show a title and a due date. Excluding them is the
     * single biggest win available on the busiest query in the app.
     *
     * Every foreign key stays in the projection — omitting one silently breaks
     * eager loading, which is a far more expensive mistake than the bytes saved.
     */
    public const LIST_COLUMNS = [
        'id', 'uuid', 'reference', 'title', 'priority', 'status',
        'start_date', 'due_date', 'progress', 'estimated_hours',
        'latitude', 'longitude', 'radius', 'location_name',
        'max_assignees', 'requires_photo', 'requires_geo_check', 'requires_review',
        'task_category_id', 'event_id', 'upcoming_event_id', 'parent_task_id',
        'created_by', 'approved_by', 'completed_at', 'created_at', 'updated_at',
    ];

    /** Matches the mobile client's page size, so it never over-fetches. */
    protected $perPage = 20;

    protected $fillable = [
        'reference',
        'task_category_id',
        'event_id',
        'upcoming_event_id',
        'parent_task_id',
        'task_template_id',
        'task_recurrence_id',
        'occurrence_date',
        'title',
        'description',
        'instructions',
        'priority',
        'status',
        'start_date',
        'due_date',
        'estimated_hours',
        'actual_hours',
        'latitude',
        'longitude',
        'radius',
        'location_name',
        'requires_photo',
        'requires_geo_check',
        'requires_review',
        'max_assignees',
        'progress',
        'created_by',
        'approved_by',
        'published_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'priority' => TaskPriority::class,
        'status' => TaskStatus::class,
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'occurrence_date' => 'date',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'latitude' => 'float',
        'longitude' => 'float',
        'radius' => 'integer',
        'requires_photo' => 'boolean',
        'requires_geo_check' => 'boolean',
        'requires_review' => 'boolean',
        'max_assignees' => 'integer',
        'progress' => 'integer',
        'published_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Mirror the column defaults in memory, so a task created without an
     * explicit status is `draft` on the object as well as in the row —
     * `transitionTo()` reads `$this->status` and would otherwise see null.
     */
    protected $attributes = [
        'status' => TaskStatus::DRAFT->value,
        'priority' => TaskPriority::MEDIUM->value,
        'progress' => 0,
        // Off by default, matching the columns. The admin form no longer asks
        // for coordinates or an on-site radius, so a geo check would have
        // nothing to measure against and a photo requirement would block
        // submissions for a rule nobody chose. Both remain settable through the
        // API for a task that genuinely needs them.
        'requires_photo' => false,
        'requires_geo_check' => false,
        // Stays on: with the toggle gone this is the single behaviour for every
        // task, and the review queue is built on work being checked.
        'requires_review' => true,
    ];

    /**
     * Assign the human-quotable reference on insert.
     *
     * Uses the row id, so the sequence can never collide the way a random or
     * count-based scheme can under concurrent inserts.
     */
    protected static function booted(): void
    {
        static::created(function (self $task) {
            if (blank($task->reference)) {
                $task->forceFill([
                    'reference' => sprintf('TSK-%s-%06d', $task->created_at->format('Y'), $task->id),
                ])->saveQuietly();
            }

            // "Task Created" is the first entry every timeline needs, and doing
            // it here means it cannot be forgotten by a controller, a seeder or
            // the recurrence generator.
            TaskActivityLog::record($task, TaskActivityAction::CREATED, $task->creator);
        });

        // "Task Updated" — recorded with the field names that actually changed,
        // so the timeline can say *what* was edited without diffing snapshots.
        static::updated(function (self $task) {
            // Columns a transition or a roll-up writes for itself. Those events
            // are already logged with their own action, and repeating them here
            // as "Task updated" would bury the real edits in machine noise.
            $derived = [
                'status', 'updated_at', 'reference', 'progress', 'actual_hours',
                'published_at', 'completed_at', 'cancelled_at', 'cancellation_reason',
                'approved_by',
            ];

            $changed = array_values(array_diff(array_keys($task->getChanges()), $derived));

            if ($changed === []) {
                return;
            }

            TaskActivityLog::record(
                task: $task,
                action: TaskActivityAction::UPDATED,
                meta: ['changed' => $changed],
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function upcomingEvent(): BelongsTo
    {
        return $this->belongsTo(UpcomingEvent::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_task_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(self::class, 'parent_task_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(TaskTemplate::class, 'task_template_id');
    }

    public function recurrence(): BelongsTo
    {
        return $this->belongsTo(TaskRecurrence::class, 'task_recurrence_id');
    }

    /** Everyone attached to the task, in any role. */
    public function assignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function assignees(): HasMany
    {
        return $this->hasMany(TaskAssignment::class)
            ->where('role', TaskAssignmentRole::ASSIGNEE->value);
    }

    public function reviewers(): HasMany
    {
        return $this->hasMany(TaskAssignment::class)
            ->where('role', TaskAssignmentRole::REVIEWER->value);
    }

    /** The one accountable assignee, when the task is shared by a team. */
    public function primaryAssignment(): HasMany
    {
        return $this->hasMany(TaskAssignment::class)->where('is_primary', true);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class)->latest();
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(TaskChecklistItem::class)->orderBy('position');
    }

    public function checklistCompletions(): HasManyThrough
    {
        return $this->hasManyThrough(
            TaskChecklistCompletion::class,
            TaskChecklistItem::class,
            'task_id',
            'task_checklist_item_id',
        );
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->whereNull('parent_id')->oldest();
    }

    /**
     * Remove every file belonging to this task, not just its own.
     *
     * Hard-deleting a task cascades its submissions and comments away at the
     * database level, which fires no Eloquent events — so their attachments'
     * files would be left on disk with nothing pointing at them. Sweeping by
     * `task_id` catches the whole tree before the cascade runs.
     */
    protected function purgeTaskAttachments(): void
    {
        TaskAttachment::query()
            ->where('task_id', $this->id)
            ->each(fn (TaskAttachment $attachment) => $attachment->delete());
    }

    /** The audit trail — every action taken on this task, newest first. */
    public function activities(): HasMany
    {
        return $this->hasMany(TaskActivityLog::class)->latest();
    }

    /** Just the status transitions out of the activity log. */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(TaskActivityLog::class)->whereNotNull('to_status')->latest();
    }

    /** Interim progress reports across every assignee. */
    public function progressUpdates(): HasMany
    {
        return $this->hasMany(TaskProgress::class)->latest();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TaskReview::class)->orderByDesc('reviewed_at');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(TaskNotification::class)->latest();
    }

    /** Tasks that must finish before this one may start. */
    public function dependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class);
    }

    /** Tasks waiting on this one. */
    public function dependents(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'depends_on_task_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Assignment
    |--------------------------------------------------------------------------
    |
    | A task is assignable to one volunteer or to many. `max_assignees` is what
    | separates the two: 1 makes it a single-assignee task, null leaves it open
    | to a whole team. The limit is checked here rather than in a controller so
    | it holds for the admin panel, the mobile API and any future importer
    | alike.
    */

    /** This task takes exactly one assignee. */
    public function isSingleAssignee(): bool
    {
        return $this->max_assignees === 1;
    }

    /** Assignees still engaged — declined and reassigned people do not count. */
    public function activeAssigneeCount(): int
    {
        return $this->assignees()
            ->whereNotIn('status', [
                TaskAssignmentStatus::DECLINED->value,
                TaskAssignmentStatus::REASSIGNED->value,
                TaskAssignmentStatus::CANCELLED->value,
            ])
            ->count();
    }

    public function hasCapacityForAssignee(): bool
    {
        return $this->max_assignees === null
            || $this->activeAssigneeCount() < $this->max_assignees;
    }

    /**
     * Attach one user to this task.
     *
     * Idempotent for a user who already holds the role: returns the existing
     * assignment untouched rather than throwing, because the admin panel's
     * multi-select re-submits the full list on every save and a re-save must not
     * be an error.
     *
     * Moves a Draft task to Assigned on the first assignee, so publishing is a
     * consequence of handing work out rather than a second step an operator can
     * forget.
     *
     * @throws TaskAssignmentException when the task is full or closed
     */
    public function assign(
        User $user,
        ?User $by = null,
        TaskAssignmentRole $role = TaskAssignmentRole::ASSIGNEE,
        bool $isPrimary = false,
        ?string $remarks = null,
        ?\DateTimeInterface $assignedAt = null,
    ): TaskAssignment {
        $existing = $this->assignments()
            ->where('user_id', $user->id)
            ->where('role', $role->value)
            ->first();

        if ($existing) {
            return $existing;
        }

        if ($this->status->isTerminal()) {
            throw TaskAssignmentException::notOpenForAssignment($this);
        }

        // Reviewers and watchers are not capped — the limit is about who does
        // the work, not who oversees it.
        if ($role === TaskAssignmentRole::ASSIGNEE && ! $this->hasCapacityForAssignee()) {
            throw TaskAssignmentException::limitReached($this);
        }

        return DB::transaction(function () use ($user, $by, $role, $isPrimary, $remarks, $assignedAt) {
            // A task has at most one primary; promoting a new one demotes the old.
            if ($isPrimary) {
                $this->assignments()->where('is_primary', true)->update(['is_primary' => false]);
            }

            $assignment = $this->assignments()->create([
                'user_id' => $user->id,
                'assigned_by' => $by?->id,
                'role' => $role->value,
                'status' => TaskAssignmentStatus::PENDING->value,
                'is_primary' => $isPrimary,
                'assigned_at' => $assignedAt ?? now(),
                'remarks' => $remarks,
            ]);

            // One "Task assigned" entry per person. Logged here rather than
            // relying on the publish transition below, which only fires for the
            // first assignee — the second and third would otherwise be silent.
            $this->logActivity(TaskActivityAction::ASSIGNED, $by, assignment: $assignment);

            if ($role === TaskAssignmentRole::ASSIGNEE && $this->status === TaskStatus::DRAFT) {
                $this->transitionTo(
                    target: TaskStatus::ASSIGNED,
                    actor: $by,
                    meta: ['source' => 'assign'],
                    assignment: $assignment,
                    // The assignment itself is already logged above; this row is
                    // the task leaving Draft, which is a different fact.
                    action: TaskActivityAction::STATUS_CHANGED,
                );
            }

            return $assignment;
        });
    }

    /**
     * Attach several users at once.
     *
     * All-or-nothing: if the fifth user would exceed the limit, the first four
     * are rolled back too. A half-assigned task is worse than a refused one —
     * the coordinator sees the error and picks a smaller set, rather than
     * discovering later that two of the five never got the push.
     *
     * @param  iterable<User>  $users
     * @return \Illuminate\Support\Collection<int, TaskAssignment>
     *
     * @throws TaskAssignmentException
     */
    public function assignMany(
        iterable $users,
        ?User $by = null,
        TaskAssignmentRole $role = TaskAssignmentRole::ASSIGNEE,
    ): Collection {
        return DB::transaction(function () use ($users, $by, $role) {
            // An Eloquent collection, not `collect()`: callers routinely want to
            // `load()` relations onto the result, and a Support collection has
            // no such method. Eloquent's extends Support's, so this is strictly
            // more capable and changes nothing for callers that only iterate.
            $assignments = TaskAssignment::query()->getModel()->newCollection();

            foreach ($users as $user) {
                $assignments->push($this->assign($user, $by, $role));
                // assign() reads activeAssigneeCount() off the database, so the
                // relation cache must not go stale between iterations or the
                // limit would be checked against the count we started with.
                $this->unsetRelation('assignments')->unsetRelation('assignees');
            }

            return $assignments;
        });
    }

    /**
     * Hand a task from one volunteer to another, preserving the audit trail.
     *
     * The old assignment is closed as `reassigned` rather than deleted: the
     * record that it was once theirs, and why it moved, is exactly what a
     * coordinator needs three months later.
     */
    public function reassign(User $from, User $to, ?User $by = null, ?string $remarks = null): TaskAssignment
    {
        return DB::transaction(function () use ($from, $to, $by, $remarks) {
            $current = $this->assignees()->where('user_id', $from->id)->first();

            if ($current) {
                $current->transitionTo(TaskAssignmentStatus::REASSIGNED, $by, $remarks);
            }

            return $this->assign($to, $by, remarks: $remarks, isPrimary: (bool) $current?->is_primary);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | State machine
    |--------------------------------------------------------------------------
    */

    /**
     * Move the task to a new status, recording the change.
     *
     * The single write path for `status`. Returns false — rather than throwing —
     * on an illegal move so callers can turn it into a 422 without a try/catch.
     * The status write and its history row share a transaction: an audit trail
     * with gaps is worse than no audit trail.
     *
     * @param  array<string, mixed>  $meta  source, ip, app version …
     */
    public function transitionTo(
        TaskStatus $target,
        ?User $actor = null,
        ?string $reason = null,
        array $meta = [],
        ?TaskAssignment $assignment = null,
        ?TaskActivityAction $action = null,
    ): bool {
        $current = $this->status;

        if (! $current->canTransitionTo($target)) {
            return false;
        }

        DB::transaction(function () use ($current, $target, $actor, $reason, $meta, $assignment, $action) {
            $this->status = $target;

            // Timestamp the milestones the reports read, so no query has to
            // reconstruct them from the history table.
            match ($target) {
                TaskStatus::ASSIGNED => $this->published_at ??= now(),
                TaskStatus::APPROVED => tap($this, function (self $task) use ($actor) {
                    $task->completed_at = now();
                    $task->approved_by = $actor?->id;
                    $task->progress = 100;
                }),
                TaskStatus::CANCELLED => tap($this, function (self $task) use ($reason) {
                    $task->cancelled_at = now();
                    $task->cancellation_reason = $reason;
                }),
                default => null,
            };

            $this->save();

            // One audit trail for everything. The action is named where a
            // specific word exists ("Task approved") and falls back to the
            // generic status change otherwise, while `from_status`/`to_status`
            // keep the transition itself queryable.
            TaskActivityLog::record(
                task: $this,
                action: $action ?? TaskActivityAction::forStatus($target),
                actor: $actor,
                description: $reason ? "{$this->reference}: {$reason}" : null,
                assignment: $assignment,
                from: $current,
                to: $target,
                meta: $meta,
            );
        });

        return true;
    }

    /**
     * Log a non-transition action against this task.
     *
     * A thin pass-through so callers never need to import the log model, and so
     * every write goes through one place if the signature ever changes.
     *
     * @param  array<string, mixed>  $meta
     */
    public function logActivity(
        TaskActivityAction $action,
        ?User $actor = null,
        ?string $description = null,
        ?TaskAssignment $assignment = null,
        array $meta = [],
    ): TaskActivityLog {
        return TaskActivityLog::record(
            task: $this,
            action: $action,
            actor: $actor,
            description: $description,
            assignment: $assignment,
            meta: $meta,
        );
    }

    /**
     * Re-derive the task status from its assignees.
     *
     * The task-level status is a roll-up, never independently edited: it is
     * Approved only when every engaged assignee is approved, Submitted once all
     * of them have submitted, In Progress as soon as one has started. Called
     * after any assignment status change.
     */
    public function recalculateStatus(?User $actor = null, ?TaskAssignment $trigger = null): void
    {
        $engaged = $this->assignees()
            ->get()
            ->reject(fn (TaskAssignment $a) => $a->status->isDisengaged());

        if ($engaged->isEmpty()) {
            return;
        }

        $target = match (true) {
            $engaged->every(fn ($a) => $a->status === TaskAssignmentStatus::APPROVED) => TaskStatus::APPROVED,
            // Everyone's work was sent back: the task itself needs rework, and
            // should say so on the board rather than sitting in Submitted where
            // a coordinator would think it was still queued for review.
            $engaged->every(fn ($a) => $a->status === TaskAssignmentStatus::REJECTED) => TaskStatus::REJECTED,
            $engaged->every(fn ($a) => in_array($a->status, [TaskAssignmentStatus::SUBMITTED, TaskAssignmentStatus::APPROVED], true)) => TaskStatus::SUBMITTED,
            $engaged->contains(fn ($a) => $a->status === TaskAssignmentStatus::IN_PROGRESS) => TaskStatus::IN_PROGRESS,
            default => null,
        };

        if (! $target || $this->status === $target) {
            return;
        }

        // The task machine refuses Submitted → Approved: approval must pass
        // through review. When every assignee has already been signed off
        // individually, that review demonstrably happened, so walk the
        // intermediate step rather than letting the roll-up silently fail and
        // leave the task stuck at Submitted. Stepping through also keeps both
        // moves in `task_status_histories`, which is the point of having it.
        if (! $this->status->canTransitionTo($target)
            && $this->status->canTransitionTo(TaskStatus::UNDER_REVIEW)
            && TaskStatus::UNDER_REVIEW->canTransitionTo($target)) {
            $this->rollUpTo(TaskStatus::UNDER_REVIEW, $actor, $trigger);
        }

        $this->rollUpTo($target, $actor, $trigger);
    }

    /**
     * Apply a derived status change.
     *
     * Logged as a generic status change rather than under a named action,
     * because the named action already belongs to the person who caused it —
     * `TaskAssignment::transitionTo()` records "Ahmad submitted his work", and
     * this records "the task moved to Submitted". Two rows saying different
     * things, rather than one event told twice in two voices.
     */
    protected function rollUpTo(TaskStatus $target, ?User $actor, ?TaskAssignment $trigger): void
    {
        $this->transitionTo(
            target: $target,
            actor: $actor,
            meta: ['source' => 'rollup'],
            assignment: $trigger,
            action: TaskActivityAction::STATUS_CHANGED,
        );
    }

    /**
     * Recompute the cached `progress` percentage.
     *
     * Two sources, and the volunteer's own report wins. If anyone has filed a
     * {@see TaskProgress} update, the task's progress is the average of each
     * engaged assignee's latest reported figure — a person on the ground saying
     * "60%" is better information than counting ticked boxes. Only when nobody
     * has reported does it fall back to checklist completion.
     *
     * Averaged across engaged assignees so a team task shows team progress.
     */
    public function recalculateProgress(): void
    {
        $assignmentIds = $this->assignees()
            ->get()
            ->reject(fn (TaskAssignment $a) => $a->status->isDisengaged())
            ->pluck('id');

        if ($assignmentIds->isEmpty()) {
            return;
        }

        $reported = $this->latestReportedProgress($assignmentIds);

        if ($reported !== null) {
            $this->forceFill(['progress' => $reported])->saveQuietly();

            return;
        }

        $requiredIds = $this->checklistItems()->where('is_required', true)->pluck('id');

        if ($requiredIds->isEmpty()) {
            return;
        }

        $done = TaskChecklistCompletion::query()
            ->whereIn('task_checklist_item_id', $requiredIds)
            ->whereIn('task_assignment_id', $assignmentIds)
            ->count();

        $this->forceFill([
            'progress' => (int) round($done / ($requiredIds->count() * $assignmentIds->count()) * 100),
        ])->saveQuietly();
    }

    /**
     * Average of each engaged assignee's most recent progress report.
     *
     * Assignees who have not reported count as 0 rather than being skipped:
     * a five-person task where one person says 100% is 20% done, not 100%.
     *
     * @param  \Illuminate\Support\Collection<int, int>  $assignmentIds
     */
    protected function latestReportedProgress(Collection $assignmentIds): ?int
    {
        // One row per assignment — its newest report. Grouping on the max id
        // rather than max(created_at) avoids ties when several updates land in
        // the same second, which they do when the app flushes an offline queue.
        $latestIds = TaskProgress::query()
            ->whereIn('task_assignment_id', $assignmentIds)
            ->selectRaw('MAX(id) as id')
            ->groupBy('task_assignment_id')
            ->pluck('id');

        if ($latestIds->isEmpty()) {
            return null;
        }

        $sum = (int) TaskProgress::whereIn('id', $latestIds)->sum('progress_percentage');

        return (int) round($sum / $assignmentIds->count());
    }

    /*
    |--------------------------------------------------------------------------
    | Geofence
    |--------------------------------------------------------------------------
    */

    /**
     * Dependencies that are not yet satisfied.
     *
     * @return \Illuminate\Support\Collection<int, TaskDependency>
     */
    public function blockingDependencies(): Collection
    {
        return $this->dependencies()
            ->with('dependsOn:id,uuid,reference,title,status')
            ->get()
            ->reject(fn (TaskDependency $dependency) => $dependency->isSatisfied())
            ->values();
    }

    /** Nothing is standing in this task's way. */
    public function canStart(): bool
    {
        return $this->blockingDependencies()->isEmpty();
    }

    public function hasGeofence(): bool
    {
        return $this->latitude !== null && $this->longitude !== null && $this->radius !== null;
    }

    /**
     * Great-circle distance in metres from the task centre to a point.
     *
     * Haversine on a spherical earth: accurate to ~0.5% — metres over the
     * distances a geofence check cares about, and cheap enough to run inline.
     */
    public function distanceTo(float $latitude, float $longitude): ?float
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        $earthRadius = 6_371_000;
        $dLat = deg2rad($latitude - $this->latitude);
        $dLon = deg2rad($longitude - $this->longitude);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) * sin($dLon / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function isWithinGeofence(float $latitude, float $longitude): ?bool
    {
        if (! $this->hasGeofence()) {
            return null;
        }

        return $this->distanceTo($latitude, $longitude) <= $this->radius;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /** Live work someone is expected to act on. */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [
            TaskStatus::ASSIGNED->value,
            TaskStatus::IN_PROGRESS->value,
            TaskStatus::REJECTED->value,
        ]);
    }

    public function scopeAwaitingReview(Builder $query): Builder
    {
        return $query->whereIn('status', [
            TaskStatus::SUBMITTED->value,
            TaskStatus::UNDER_REVIEW->value,
        ]);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereNotIn('status', [
                TaskStatus::DRAFT->value,
                TaskStatus::APPROVED->value,
                TaskStatus::CANCELLED->value,
            ]);
    }

    /** Everything this user is attached to, in any role. */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->whereHas('assignments', fn (Builder $q) => $q->where('user_id', $userId));
    }

    /**
     * Tasks whose centre falls inside a bounding box.
     *
     * A box, not a circle: it is index-friendly (hits the composite
     * latitude/longitude index) and the app refines to a true radius client-side
     * once the candidate set is small.
     */
    public function scopeWithinBounds(Builder $query, float $minLat, float $maxLat, float $minLng, float $maxLng): Builder
    {
        return $query->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLng, $maxLng]);
    }

    /*
    |--------------------------------------------------------------------------
    | Loading strategy
    |--------------------------------------------------------------------------
    |
    | Named bundles rather than `$with`. A global `$with` would pay for the
    | relations on every query including counts, exports and the recurrence
    | generator, none of which touch them. Naming the bundle at the call site
    | keeps the cost where the benefit is, and makes an N+1 a visible omission
    | rather than an invisible default.
    */

    /** Projection + relations for a list screen. One query per relation, not per row. */
    public function scopeForList(Builder $query): Builder
    {
        return $query
            ->select(self::LIST_COLUMNS)
            ->with([
                'category:id,name,slug,color,icon',
                // Only what a row renders: the avatar strip and a count.
                'assignees:id,task_id,user_id,role,status,is_primary',
                'assignees.user:id,name,lastname,profile_image',
            ])
            ->withCount([
                'assignees',
                'comments',
                'attachments',
            ]);
    }

    /** Everything the detail screen renders, in a fixed number of queries. */
    public function scopeForDetail(Builder $query): Builder
    {
        return $query->with([
            'category',
            'creator:id,name,lastname,profile_image',
            'approver:id,name,lastname',
            'event:id,title',
            'assignments.user:id,name,lastname,profile_image',
            'checklistItems',
            'attachments',
            'dependencies.dependsOn:id,uuid,reference,title,status',
        ]);
    }

    /**
     * Counts without loading the rows behind them.
     *
     * A dashboard that shows "12 assignees, 4 submissions" should not hydrate
     * sixteen models to print two numbers.
     */
    public function scopeWithActivityCounts(Builder $query): Builder
    {
        return $query->withCount([
            'assignees',
            'submissions',
            'comments',
            'progressUpdates',
            'attachments',
        ]);
    }

    /**
     * Critical first, then by deadline, with undated work last.
     *
     * `due_date IS NULL` as a leading sort key pushes tasks without a deadline
     * to the end instead of letting NULL sort first, which is what MySQL and
     * SQLite both do by default and is never what a coordinator wants to see.
     */
    public function scopeMostUrgent(Builder $query): Builder
    {
        return $query
            ->orderByRaw(TaskPriority::sqlOrderCase('priority', 'desc'))
            ->orderByRaw('due_date IS NULL')
            ->orderBy('due_date');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isPast()
            && $this->status->countsTowardsOverdue();
    }
}
