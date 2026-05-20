<?php

namespace App\Models;

use App\Enums\PartnerType;
use App\Models\Concerns\HasSponsorCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Partner extends Model
{
    use HasSponsorCode;

    protected $fillable = [
        'code',
        'company_name',
        'type',
        'description',
        'logo',
        'is_active',
        'sort_order',
    ];

    public function sponsorCodeSource(): string
    {
        return (string) $this->company_name;
    }

    /**
     * Events this partner sponsored.
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_partner')->withTimestamps();
    }

    protected $casts = [
        'type' => PartnerType::class,
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
