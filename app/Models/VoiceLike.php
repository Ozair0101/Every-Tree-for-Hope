<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoiceLike extends Model
{
    protected $fillable = [
        'voice_id',
        'fingerprint',
    ];

    public function voice(): BelongsTo
    {
        return $this->belongsTo(Voice::class);
    }
}
