<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VoiceComment extends Model
{
    protected $fillable = [
        'voice_id',
        'author_name',
        'body',
        'status',
    ];

    public function voice(): BelongsTo
    {
        return $this->belongsTo(Voice::class);
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
