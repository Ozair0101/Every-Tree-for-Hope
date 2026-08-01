<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A tick: this assignee completed this step, at this moment.
 *
 * Keyed by assignment rather than by user, so a volunteer reassigned to the
 * same task later starts from a clean checklist. The unique index on
 * (item, assignment) makes the ticking endpoint an idempotent upsert — which
 * matters when the app retries over a flaky rural connection.
 */
class TaskChecklistCompletion extends Model
{
    protected $fillable = [
        'task_checklist_item_id',
        'task_assignment_id',
        'completed_by',
        'completed_at',
        'note',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(TaskChecklistItem::class, 'task_checklist_item_id');
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TaskAssignment::class, 'task_assignment_id');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function scopeForAssignment(Builder $query, int $assignmentId): Builder
    {
        return $query->where('task_assignment_id', $assignmentId);
    }

    /** Newest ticks first — the "what just happened" feed. */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('completed_at');
    }
}
