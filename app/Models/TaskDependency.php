<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * "Task A cannot start until task B finishes."
 *
 * Sequencing matters in the field: pits are dug before saplings arrive, and
 * watering rounds follow planting. The API refuses to start a task whose
 * dependencies are unmet.
 *
 * Cycles and self-references are rejected in the application layer — MySQL has
 * no way to express a recursive constraint.
 */
class TaskDependency extends Model
{
    protected $fillable = [
        'task_id',
        'depends_on_task_id',
        'type',
    ];

    public const TYPE_FINISH_TO_START = 'finish_to_start';

    public const TYPE_START_TO_START = 'start_to_start';

    protected $attributes = [
        'type' => self::TYPE_FINISH_TO_START,
    ];

    /** The blocked task. */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /** The task that must finish first. */
    public function dependsOn(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'depends_on_task_id');
    }

    /**
     * Is the blocker finished?
     *
     * `finish_to_start` needs the blocker approved; `start_to_start` only needs
     * it under way, which is how parallel field work is sequenced — the water
     * truck can set off once digging has begun, not once it has finished.
     */
    public function isSatisfied(): bool
    {
        $blocker = $this->dependsOn;

        if ($blocker === null) {
            return true;
        }

        return match ($this->type) {
            self::TYPE_START_TO_START => ! in_array(
                $blocker->status,
                [TaskStatus::DRAFT, TaskStatus::ASSIGNED, TaskStatus::CANCELLED],
                strict: true,
            ),
            default => $blocker->status === TaskStatus::APPROVED,
        };
    }

    /**
     * Would adding this edge create a cycle?
     *
     * MySQL cannot express a recursive CHECK, so the guard lives here. Walks the
     * blocker's own dependencies breadth-first with a visited set, which also
     * makes it safe to run against a graph that is already cyclic.
     */
    public static function wouldCycle(int $taskId, int $dependsOnTaskId): bool
    {
        if ($taskId === $dependsOnTaskId) {
            return true;
        }

        $seen = [];
        $frontier = [$dependsOnTaskId];

        while ($frontier !== []) {
            $next = static::query()
                ->whereIn('task_id', $frontier)
                ->pluck('depends_on_task_id')
                ->all();

            if (in_array($taskId, $next, strict: true)) {
                return true;
            }

            $seen = array_merge($seen, $frontier);
            $frontier = array_values(array_diff(array_unique($next), $seen));
        }

        return false;
    }

    public function scopeBlocking(Builder $query, int $taskId): Builder
    {
        return $query->where('depends_on_task_id', $taskId);
    }
}
