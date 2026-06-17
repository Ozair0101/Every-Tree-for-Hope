<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Voice extends Model
{
    protected $fillable = [
        'slug',
        'author_name',
        'author_email',
        'country',
        'category',
        'title',
        'body',
        'image_path',
        'likes_count',
        'comments_count',
        'views_count',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'views_count' => 'integer',
        'is_featured' => 'boolean',
    ];

    /**
     * The kinds of things people share, with the label + the accent used in the UI.
     */
    public const CATEGORIES = [
        'idea' => 'Idea',
        'finding' => 'Finding',
        'experience' => 'Experience',
        'question' => 'Question',
    ];

    protected static function booted(): void
    {
        static::creating(function (Voice $voice) {
            if (empty($voice->slug)) {
                $voice->slug = static::uniqueSlug($voice->title);
            }
        });
    }

    public static function uniqueSlug(string $source): string
    {
        $base = Str::slug(Str::limit($source, 60, '')) ?: 'voice';
        $slug = $base;
        $i = 2;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function comments(): HasMany
    {
        return $this->hasMany(VoiceComment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(VoiceLike::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')->orderByDesc('created_at');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? Str::headline((string) $this->category);
    }

    /**
     * Initials for the author avatar circle.
     */
    public function getAuthorInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->author_name)) ?: [];
        $initials = collect($parts)->take(2)->map(fn ($p) => Str::upper(Str::substr($p, 0, 1)))->implode('');

        return $initials !== '' ? $initials : 'V';
    }

    /**
     * A deterministic accent colour per author, so avatars feel personal.
     */
    public function getAuthorColorAttribute(): string
    {
        $palette = ['#16a34a', '#0d9488', '#0891b2', '#7c3aed', '#db2777', '#ea580c', '#ca8a04', '#4f46e5'];

        return $palette[crc32((string) $this->author_name) % count($palette)];
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
