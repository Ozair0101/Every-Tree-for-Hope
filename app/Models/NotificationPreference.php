<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A user's opt-out from one kind of notification on one channel.
 *
 * Only opt-outs are stored — the absence of a row means "enabled". A row per
 * user per event per channel would be tens of thousands of rows that all say
 * "yes"; this way the table stays proportional to how many people actually
 * changed something.
 */
class NotificationPreference extends Model
{
    public const CHANNEL_PUSH = 'push';

    public const CHANNEL_EMAIL = 'email';

    public const CHANNEL_DATABASE = 'database';

    /** The event keys the task module emits. */
    public const EVENT_TASK_ASSIGNED = 'task.assigned';

    public const EVENT_TASK_UPDATED = 'task.updated';

    public const EVENT_TASK_DUE_SOON = 'task.due_soon';

    public const EVENT_TASK_OVERDUE = 'task.overdue';

    public const EVENT_TASK_SUBMITTED = 'task.submitted';

    public const EVENT_TASK_APPROVED = 'task.approved';

    public const EVENT_TASK_REJECTED = 'task.rejected';

    public const EVENT_TASK_COMMENTED = 'task.commented';

    public const EVENT_TASK_CANCELLED = 'task.cancelled';

    protected $fillable = [
        'user_id',
        'channel',
        'event_key',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * May we send this user this event on this channel?
     *
     * Defaults to true when no row exists.
     */
    public static function allows(int $userId, string $channel, string $eventKey): bool
    {
        $preference = static::query()
            ->where('user_id', $userId)
            ->where('channel', $channel)
            ->where('event_key', $eventKey)
            ->value('enabled');

        return $preference === null || (bool) $preference;
    }

    /**
     * Bulk-resolve preferences for a fan-out.
     *
     * Notifying thirty assignees must not mean thirty `allows()` queries. One
     * query returns only the opt-outs — absence means enabled — so the caller
     * filters an in-memory set.
     *
     * @param  array<int, int>  $userIds
     * @return array<int, bool> user id => may we send
     */
    public static function allowsMany(array $userIds, string $channel, string $eventKey): array
    {
        $optedOut = static::query()
            ->whereIn('user_id', $userIds)
            ->where('channel', $channel)
            ->where('event_key', $eventKey)
            ->where('enabled', false)
            ->pluck('user_id')
            ->flip();

        return collect($userIds)
            ->mapWithKeys(fn (int $id) => [$id => ! $optedOut->has($id)])
            ->all();
    }

    /** Turn one event off for a user, creating the opt-out row on demand. */
    public static function optOut(int $userId, string $channel, string $eventKey): self
    {
        return static::updateOrCreate(
            ['user_id' => $userId, 'channel' => $channel, 'event_key' => $eventKey],
            ['enabled' => false],
        );
    }

    /**
     * Turn one event back on.
     *
     * Deletes the row rather than setting `enabled = true`: absence already
     * means enabled, and keeping a row that says so would grow the table for
     * every preference a user ever toggled twice.
     */
    public static function optIn(int $userId, string $channel, string $eventKey): void
    {
        static::query()
            ->where('user_id', $userId)
            ->where('channel', $channel)
            ->where('event_key', $eventKey)
            ->delete();
    }

    public function scopeForChannel(Builder $query, string $channel): Builder
    {
        return $query->where('channel', $channel);
    }

    public function scopeDisabled(Builder $query): Builder
    {
        return $query->where('enabled', false);
    }

    /** @return array<int, string> every event key the module can emit. */
    public static function eventKeys(): array
    {
        return [
            self::EVENT_TASK_ASSIGNED,
            self::EVENT_TASK_UPDATED,
            self::EVENT_TASK_DUE_SOON,
            self::EVENT_TASK_OVERDUE,
            self::EVENT_TASK_SUBMITTED,
            self::EVENT_TASK_APPROVED,
            self::EVENT_TASK_REJECTED,
            self::EVENT_TASK_COMMENTED,
            self::EVENT_TASK_CANCELLED,
        ];
    }
}
