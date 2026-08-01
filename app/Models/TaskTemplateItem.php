<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One checklist step in a template, copied into every generated task.
 *
 * Copied rather than referenced: editing a template must not silently change
 * the definition of work volunteers already accepted.
 */
class TaskTemplateItem extends Model
{
    protected $fillable = [
        'task_template_id',
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

    public function template(): BelongsTo
    {
        return $this->belongsTo(TaskTemplate::class, 'task_template_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    /** @return array<string, mixed> the checklist row to insert on the new task. */
    public function toChecklistAttributes(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'is_required' => $this->is_required,
            'requires_photo' => $this->requires_photo,
            'position' => $this->position,
        ];
    }
}
