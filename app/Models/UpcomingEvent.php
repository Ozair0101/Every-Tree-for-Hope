<?php

namespace App\Models;

use App\Models\Concerns\HasTreeSpecies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class UpcomingEvent extends Model
{
    use HasTranslations;
    use HasTreeSpecies;

    /**
     * Attributes stored as {"en": "...", "fa": "...", "ps": "..."} JSON.
     * Reading $event->title returns the value for the current app locale,
     * falling back to English when a translation is missing.
     */
    public array $translatable = ['title', 'description'];

    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'province',
        'map_embed',
        'tree_names',
        'custom_tree_species',
        'images',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'tree_names' => 'array',
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
