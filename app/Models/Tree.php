<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A tree a volunteer planted and recorded in the field.
 *
 * Moderated like {@see Voice}: created `pending`, made visible on the planter's
 * profile, the public list and the map only once `status` is `approved`.
 */
class Tree extends Model
{
    protected $fillable = [
        'user_id',
        'species',
        'notes',
        'location_name',
        'latitude',
        'longitude',
        'gps_accuracy',
        'planted_on',
        'image_path',
        'status',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'gps_accuracy' => 'integer',
        'planted_on' => 'date',
        'approved_at' => 'datetime',
    ];

    public const STATUSES = ['pending', 'approved', 'rejected'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(TreeUpdate::class)->latest();
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    /** Newest first, matching how the app lists them. */
    public function scopeNewest(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
