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
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Clean custom_tree_species before saving
     */
    public function setCustomTreeSpeciesAttribute($value)
    {
        if ($value) {
            $species = array_map('trim', explode(',', $value));
            $species = array_filter($species, function($species) {
                return !empty($species);
            });
            
            // Remove duplicates (case-insensitive)
            $uniqueSpecies = [];
            $seen = [];
            foreach ($species as $item) {
                $lowerItem = strtolower($item);
                if (!isset($seen[$lowerItem])) {
                    $uniqueSpecies[] = $item;
                    $seen[$lowerItem] = true;
                }
            }
            
            $this->attributes['custom_tree_species'] = implode(', ', $uniqueSpecies);
        } else {
            $this->attributes['custom_tree_species'] = null;
        }
    }

    /**
     * Clean tree_names before saving
     */
    public function setTreeNamesAttribute($value)
    {
        if (is_array($value)) {
            $filtered = array_filter($value, function($species) {
                return $species !== 'Other' && !empty(trim($species));
            });
            
            // Remove duplicates (case-insensitive)
            $uniqueSpecies = [];
            $seen = [];
            foreach ($filtered as $item) {
                $lowerItem = strtolower(trim($item));
                if (!isset($seen[$lowerItem])) {
                    $uniqueSpecies[] = trim($item);
                    $seen[$lowerItem] = true;
                }
            }
            
            $this->attributes['tree_names'] = json_encode(array_values($uniqueSpecies));
        } else {
            $this->attributes['tree_names'] = null;
        }
    }

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
        
        // Add checkbox selections (excluding 'Other' if it exists)
        if ($this->tree_names && is_array($this->tree_names)) {
            $filteredSpecies = array_filter($this->tree_names, function($species) {
                return $species !== 'Other' && !empty(trim($species));
            });
            $species = array_merge($species, $filteredSpecies);
        }
        
        // Add custom species
        if ($this->custom_tree_species) {
            $customSpecies = array_map('trim', explode(',', $this->custom_tree_species));
            $customSpecies = array_filter($customSpecies, function($species) {
                return !empty($species);
            });
            $species = array_merge($species, $customSpecies);
        }
        
        // Case-insensitive deduplication and sorting
        $uniqueSpecies = [];
        $seen = [];
        
        foreach ($species as $item) {
            $lowerItem = strtolower(trim($item));
            if (!isset($seen[$lowerItem])) {
                $uniqueSpecies[] = trim($item);
                $seen[$lowerItem] = true;
            }
        }
        
        sort($uniqueSpecies);
        
        return $uniqueSpecies;
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
