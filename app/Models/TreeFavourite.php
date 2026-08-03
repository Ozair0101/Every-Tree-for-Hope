<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One person's save of one post.
 *
 * Private by design: a favourite is a bookmark for the reader, not a signal
 * about the tree, so nothing here is ever counted or shown publicly.
 */
class TreeFavourite extends Model
{
    protected $fillable = ['user_id', 'tree_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tree(): BelongsTo
    {
        return $this->belongsTo(Tree::class);
    }
}
