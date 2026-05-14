<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UpcomingEvent extends Model
{
    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'province',
        'images',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('date', '>=', now()->toDateString());
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(InvolvementRequest::class, 'upcoming_event_id');
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date ? $this->date->format('F j, Y') : '';
    }
}
