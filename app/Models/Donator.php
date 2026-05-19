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
        'status',
        'phone',
        'profile_image',
        'donation_date',
    ];

    protected $casts = [
        'financial_support' => 'decimal:2',
        'donation_date' => 'date',
    ];

    /**
     * Get verified donators only
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
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
