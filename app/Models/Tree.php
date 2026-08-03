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
        'after_image_path',
        'after_image_note',
        'after_image_taken_at',
        'after_image_thumbnail_path',
        'after_image_latitude',
        'after_image_longitude',
        'after_image_distance',
        'after_image_device',
        'image_thumbnail_path',
        'address',
        'is_mocked',
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
        'after_image_taken_at' => 'datetime',
        'after_image_latitude' => 'float',
        'after_image_longitude' => 'float',
        'after_image_distance' => 'integer',
        'is_mocked' => 'boolean',
        'shares_count' => 'integer',
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

    /* ══════════════ Gallery ══════════════ */

    public function images(): HasMany
    {
        return $this->hasMany(TreeImage::class)->ordered();
    }

    /** Planting-day photographs. */
    public function beforeImages(): HasMany
    {
        return $this->hasMany(TreeImage::class)->before()->ordered();
    }

    /** Follow-up photographs, taken a season later. */
    public function afterImages(): HasMany
    {
        return $this->hasMany(TreeImage::class)->after()->ordered();
    }

    /* ══════════════ Social ══════════════ */

    public function likes(): HasMany
    {
        return $this->hasMany(TreeLike::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TreeComment::class);
    }

    /**
     * Saves of this post. Private to each saver — unlike likes, there is no
     * public count, so nothing here is ever exposed in aggregate.
     */
    public function favourites(): HasMany
    {
        return $this->hasMany(TreeFavourite::class);
    }

    /** Whether a given user has saved this tree. Null user never has. */
    public function isFavouritedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        // Uses the loaded relation when the feed eager-loaded it, so a page of
        // twenty posts costs one query rather than twenty.
        if ($this->relationLoaded('favourites')) {
            return $this->favourites->contains('user_id', $user->id);
        }

        return $this->favourites()->where('user_id', $user->id)->exists();
    }

    /** Whether a given user has liked this tree. Null user is never a liker. */
    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        // Uses the loaded relation when the feed has eager-loaded it, so a page
        // of twenty posts costs one query rather than twenty.
        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('user_id', $user->id);
        }

        return $this->likes()->where('user_id', $user->id)->exists();
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
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Before and after
    |--------------------------------------------------------------------------
    |
    | `image_path` is the planting photo — the "before". `after_image_path` is
    | the follow-up taken a season later, on the SAME record, so the pair is
    | unambiguous. Two separate rows would leave "which after belongs to which
    | before?" as a guess, which is the whole thing this feature exists to show.
    |
    | The original column keeps its name: renaming a live column would break the
    | mobile app, the admin panel and the public map at once, for nothing.
    */

    public function getAfterImageUrlAttribute(): ?string
    {
        return $this->after_image_path ? asset('storage/'.$this->after_image_path) : null;
    }

    /** Both halves present — the pair can actually be shown side by side. */
    public function hasComparison(): bool
    {
        return filled($this->image_path) && filled($this->after_image_path);
    }

    /**
     * How long the tree has been growing between the two photographs.
     *
     * Measured from `planted_on` rather than `created_at`: a volunteer may
     * record a tree weeks after planting it, and the growth story is about the
     * tree's life, not about when the paperwork was filed.
     */
    public function growthDays(): ?int
    {
        if (! $this->hasComparison() || ! $this->after_image_taken_at) {
            return null;
        }

        return (int) $this->planted_on?->diffInDays($this->after_image_taken_at);
    }

    /** Trees whose planter has posted the follow-up. */
    public function scopeWithComparison(Builder $query): Builder
    {
        return $query->whereNotNull('image_path')->whereNotNull('after_image_path');
    }

    /** Approved trees still waiting for their after photo. */
    public function scopeAwaitingAfterImage(Builder $query): Builder
    {
        return $query->approved()->whereNull('after_image_path');
    }

    /* ══════════════ Cover selection ══════════════ */

    /**
     * Make one image the cover of its phase, and mirror it onto the parent.
     *
     * The mirroring is the point. `trees.image_path` and `after_image_path` are
     * read by the public map, the admin panel, `hasComparison()`, the analytics
     * follow-up rate and every existing mobile list row — none of which know
     * `tree_images` exists. Writing through this one method is what keeps all
     * of them true.
     */
    public function setCover(TreeImage $image): void
    {
        if ($image->tree_id !== $this->id) {
            throw new \InvalidArgumentException('That image belongs to a different tree.');
        }

        // Demote the incumbent first. Scoped to the phase so choosing a new
        // "after" cover does not silently clear the "before" one.
        $this->images()->newQuery()
            ->where('tree_id', $this->id)
            ->where('phase', $image->phase)
            ->where('id', '!=', $image->id)
            ->update(['is_cover' => false]);

        $image->forceFill(['is_cover' => true])->save();

        $this->syncCover($image->phase);
    }

    /**
     * Copy the current cover of a phase onto the denormalised columns.
     *
     * Called after any change to the gallery — an upload, a deletion, a new
     * cover. When a phase has no images left the columns are nulled, which is
     * what makes `awaitingAfterImage()` correct again after the last follow-up
     * photo is deleted.
     */
    public function syncCover(string $phase): void
    {
        $cover = $this->images()->newQuery()
            ->where('tree_id', $this->id)
            ->where('phase', $phase)
            ->orderByDesc('is_cover')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        $columns = $phase === TreeImage::PHASE_AFTER
            ? [
                'after_image_path' => $cover?->path,
                'after_image_thumbnail_path' => $cover?->thumbnail_path,
                'after_image_note' => $cover?->caption,
                'after_image_taken_at' => $cover?->captured_at,
                'after_image_latitude' => $cover?->latitude,
                'after_image_longitude' => $cover?->longitude,
                'after_image_distance' => $cover?->distance_meters,
                'after_image_device' => $cover?->device_model,
            ]
            : [
                'image_path' => $cover?->path,
                'image_thumbnail_path' => $cover?->thumbnail_path,
            ];

        $this->forceFill($columns)->save();

        // The row that survives as cover may not have been flagged as one —
        // after a deletion the fallback is simply the next in order.
        if ($cover && ! $cover->is_cover) {
            $cover->forceFill(['is_cover' => true])->save();
        }
    }

    /**
     * Whether the planter may still add follow-up photographs.
     *
     * Gated on approval because the after-photo is the second half of a public
     * record: until the first half has been accepted there is nothing to
     * compare against, and a rejected planting should not accumulate more.
     */
    public function acceptsAfterImages(): bool
    {
        return $this->status === 'approved';
    }
}
