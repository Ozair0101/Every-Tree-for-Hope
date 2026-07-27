<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One device registered to receive push notifications.
 *
 * The single device registry for the whole app: the Expo channel sends through
 * it, and the task module's delivery log points at it. A second registry would
 * mean two registration endpoints, two cleanup jobs, and an app left guessing
 * which one a given device is in.
 *
 * The token is unique across the whole table, not per user: a physical device
 * that a second person signs into hands the same token to a new account, and
 * that row must MOVE rather than duplicate — otherwise the previous user keeps
 * receiving the new user's notifications on a phone that is no longer theirs.
 *
 * @see \App\Notifications\Channels\ExpoPushChannel
 */
class PushToken extends Model
{
    /** Consecutive failures after which a token stops being worth a send. */
    public const FAILURE_THRESHOLD = 3;

    public const PLATFORM_ANDROID = 'android';

    public const PLATFORM_IOS = 'ios';

    public const PLATFORM_WEB = 'web';

    protected $fillable = [
        'user_id',
        'token',
        'platform',
        'device_id',
        'device_name',
        'app_version',
        'os_version',
        'locale',
        'is_active',
        'last_used_at',
        'failure_count',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'is_active' => 'boolean',
        'failure_count' => 'integer',
    ];

    protected $attributes = [
        'is_active' => true,
        'failure_count' => 0,
        'locale' => 'en',
    ];

    /** Never leak a push token through an API resource. */
    protected $hidden = ['token'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Deliveries attempted against this device. */
    public function pushNotifications(): HasMany
    {
        return $this->hasMany(PushNotification::class);
    }

    /** Expo's own format check, so obvious rubbish never reaches their API. */
    public static function looksValid(string $token): bool
    {
        return (bool) preg_match('/^Expo(nent)?PushToken\[.+\]$/', $token);
    }

    /**
     * Register or refresh a token for a user.
     *
     * Keyed on the token, so a re-register from the same install is an upsert
     * and a shared phone re-keys to whoever is signed in now. The client should
     * call this on every launch and after every token refresh.
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function register(User $user, string $token, array $attributes = []): self
    {
        return static::updateOrCreate(
            ['token' => $token],
            array_merge($attributes, [
                'user_id' => $user->id,
                'is_active' => true,
                'failure_count' => 0,
                'last_used_at' => now(),
            ]),
        );
    }

    /**
     * Record a delivery failure; retire the token once it is clearly dead.
     *
     * Deactivates rather than deletes. `push_notifications` rows point here, and
     * a delivery history that vanishes when someone wipes their phone is not a
     * delivery history.
     */
    public function recordFailure(?string $errorCode = null): void
    {
        $permanentlyDead = in_array(
            $errorCode,
            ['DeviceNotRegistered', 'UNREGISTERED', 'INVALID_ARGUMENT', 'NOT_FOUND'],
            strict: true,
        );

        $this->increment('failure_count');

        if ($permanentlyDead || $this->failure_count >= self::FAILURE_THRESHOLD) {
            $this->forceFill(['is_active' => false])->save();
        }
    }

    public function recordSuccess(): void
    {
        $this->forceFill([
            'failure_count' => 0,
            'last_used_at' => now(),
        ])->saveQuietly();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
