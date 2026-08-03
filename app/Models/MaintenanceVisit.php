<?php

namespace App\Models;

use App\Models\Concerns\HasClientUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A follow-up inspection of a planting event.
 *
 * Moderated like {@see Tree}: created `pending`, and only once `approved` is it
 * allowed to move the parent event's survival numbers. Captured in the field,
 * so it carries a client-generated {@see HasClientUuid} key for safe offline
 * replay.
 */
class MaintenanceVisit extends Model
{
    use HasClientUuid;

    protected $fillable = [
        'client_uuid',
        'event_id',
        'user_id',
        'visit_date',
        'trees_checked',
        'trees_lost',
        'notes',
        'latitude',
        'longitude',
        'gps_accuracy',
        'address',
        'is_mocked',
        'status',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'trees_checked' => 'integer',
        'trees_lost' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'gps_accuracy' => 'integer',
        'is_mocked' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public const STATUSES = ['pending', 'approved', 'rejected'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(MaintenanceVisitImage::class)->orderBy('sort_order');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeNewest(Builder $query): Builder
    {
        return $query->orderByDesc('visit_date')->orderByDesc('id');
    }
}
