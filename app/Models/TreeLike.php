<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One user's like on one tree.
 *
 * A row, not a counter, so "have I liked this?" is answerable and a like can be
 * taken back. The count is derived with `withCount` rather than denormalised —
 * a cached total that drifts from the rows is worse than one extra subquery at
 * this scale.
 */
class TreeLike extends Model
{
    protected $fillable = ['tree_id', 'user_id'];

    public function tree(): BelongsTo
    {
        return $this->belongsTo(Tree::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
