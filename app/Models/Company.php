<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'website',
        'industry',
        'location',
        'about',
        'is_verified',
        'is_active',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Company $company) {
            if (empty($company->slug)) {
                $base = Str::slug($company->name) ?: 'company';
                $slug = $base;
                $i = 2;
                while (static::query()->where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $company->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(JobPosting::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Public URL to the uploaded logo, or null if none was uploaded.
     * Uses asset('storage/...') to match the rest of the site (and the
     * current host), rather than the configured APP_URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        if (str_starts_with($this->logo_path, 'http')) {
            return $this->logo_path;
        }

        return asset('storage/' . $this->logo_path);
    }

    /**
     * Up to two uppercase initials from the company name, for the
     * placeholder shown when there is no logo.
     */
    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $words = array_values(array_filter($words));

        if (empty($words)) {
            return '?';
        }

        $first = mb_substr($words[0], 0, 1);
        $second = isset($words[1]) ? mb_substr($words[1], 0, 1) : '';

        return mb_strtoupper($first . $second);
    }
}
