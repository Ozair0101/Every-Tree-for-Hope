<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SponsorPackage extends Model
{
    protected $fillable = [
        'title',
        'price',
        'currency',
        'trees_count',
        'description',
        'badge_label',
        'allocations',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'trees_count' => 'integer',
        'allocations' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    public function getFormattedPriceAttribute(): string
    {
        $price = (float) $this->price;
        $whole = number_format($price, $price == floor($price) ? 0 : 2);
        $currency = $this->currency ?? 'USD';
        return $currency === 'USD' ? '$' . $whole : $whole . ' ' . $currency;
    }
}
