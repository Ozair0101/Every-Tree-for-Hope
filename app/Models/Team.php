<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'position',
        'image',
        'social_media_url',
        'email',
        'bio',
        'message',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active team members
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    /**
     * Get the full image URL
     */
    public function getFullImageUrlAttribute()
    {
        if ($this->image) {
            // If it's already a full URL (starts with http), return as is
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }
            // Otherwise, treat as stored file path
            return asset('storage/' . $this->image);
        }
        return asset('placeholder-user.jpg');
    }
}
