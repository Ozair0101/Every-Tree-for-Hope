<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The task module's in-app inbox: what the user should SEE.
 *
 * Separate from the app's existing Laravel `notifications` table, which is live
 * and already read by the mobile client through `$user->notifications()` for
 * tree and voice moderation. Reshaping that would break a shipped screen; this
 * is the task inbox, in the flat shape the task screen actually wants.
 *
 * Separate again from {@see PushNotification}, which is *delivery*. One row here
 * fans out to one delivery row per registered device, linked by
 * `push_notifications.task_notification_id`. Delivery fails in ways an inbox
 * should never inherit — a dead FCM token must not make a notification vanish
 * from the user's list.
 */
class TaskNotification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'type',
        'task_id',
        'task_assignment_id',
        'is_read',
        'read_at',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    protected $attributes = [
        'is_read' => false,
    ];

    /**
     * Keep `is_read` and `read_at` in lockstep.
     *
     * The two are redundant on purpose — the unread badge is the most-run query
     * in the app and a boolean leads a composite index far better than a
     * nullable timestamp — but redundancy is only safe if it cannot drift, so
     * one is always derived from the other here rather than at each call site.
     */
    protected static function booted(): void
    {
        static::saving(function (self $notification) {
            if ($notification->is_read && $notification->read_at === null) {
                $notification->read_at = now();
            }

            if (! $notification->is_read) {
                $notification->read_at = null;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TaskAssignment::class, 'task_assignment_id');
    }

    /** The delivery attempts this notification produced, one per device. */
    public function deliveries(): HasMany
    {
        return $this->hasMany(PushNotification::class);
    }

    public function markAsRead(): bool
    {
        if ($this->is_read) {
            return false;
        }

        return $this->forceFill(['is_read' => true, 'read_at' => now()])->save();
    }

    public function markAsUnread(): bool
    {
        return $this->forceFill(['is_read' => false, 'read_at' => null])->save();
    }

    /**
     * Compose an inbox entry for a task event.
     *
     * Deliberately does not send anything — queuing the push is the caller's
     * job, so the inbox stays correct even when FCM is unreachable.
     *
     * Named `notify()` rather than the more obvious `push()`, which is already
     * a non-static Eloquent method for saving a model with its relations.
     */
    public static function notify(
        User $user,
        string $type,
        string $title,
        string $body,
        ?Task $task = null,
        ?TaskAssignment $assignment = null,
        array $data = [],
    ): self {
        return static::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'task_id' => $task?->id,
            'task_assignment_id' => $assignment?->id,
            'data' => $data ?: null,
        ]);
    }

    /** The badge count. */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /** The inbox, newest first. */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }
}
