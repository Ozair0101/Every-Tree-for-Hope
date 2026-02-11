<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'location',
        'province',
        'tree_names',
        'custom_tree_species',
        'date',
        'trees_planted',
        'volunteers',
        'sponsor_partner',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'date' => 'date',
        'tree_names' => 'array',
        'trees_planted' => 'integer',
        'volunteers' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the images for the event
     */
    public function images(): HasMany
    {
        return $this->hasMany(EventImage::class)->orderBy('sort_order');
    }

    /**
     * Get all tree species (both checkbox and custom)
     */
    public function getAllTreeSpeciesAttribute(): array
    {
        $species = [];
        
        // Add checkbox selections
        if ($this->tree_names && is_array($this->tree_names)) {
            $species = array_merge($species, $this->tree_names);
        }
        
        // Add custom species
        if ($this->custom_tree_species) {
            $customSpecies = array_map('trim', explode(',', $this->custom_tree_species));
            $customSpecies = array_filter($customSpecies, function($species) {
                return !empty($species);
            });
            $species = array_merge($species, $customSpecies);
        }
        
        // Remove duplicates and re-index
        return array_values(array_unique($species));
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
