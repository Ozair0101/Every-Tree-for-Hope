<?php

namespace App\Models;

use App\Enums\TaskActivityAction;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

/**
 * The single audit trail: every action anyone took on a task.
 *
 * Append-only. Nothing updates or deletes these rows, and `user_id` is nullable
 * with ON DELETE SET NULL — an audit trail that can be rewritten by deleting an
 * account is not an audit trail.
 *
 * This table absorbed what began as a separate `task_status_histories`. Two
 * tables recording overlapping events meant writing two rows per transition and
 * gave drift somewhere to hide. Status changes keep their typed `from_status` /
 * `to_status` columns here, so "when was this approved?" stays an indexed query
 * while everything else lands in the same timeline.
 */
class TaskActivityLog extends Model
{
    /**
     * Deleted in bulk, not model by model.
     *
     * This is the fastest-growing table in the module — roughly ten to fifteen
     * rows per task lifecycle — and it has no `deleted` hook to honour and no
     * files to unlink, so there is nothing to gain from hydrating each row. Mass
     * pruning issues plain DELETEs in chunks and leaves the table usable
     * throughout. Register `model:prune` on the scheduler to use it.
     */
    use MassPrunable;

    /**
     * How long a timeline entry is kept.
     *
     * Two years: long enough to answer "was this site actually visited in June
     * last year?", which is the question donors and auditors ask, and short
     * enough that the table does not become the largest thing in the database.
     * Status transitions are kept longer than routine chatter — see prunable().
     */
    public const RETENTION_DAYS = 730;

    /** Routine entries — comments, ticks, progress — age out sooner. */
    public const CHATTER_RETENTION_DAYS = 180;

    protected $perPage = 50;

    protected $fillable = [
        'task_id',
        'task_assignment_id',
        'user_id',
        'action',
        'description',
        'from_status',
        'to_status',
        'ip',
        'device',
        'meta',
    ];

    protected $casts = [
        'action' => TaskActivityAction::class,
        'from_status' => TaskStatus::class,
        'to_status' => TaskStatus::class,
        'meta' => 'array',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TaskAssignment::class, 'task_assignment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Write an entry, capturing request provenance automatically.
     *
     * IP and device are read from the current request when one exists, so
     * callers never have to remember to pass them — and a console command or
     * queued job simply records none rather than a misleading value.
     *
     * @param  array<string, mixed>  $meta
     */
    public static function record(
        Task $task,
        TaskActivityAction $action,
        ?User $actor = null,
        ?string $description = null,
        ?TaskAssignment $assignment = null,
        ?TaskStatus $from = null,
        ?TaskStatus $to = null,
        array $meta = [],
    ): self {
        $request = app()->bound('request') && app('request') instanceof Request
            ? app('request')
            : null;

        return static::create([
            'task_id' => $task->id,
            'task_assignment_id' => $assignment?->id,
            'user_id' => $actor?->id,
            'action' => $action->value,
            'description' => $description ?? static::describe($action, $task, $actor, $assignment),
            'from_status' => $from?->value,
            'to_status' => $to?->value,
            'ip' => $request?->ip(),
            'device' => $request?->userAgent(),
            'meta' => $meta ?: null,
        ]);
    }

    /**
     * Build a readable sentence for the timeline.
     *
     * Composed at write time and stored, not rendered on read: the wording must
     * stay true to what happened even after the task is renamed or the user is
     * deleted. A timeline that rewrites itself is worthless as evidence.
     */
    protected static function describe(
        TaskActivityAction $action,
        Task $task,
        ?User $actor,
        ?TaskAssignment $assignment,
    ): string {
        $who = $actor?->name ?? 'System';
        $target = $assignment?->user?->name;

        return match ($action) {
            TaskActivityAction::CREATED => "{$who} created task {$task->reference}.",
            TaskActivityAction::ASSIGNED => $target
                ? "{$who} assigned {$task->reference} to {$target}."
                : "{$who} assigned {$task->reference}.",
            TaskActivityAction::REASSIGNED => "{$who} reassigned {$task->reference}".($target ? " to {$target}." : '.'),
            TaskActivityAction::STARTED => "{$who} started work on {$task->reference}.",
            TaskActivityAction::SUBMITTED => "{$who} submitted {$task->reference} for review.",
            TaskActivityAction::APPROVED => "{$who} approved {$task->reference}.",
            TaskActivityAction::REJECTED => "{$who} rejected the submission for {$task->reference}.",
            TaskActivityAction::NEEDS_REVISION => "{$who} requested a revision on {$task->reference}.",
            TaskActivityAction::CANCELLED => "{$who} cancelled {$task->reference}.",
            default => "{$who}: ".strtolower($action->label())." on {$task->reference}.",
        };
    }

    /**
     * What `model:prune` may delete.
     *
     * Two windows, because two kinds of entry have different value. A status
     * transition is the evidence trail — who approved what, when — and is kept
     * for the full retention period. Routine chatter (comments, checklist ticks,
     * progress reports) is useful while work is live and noise a year later, so
     * it goes first.
     *
     * Entries belonging to an unfinished task are never pruned regardless of
     * age: a task still open after two years is exactly the one somebody will
     * ask questions about.
     */
    public function prunable(): Builder
    {
        return static::query()
            ->whereHas('task', fn (Builder $q) => $q->whereIn('status', [
                TaskStatus::APPROVED->value,
                TaskStatus::CANCELLED->value,
            ]))
            ->where(function (Builder $q) {
                $q->where('created_at', '<', now()->subDays(self::RETENTION_DAYS))
                    ->orWhere(fn (Builder $inner) => $inner
                        ->whereNull('to_status')
                        ->where('created_at', '<', now()->subDays(self::CHATTER_RETENTION_DAYS)));
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /** The task detail screen's activity feed. */
    public function scopeTimeline(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    public function scopeOfAction(Builder $query, TaskActivityAction $action): Builder
    {
        return $query->where('action', $action->value);
    }

    /** Only the entries that were status transitions. */
    public function scopeStatusChanges(Builder $query): Builder
    {
        return $query->whereNotNull('to_status');
    }

    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
