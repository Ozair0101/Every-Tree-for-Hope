<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donator extends Model
{
    protected $fillable = [
        'full_name',
        'impact',
        'location',
        'financial_support',
        'location_type',
        'status',
        'email',
        'phone',
        'profile_image',
        'notes',
        'donation_date',
        'trees_sponsored',
        'is_featured',
    ];

    protected $casts = [
        'financial_support' => 'decimal:2',
        'donation_date' => 'date',
        'trees_sponsored' => 'integer',
        'is_featured' => 'boolean',
    ];

    /**
     * Get verified donators only
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Get featured donators only
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Format financial support with currency
     */
    public function getFormattedFinancialSupportAttribute()
    {
        return '$' . number_format($this->financial_support, 2);
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $color = $this->status === 'verified' ? 'green' : 'yellow';
        $text = ucfirst($this->status);
        
        return "<span class='bg-{$color}-100 text-{$color}-800 text-xs font-semibold px-2.5 py-0.5 rounded'>{$text}</span>";
    }
}
