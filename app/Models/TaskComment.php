<?php

namespace App\Models;

use App\Models\Concerns\HasTaskAttachments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Discussion on a task — questions from the field, clarifications from the office.
 *
 * `is_internal` marks a staff-only note. The API filters these out for anyone
 * who is merely an assignee, so reviewers can discuss a weak submission without
 * the volunteer reading it. Enforce that in the resource layer, not just here.
 */
class TaskComment extends Model
{
    use HasTaskAttachments, SoftDeletes;

    protected $fillable = [
        'task_id',
        'user_id',
        'parent_id',
        'body',
        'is_internal',
        'edited_at',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'edited_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->oldest();
    }

    /** What a volunteer is allowed to see. */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_internal', false);
    }
}
