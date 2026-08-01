<?php

namespace App\Models;

use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A reusable blueprint for a task, with its checklist.
 *
 * Tree maintenance is repetitive by nature — water every third day through
 * summer, inspect monthly, prune each spring. A template is a partial task:
 * everything except the dates and the exact assignees, which the recurrence
 * supplies at generation time.
 */
class TaskTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'task_category_id',
        'title_template',
        'task_description',
        'instructions',
        'priority',
        'estimated_hours',
        'radius',
        'requires_photo',
        'requires_geo_check',
        'requires_review',
        'duration_days',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'priority' => TaskPriority::class,
        'estimated_hours' => 'decimal:2',
        'radius' => 'integer',
        'requires_photo' => 'boolean',
        'requires_geo_check' => 'boolean',
        'requires_review' => 'boolean',
        'duration_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TaskTemplateItem::class)->orderBy('position');
    }

    public function recurrences(): HasMany
    {
        return $this->hasMany(TaskRecurrence::class);
    }

    /** Tasks stamped out from this blueprint. */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * The attribute payload for a task generated from this template.
     *
     * Returns an array rather than a saved model so the caller controls the
     * transaction that also creates the checklist and the assignments.
     *
     * @return array<string, mixed>
     */
    public function toTaskAttributes(\DateTimeInterface $startDate): array
    {
        return [
            'task_category_id' => $this->task_category_id,
            'task_template_id' => $this->id,
            'title' => str_replace(
                [':date', ':month', ':year'],
                [$startDate->format('d M Y'), $startDate->format('F'), $startDate->format('Y')],
                $this->title_template,
            ),
            'description' => $this->task_description,
            'instructions' => $this->instructions,
            'priority' => $this->priority->value,
            'estimated_hours' => $this->estimated_hours,
            'radius' => $this->radius,
            'requires_photo' => $this->requires_photo,
            'requires_geo_check' => $this->requires_geo_check,
            'requires_review' => $this->requires_review,
            'start_date' => $startDate,
            'due_date' => (clone $startDate)->modify("+{$this->duration_days} days"),
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
