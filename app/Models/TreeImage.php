<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * One photograph of a tree, in one of its two phases.
 *
 * Stored three times over — original, compressed, thumbnail — for the same
 * reason task attachments are: the compressed copy is what a phone on rural
 * data downloads, and the original is what settles an argument about whether a
 * photo was really taken where it claims.
 */
class TreeImage extends Model
{
    public const PHASE_BEFORE = 'before';

    public const PHASE_AFTER = 'after';

    public const PHASES = [self::PHASE_BEFORE, self::PHASE_AFTER];

    /** How many photographs one phase of one tree may hold. */
    public const MAX_PER_PHASE = 10;

    protected $fillable = [
        'tree_id',
        'phase',
        'path',
        'thumbnail_path',
        'original_path',
        'caption',
        'is_cover',
        'sort_order',
        'latitude',
        'longitude',
        'gps_accuracy',
        'captured_at',
        'device_make',
        'device_model',
        'device_os',
        'app_version',
        'metadata_source',
        'exif',
        'width',
        'height',
        'size_bytes',
        'distance_meters',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
        'sort_order' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'gps_accuracy' => 'integer',
        'captured_at' => 'datetime',
        'exif' => 'array',
        'width' => 'integer',
        'height' => 'integer',
        'size_bytes' => 'integer',
        'distance_meters' => 'integer',
    ];

    public function tree(): BelongsTo
    {
        return $this->belongsTo(Tree::class);
    }

    /* ══════════════ Scopes ══════════════ */

    public function scopeBefore(Builder $query): Builder
    {
        return $query->where('phase', self::PHASE_BEFORE);
    }

    public function scopeAfter(Builder $query): Builder
    {
        return $query->where('phase', self::PHASE_AFTER);
    }

    public function scopePhase(Builder $query, string $phase): Builder
    {
        return $query->where('phase', $phase);
    }

    /** Display order: the cover leads, then whatever the planter arranged. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_cover')->orderBy('sort_order')->orderBy('id');
    }

    /* ══════════════ URLs ══════════════ */

    public function getUrlAttribute(): ?string
    {
        return $this->path ? asset('storage/'.$this->path) : null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        // Falls back to the full image rather than returning null: a list row
        // showing a slightly heavy picture is better than showing a gap, and
        // images backfilled from the old columns have no thumbnail at all.
        return $this->thumbnail_path
            ? asset('storage/'.$this->thumbnail_path)
            : $this->url;
    }

    public function getOriginalUrlAttribute(): ?string
    {
        return $this->original_path ? asset('storage/'.$this->original_path) : null;
    }

    /* ══════════════ Verification ══════════════ */

    /** The photograph carries GPS of its own, read from the file. */
    public function hasLocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Whether the camera itself vouched for this image.
     *
     * Only true when real GPS, a real capture time or a real camera identity
     * was found. Every JPEG carries *some* EXIF, so "has EXIF" proves nothing —
     * this is the distinction that keeps the badge meaningful.
     */
    public function isCameraVerified(): bool
    {
        return $this->metadata_source === 'exif';
    }

    /**
     * Delete the row and every file behind it.
     *
     * All three copies, because deleting only the one that gets served leaves
     * the original and the thumbnail on disk forever — the leak that has to be
     * fixed by hand later.
     */
    public function purge(string $disk = 'public'): void
    {
        $paths = array_filter([$this->path, $this->thumbnail_path, $this->original_path]);

        foreach ($paths as $path) {
            Storage::disk($disk)->delete($path);
        }

        $this->delete();
    }
}
