<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanTreeSpeciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = DB::table('events')->get();
        
        foreach ($events as $event) {
            // Parse tree_names if it's a JSON string
            $treeNames = [];
            if ($event->tree_names) {
                $decoded = json_decode($event->tree_names, true);
                if (is_array($decoded)) {
                    $treeNames = array_filter($decoded, function($name) {
                        return $name !== 'Other' && !empty(trim($name));
                    });
                }
            }
            
            // Parse custom_tree_species
            $customSpecies = [];
            if ($event->custom_tree_species) {
                $customSpecies = array_map('trim', explode(',', $event->custom_tree_species));
                $customSpecies = array_filter($customSpecies, function($species) {
                    return !empty($species);
                });
            }
            
            // Find and remove duplicates between the two fields
            $cleanTreeNames = [];
            $cleanCustomSpecies = [];
            $duplicatesFound = [];
            
            // First, process tree_names and move any that are also in custom to custom only
            foreach ($treeNames as $name) {
                $name = trim($name);
                $lowerName = strtolower($name);
                
                $isDuplicate = false;
                foreach ($customSpecies as $custom) {
                    if (strtolower(trim($custom)) === $lowerName) {
                        $duplicatesFound[] = $name;
                        $isDuplicate = true;
                        break;
                    }
                }
                
                if (!$isDuplicate) {
                    $cleanTreeNames[] = $name;
                }
            }
            
            // Clean custom species (remove duplicates within itself)
            $seenCustom = [];
            foreach ($customSpecies as $custom) {
                $custom = trim($custom);
                $lowerCustom = strtolower($custom);
                
                if (!isset($seenCustom[$lowerCustom])) {
                    $cleanCustomSpecies[] = $custom;
                    $seenCustom[$lowerCustom] = true;
                }
            }
            
            // Only update if there were changes
            $treeNamesChanged = json_encode(array_values($cleanTreeNames)) !== $event->tree_names;
            $customSpeciesChanged = implode(', ', $cleanCustomSpecies) !== $event->custom_tree_species;
            
            if ($treeNamesChanged || $customSpeciesChanged) {
                DB::table('events')
                    ->where('id', $event->id)
                    ->update([
                        'tree_names' => json_encode(array_values($cleanTreeNames)),
                        'custom_tree_species' => implode(', ', $cleanCustomSpecies)
                    ]);
                    
                if (!empty($duplicatesFound)) {
                    echo "Cleaned event ID {$event->id}: Removed duplicates " . implode(', ', $duplicatesFound) . "\n";
                } else {
                    echo "Cleaned event ID {$event->id}: General cleanup\n";
                }
            }
        }
        
        echo "Tree species cleanup completed!\n";
    }
}
