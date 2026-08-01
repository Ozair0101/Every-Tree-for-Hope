<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One photograph on a {@see TreeUpdate} progress entry.
 */
class TreeUpdateImage extends Model
{
    protected $fillable = [
        'tree_update_id',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function treeUpdate(): BelongsTo
    {
        return $this->belongsTo(TreeUpdate::class, 'tree_update_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }
}
