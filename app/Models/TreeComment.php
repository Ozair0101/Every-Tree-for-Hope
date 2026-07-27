<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A comment on a planted tree, or a reply to one.
 *
 * Threading is two-level by design — see the migration for why. `parent_id` is
 * the true conversational edge; `root_id` is the thread it belongs to, so a
 * whole conversation loads in one indexed query instead of a recursive walk.
 *
 * Soft-deleted so a removed comment leaves its replies standing. Hard-deleting
 * a comment mid-thread cascades its children away, which reads to everyone else
 * as the replies having been censored.
 */
class TreeComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tree_id',
        'user_id',
        'parent_id',
        'root_id',
        'body',
        'is_hidden',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function tree(): BelongsTo
    {
        return $this->belongsTo(Tree::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Who this was a reply to — null for a top-level comment. */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** Every reply in this thread, however deep the chain actually went. */
    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'root_id')->oldest();
    }

    /* ══════════════ Scopes ══════════════ */

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_hidden', false);
    }

    /** Top-level comments only. */
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('root_id');
    }

    /* ══════════════ Writing ══════════════ */

    /**
     * Post a comment, or a reply to one.
     *
     * The thread is resolved here rather than trusted from the client: a reply
     * to a reply belongs to the *original* thread, and letting the caller name
     * its own `root_id` is how a comment ends up orphaned in a thread it was
     * never part of.
     */
    public static function post(Tree $tree, User $author, string $body, ?self $replyTo = null): self
    {
        return static::create([
            'tree_id' => $tree->id,
            'user_id' => $author->id,
            'parent_id' => $replyTo?->id,
            // A reply to a root joins that root; a reply to a reply joins the
            // same thread rather than starting a third level.
            'root_id' => $replyTo ? ($replyTo->root_id ?? $replyTo->id) : null,
            'body' => trim($body),
        ]);
    }

    /** Whether this comment can be removed by the given user. */
    public function isRemovableBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        // The author, the planter whose tree it is, or a moderator. The planter
        // is included on purpose: it is their record, and asking them to wait
        // for staff to remove abuse from it is the wrong answer.
        return $this->user_id === $user->id
            || $this->tree?->user_id === $user->id
            || $user->can('approve_tree');
    }
}
