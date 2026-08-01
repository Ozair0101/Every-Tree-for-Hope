<?php

namespace App\Models;

use App\Models\Concerns\HasClientUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An interim "I am partway" report from the field.
 *
 * Distinct from {@see TaskSubmission}, which says "I am finished, review this".
 * A progress update is never reviewed, can be sent twenty times in a day, and is
 * what makes a long task visible to the office while it is still running —
 * watering 200 saplings across three days would otherwise be silent until it was
 * over.
 *
 * Each row also carries where the volunteer was, so the sequence forms a trail
 * the coordinator can plot on the same map as the trees.
 */
class TaskProgress extends Model
{
    use HasClientUuid;

    /** Laravel would guess `task_progresses`. */
    protected $table = 'task_progress';

    protected $perPage = 30;

    protected $fillable = [
        // Client-generated idempotency key, so a retry after a dropped response
        // does not skew the progress roll-up with a duplicate report.
        'client_uuid',
        'task_assignment_id',
        'task_id',
        'progress_percentage',
        'note',
        'latitude',
        'longitude',
        'gps_accuracy',
        'created_by',
    ];

    protected $casts = [
        'progress_percentage' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'gps_accuracy' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $progress) {
            // Clamp rather than reject: a client sending 105 means "done", and
            // failing the request would lose a field report over a rounding bug.
            $progress->progress_percentage = max(0, min(100, (int) $progress->progress_percentage));

            // Keep the denormalised task_id honest — it exists so the task
            // timeline needs no join, and a wrong value would be worse than none.
            if (blank($progress->task_id) && $progress->task_assignment_id) {
                $progress->task_id = TaskAssignment::whereKey($progress->task_assignment_id)->value('task_id');
            }
        });
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TaskAssignment::class, 'task_assignment_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function hasLocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /** Newest first — how the app renders the progress trail. */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    public function scopeWithLocation(Builder $query): Builder
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }
}
