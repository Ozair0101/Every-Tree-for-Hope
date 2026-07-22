<?php

namespace App\Models\Concerns;

/**
 * Shared tree-species handling for models that record which species were
 * (or will be) planted: a `tree_names` JSON column of checkbox selections
 * plus a comma-separated `custom_tree_species` free-text column.
 */
trait HasTreeSpecies
{
    /**
     * The species offered in the admin checkbox list.
     * Single source of truth for every Filament resource.
     */
    public static function treeSpeciesOptions(): array
    {
        return [
            'Almond' => 'Almond (Badam)',
            'Pine' => 'Pine (Khar)',
            'Pomegranate' => 'Pomegranate (Anar)',
            'Walnut' => 'Walnut (Ghaz)',
            'Apricot' => 'Apricot (Zardalu)',
            'Mulberry' => 'Mulberry (Toot)',
            'Apple' => 'Apple (Sib)',
            'Grape' => 'Grape (Angur)',
            'Pistachio' => 'Pistachio (Pista)',
            'Fig' => 'Fig (Anjeer)',
            'Olive' => 'Olive (Zaytun)',
            'Cherry' => 'Cherry (Gelas)',
            'Plum' => 'Plum (Aloo Bukhara)',
            'Pear' => 'Pear (Nashpati)',
            'Chenar' => 'Chenar',
            'Sepidar' => 'Sepidar',
            'Acacia' => 'Acacia',
            'Narvan / Naroon' => 'Narvan / Naroon',
            'Kaj' => 'Kaj',
            'Sarv' => 'Sarv',
            'Najo' => 'Najo',
            'Toot' => 'Toot',
            'Anjir' => 'Anjir',
            'Divar Bidar' => 'Divar Bidar',
            'Arghavan' => 'Arghavan',
            'Juniper' => 'Juniper',
        ];
    }

    /**
     * Clean custom_tree_species before saving
     */
    public function setCustomTreeSpeciesAttribute($value)
    {
        if ($value) {
            $species = array_map('trim', explode(',', $value));
            $species = array_filter($species, function ($species) {
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
            $filtered = array_filter($value, function ($species) {
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
     * Get all tree species (both checkbox and custom)
     */
    public function getAllTreeSpeciesAttribute(): array
    {
        $species = [];

        // Add checkbox selections (excluding 'Other' if it exists)
        if ($this->tree_names && is_array($this->tree_names)) {
            $filteredSpecies = array_filter($this->tree_names, function ($species) {
                return $species !== 'Other' && !empty(trim($species));
            });
            $species = array_merge($species, $filteredSpecies);
        }

        // Add custom species
        if ($this->custom_tree_species) {
            $customSpecies = array_map('trim', explode(',', $this->custom_tree_species));
            $customSpecies = array_filter($customSpecies, function ($species) {
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
}
