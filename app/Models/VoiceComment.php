<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class VoiceComment extends Model
{
    protected $fillable = [
        'voice_id',
        'user_id',
        'parent_id',
        'root_id',
        'author_name',
        'body',
        'status',
    ];

    public function voice(): BelongsTo
    {
        return $this->belongsTo(Voice::class);
    }

    /** The account behind this comment, when there is one. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Who this was a reply to — null for a top-level comment. */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Every reply in this thread, however deep the chain actually went.
     *
     * Keyed on `root_id`, not `parent_id`: the client renders two levels and
     * flattens the rest, so it wants one flat list of everything under a root
     * rather than a tree it has to walk.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'root_id')->oldest();
    }

    /** Top-level comments only. */
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('root_id');
    }

    /**
     * Whether this comment can be removed by the given user.
     *
     * The author, the person whose finding it is, or a moderator. The author of
     * the finding is included deliberately: it is their post, and making them
     * wait for staff to clear abuse from it is the wrong answer.
     *
     * An anonymous comment (no `user_id`) has no author to match, so only the
     * finding's owner or a moderator can remove it.
     */
    public function isRemovableBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return ($this->user_id !== null && $this->user_id === $user->id)
            || $this->voice?->user_id === $user->id
            || $user->can('delete_any_voice_comment');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function getAuthorInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->author_name)) ?: [];
        $initials = collect($parts)->take(2)->map(fn ($p) => Str::upper(Str::substr($p, 0, 1)))->implode('');

        return $initials !== '' ? $initials : 'V';
    }

    public function getAuthorColorAttribute(): string
    {
        $palette = ['#16a34a', '#0d9488', '#0891b2', '#7c3aed', '#db2777', '#ea580c', '#ca8a04', '#4f46e5'];

        return $palette[crc32((string) $this->author_name) % count($palette)];
    }
}
