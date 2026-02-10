<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'company_name',
        'logo',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to get only active partners
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order and company name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('company_name');
    }

    /**
     * Get the full logo URL
     */
    public function getFullLogoUrlAttribute()
    {
        if ($this->logo) {
            // If it's already a full URL (starts with http), return as is
            if (str_starts_with($this->logo, 'http')) {
                return $this->logo;
            }
            // Otherwise, treat as stored file path
            return asset('storage/' . $this->logo);
        }
        return asset('placeholder-logo.png');
    }
}
