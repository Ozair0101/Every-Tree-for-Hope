<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A progress entry on a {@see Tree} — a follow-up note and optional photo the
 * planter adds over time to show the tree growing. These feed the platform's
 * broader idea of survival/progress tracking.
 */
class TreeUpdate extends Model
{
    protected $fillable = [
        'tree_id',
        'note',
        'image_path',
        'height_cm',
    ];

    protected $casts = [
        'height_cm' => 'integer',
    ];

    /** How many photographs a single progress entry may carry. */
    public const MAX_IMAGES = 6;

    public function tree(): BelongsTo
    {
        return $this->belongsTo(Tree::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(TreeUpdateImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }
}
