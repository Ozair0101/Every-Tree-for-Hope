<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One step of a task — "dig the pit to 40cm", "photograph the sapling upright".
 *
 * Required items gate submission and drive the cached `tasks.progress` value.
 */
class TaskChecklistItem extends Model
{
    protected $fillable = [
        'task_id',
        'title',
        'description',
        'is_required',
        'requires_photo',
        'position',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'requires_photo' => 'boolean',
        'position' => 'integer',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(TaskChecklistCompletion::class);
    }

    /** Has this specific assignee ticked this step? */
    public function isCompletedBy(TaskAssignment $assignment): bool
    {
        return $this->completions()
            ->where('task_assignment_id', $assignment->id)
            ->exists();
    }

    /**
     * Mark this step done for an assignee.
     *
     * `firstOrCreate` against the (item, assignment) unique index, so a
     * double-tapped checkbox on a slow connection is a no-op rather than an
     * error the volunteer has to interpret.
     */
    public function completeFor(TaskAssignment $assignment, ?User $actor = null, ?string $note = null): TaskChecklistCompletion
    {
        $completion = $this->completions()->firstOrCreate(
            ['task_assignment_id' => $assignment->id],
            ['completed_by' => $actor?->id, 'completed_at' => now(), 'note' => $note],
        );

        if ($completion->wasRecentlyCreated) {
            $assignment->task->recalculateProgress();
        }

        return $completion;
    }

    /** Untick — a volunteer correcting a mis-tap. */
    public function uncompleteFor(TaskAssignment $assignment): void
    {
        $this->completions()->where('task_assignment_id', $assignment->id)->delete();

        $assignment->task->recalculateProgress();
    }

    /** Steps that gate submission. */
    public function scopeRequired(Builder $query): Builder
    {
        return $query->where('is_required', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }
}
