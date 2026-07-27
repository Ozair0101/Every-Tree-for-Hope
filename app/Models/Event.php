<?php

namespace App\Models;

use App\Models\Concerns\HasTreeSpecies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasTreeSpecies;

    protected $fillable = [
        'title',
        'description',
        'location',
        'province',
        'tree_names',
        'event_type',
        'video_url',
        'custom_tree_species',
        'date',
        'trees_planted',
        'trees_lost',
        'last_maintained_at',
        'maintenance_notes',
        'maintenance_visits',
        'maintenance_photos',
        'volunteers',
        'volunteer_names',
        'map_embed',
        'sponsor_partner',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'date' => 'date',
        'last_maintained_at' => 'date',
        'tree_names' => 'array',
        'volunteer_names' => 'array',
        'maintenance_visits' => 'array',
        'maintenance_photos' => 'array',
        'trees_planted' => 'integer',
        'trees_lost' => 'integer',
        'volunteers' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getTreesAliveAttribute(): int
    {
        return max(0, (int) ($this->trees_planted ?? 0) - (int) ($this->trees_lost ?? 0));
    }

    public function getSurvivalRateAttribute(): ?float
    {
        $planted = (int) ($this->trees_planted ?? 0);
        if ($planted <= 0) {
            return null;
        }

        return round((($planted - (int) ($this->trees_lost ?? 0)) / $planted) * 100, 1);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Get the images for the event
     */
    public function images(): HasMany
    {
        return $this->hasMany(EventImage::class)->orderBy('sort_order');
    }

    /**
     * Donators who sponsored this event.
     */
    public function donators(): BelongsToMany
    {
        return $this->belongsToMany(Donator::class, 'event_donator')->withTimestamps();
    }

    /**
     * Partners who sponsored this event.
     */
    public function partners(): BelongsToMany
    {
        return $this->belongsToMany(Partner::class, 'event_partner')->withTimestamps();
    }

    /**
     * Scope to get only active events
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by date (newest first)
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('date', 'desc');
    }

    /**
     * Scope to order by sort order and date
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('date', 'desc');
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    /**
     * Get total impact metrics
     */
    public function getTotalTreesPlantedAttribute()
    {
        return $this->trees_planted;
    }

    /**
     * Get total volunteers
     */
    public function getTotalVolunteersAttribute()
    {
        return $this->volunteers;
    }
}
