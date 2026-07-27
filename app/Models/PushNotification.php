<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Outbox and delivery log: one row per push attempted, per device.
 *
 * Separate from Laravel's `notifications` table, which stays the record of what
 * the user should *see* in the in-app bell. This table is about *delivery*,
 * which fails in ways the inbox should never inherit: tokens expire, apps get
 * uninstalled, FCM rate-limits and answers per-token. One logical notification
 * to a user with three devices is three rows here and one row there.
 *
 * Rows are written `queued` and picked up by a worker, so an FCM outage delays
 * pushes instead of losing them.
 */
class PushNotification extends Model
{
    /**
     * Delivery rows are bulk-pruned; there is nothing to unlink or cascade.
     *
     * One row per device per event means this table grows faster than the number
     * of notifications — a user with three phones triples it. Register
     * `model:prune` on the scheduler.
     */
    use MassPrunable;

    /**
     * A delivered push is a receipt, not a record — the inbox row in
     * `task_notifications` is what the user keeps. A month is enough to
     * investigate "why did I not get notified?" while it is still being asked.
     */
    public const SENT_RETENTION_DAYS = 30;

    /**
     * Failures are kept far longer: they are the evidence for a systematic
     * delivery problem, which is only ever visible over months.
     */
    public const FAILED_RETENTION_DAYS = 180;

    protected $perPage = 50;

    public const STATUS_QUEUED = 'queued';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    public const STATUS_SKIPPED = 'skipped'; // opted out, or no active device

    protected $fillable = [
        'user_id',
        'push_token_id',
        'task_notification_id',
        'related_type',
        'related_id',
        'event_key',
        'title',
        'body',
        'data',
        'status',
        'fcm_message_id',
        'error_code',
        'error_message',
        'attempts',
        'sent_at',
    ];

    protected $casts = [
        'data' => 'array',
        'attempts' => 'integer',
        'sent_at' => 'datetime',
    ];

    /**
     * Mirror the column defaults in memory.
     *
     * Without this a freshly created row reads back `status === null` until it
     * is reloaded, because the default is applied by MySQL and never travels
     * back to the model. The worker inspects the object it just created, so the
     * two must agree.
     */
    protected $attributes = [
        'status' => self::STATUS_QUEUED,
        'attempts' => 0,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** The device this delivery was aimed at. */
    public function pushToken(): BelongsTo
    {
        return $this->belongsTo(PushToken::class);
    }

    /**
     * The inbox entry this delivery is carrying.
     *
     * One notification the user should see fans out to one row here per device
     * they have registered. Keeping them apart means a dead FCM token cannot
     * make a notification vanish from the user's list.
     */
    public function taskNotification(): BelongsTo
    {
        return $this->belongsTo(TaskNotification::class);
    }

    /** Usually a Task; sometimes a TaskSubmission or TaskComment. */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function markSent(?string $messageId = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_SENT,
            'fcm_message_id' => $messageId,
            'sent_at' => now(),
            'attempts' => $this->attempts + 1,
        ])->save();
    }

    public function markFailed(?string $code = null, ?string $message = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_FAILED,
            'error_code' => $code,
            'error_message' => $message,
            'attempts' => $this->attempts + 1,
        ])->save();
    }

    /**
     * What `model:prune` may delete.
     *
     * Queued rows are never pruned no matter how old: one still sitting in the
     * queue after a month is a stuck worker, and deleting the evidence would
     * hide the bug rather than fix it.
     */
    public function prunable(): Builder
    {
        // The two branches are wrapped in an outer group deliberately. Left at
        // the top level, `A OR B` would silently mis-scope the moment a caller
        // composed onto it — `prunable()->whereKey($id)` binds the AND to the
        // second branch only and matches rows it was never asked about. An
        // ungrouped OR in a reusable query builder is a bug waiting for its
        // first caller.
        return static::query()->where(function (Builder $outer) {
            $outer
                ->where(fn (Builder $q) => $q
                    ->whereIn('status', [self::STATUS_SENT, self::STATUS_SKIPPED])
                    ->where('created_at', '<', now()->subDays(self::SENT_RETENTION_DAYS)))
                ->orWhere(fn (Builder $q) => $q
                    ->where('status', self::STATUS_FAILED)
                    ->where('created_at', '<', now()->subDays(self::FAILED_RETENTION_DAYS)));
        });
    }

    /** The worker's queue: oldest unsent first. */
    public function scopeQueued(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_QUEUED)->oldest();
    }

    /**
     * Has this exact event already gone out for this subject recently?
     *
     * Guards against a reminder job that runs twice, or a status flapping
     * between two values, turning into a burst of identical pushes.
     */
    public static function recentlySent(int $userId, string $eventKey, string $relatedType, int $relatedId, int $withinMinutes = 60): bool
    {
        return static::query()
            ->where('user_id', $userId)
            ->where('event_key', $eventKey)
            ->where('related_type', $relatedType)
            ->where('related_id', $relatedId)
            ->where('created_at', '>=', now()->subMinutes($withinMinutes))
            ->whereIn('status', [self::STATUS_QUEUED, self::STATUS_SENT])
            ->exists();
    }
}
