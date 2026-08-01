<?php

namespace App\Models;

use App\Enums\TaskSubmissionStatus;
use App\Models\Concerns\HasClientUuid;
use App\Models\Concerns\HasTaskAttachments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * One attempt at completing a task, with the proof that came with it.
 *
 * Immutable once reviewed: a rejected attempt stays on the record and the
 * volunteer's retry becomes attempt 2. That history is the point — it is how a
 * coordinator later answers "was this site actually visited in June?".
 */
class TaskSubmission extends Model
{
    /** Proof photos, videos and voice notes for this attempt. */
    use HasClientUuid, HasTaskAttachments;

    protected $perPage = 20;

    protected $fillable = [
        // Client-generated idempotency key. Fillable because the device supplies
        // it; the unique index is what makes it safe.
        'client_uuid',
        'task_id',
        'task_assignment_id',
        'user_id',
        'attempt',
        'note',
        'hours_spent',
        'latitude',
        'longitude',
        'gps_accuracy',
        'address',
        'is_mocked',
        'verification',
        'distance_meters',
        'is_within_geofence',
        'device_captured_at',
        'status',
    ];

    protected $casts = [
        'attempt' => 'integer',
        'hours_spent' => 'decimal:2',
        'latitude' => 'float',
        'longitude' => 'float',
        'gps_accuracy' => 'integer',
        'distance_meters' => 'integer',
        'is_within_geofence' => 'boolean',
        'is_mocked' => 'boolean',
        'verification' => 'array',
        'device_captured_at' => 'datetime',
        'status' => TaskSubmissionStatus::class,
    ];

    /** Mirror the column defaults so a new submission is usable before reload. */
    protected $attributes = [
        'status' => TaskSubmissionStatus::PENDING->value,
        'attempt' => 1,
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
     * Every verdict passed on this attempt, newest first.
     *
     * Plural because the same attempt can be reviewed more than once — a second
     * opinion, or an appeal after the volunteer sends better photos.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(TaskReview::class)->orderByDesc('reviewed_at');
    }

    /** The verdict that currently stands. */
    public function latestReview(): HasOne
    {
        return $this->hasOne(TaskReview::class)->latestOfMany('reviewed_at');
    }

    /** Who reviewed it last — reads through the review, not a duplicated column. */
    public function reviewer(): ?User
    {
        return $this->latestReview?->reviewer;
    }

    /**
     * Stamp the geofence verdict from the coordinates the device reported.
     *
     * Computed once here, at write time, so the review queue never runs
     * trigonometry over thousands of rows. Outside the fence is a flag, not a
     * rejection — rural GPS drifts and the reviewer decides.
     */
    public function evaluateGeofence(Task $task): void
    {
        if ($this->latitude === null || $this->longitude === null) {
            return;
        }

        $distance = $task->distanceTo($this->latitude, $this->longitude);

        $this->distance_meters = $distance === null ? null : (int) round($distance);
        $this->is_within_geofence = $task->isWithinGeofence($this->latitude, $this->longitude);
    }

    /** The reviewer queue: oldest pending first. */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', TaskSubmissionStatus::PENDING->value)->oldest();
    }

    /** Submissions logged away from where the work was meant to happen. */
    public function scopeSuspicious(Builder $query): Builder
    {
        return $query->where('is_within_geofence', false);
    }

    /**
     * Anything a reviewer should look at twice.
     *
     * Wider than `suspicious`: a reading inside the fence can still carry a
     * mock-provider flag, and that is the more serious of the two.
     */
    public function scopeNeedsScrutiny(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q
            ->where('is_within_geofence', false)
            ->orWhere('is_mocked', true)
            ->orWhereNotNull('verification'));
    }

    /** Did the device admit the location was faked? */
    public function wasMocked(): bool
    {
        return $this->is_mocked === true;
    }

    /**
     * The worst thing found about this reading, or null if nothing was.
     *
     * `high` currently means only one thing — the device reported a mock
     * provider — and it is the one signal worth treating as near-conclusive.
     */
    public function verificationSeverity(): ?string
    {
        foreach (['high', 'medium', 'low'] as $level) {
            foreach ($this->verification ?? [] as $finding) {
                if (($finding['severity'] ?? null) === $level) {
                    return $level;
                }
            }
        }

        return null;
    }
}
