<?php

namespace Database\Seeders;

use App\Models\Tree;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Demo planted trees spread across the whole planet, for testing the map, the
 * public gallery and the profile.
 *
 * Everything is created `approved` so it shows immediately. Trees are owned by a
 * small pool of demo volunteers (emails under `@seed.local`) so map popups show
 * varied planter names. The seeder is idempotent: it first removes any trees
 * belonging to those demo users, so re-running refreshes the set rather than
 * duplicating it — and it never touches real accounts or their trees.
 *
 * Run with:  php artisan db:seed --class=TreeSeeder
 */
class TreeSeeder extends Seeder
{
    public function run(): void
    {
        $planters = collect([
            ['name' => 'Sara',   'lastname' => 'Ahmadi',  'country' => 'Afghanistan',   'email' => 'sara@seed.local'],
            ['name' => 'James',  'lastname' => 'Carter',  'country' => 'United Kingdom', 'email' => 'james@seed.local'],
            ['name' => 'Yuki',   'lastname' => 'Tanaka',  'country' => 'Japan',          'email' => 'yuki@seed.local'],
            ['name' => 'Amina',  'lastname' => 'Okafor',  'country' => 'Nigeria',        'email' => 'amina@seed.local'],
            ['name' => 'Carlos', 'lastname' => 'Mendez',  'country' => 'Colombia',       'email' => 'carlos@seed.local'],
        ])->map(fn ($p) => User::firstOrCreate(
            ['email' => $p['email']],
            [
                'name' => $p['name'],
                'lastname' => $p['lastname'],
                'country' => $p['country'],
                'password' => Hash::make(Str::random(24)),
            ],
        ));

        // Idempotent: clear this seeder's previous trees (cascades to updates).
        Tree::whereIn('user_id', $planters->pluck('id'))->delete();

        // [ species, place label, latitude, longitude ] — every continent.
        $places = [
            ['Chinar (plane tree)', 'Kabul, Afghanistan', 34.5553, 69.2075],
            ['Mulberry', 'Mazar-i-Sharif, Afghanistan', 36.7090, 67.1109],
            ['Pomegranate', 'Herat, Afghanistan', 34.3529, 62.2040],
            ['Cedar of Lebanon', 'Istanbul, Türkiye', 41.0082, 28.9784],
            ['English oak', 'London, United Kingdom', 51.5074, -0.1278],
            ['Silver birch', 'Moscow, Russia', 55.7558, 37.6173],
            ['Rowan', 'Reykjavík, Iceland', 64.1466, -21.9426],
            ['Stone pine', 'Rome, Italy', 41.9028, 12.4964],
            ['Date palm', 'Cairo, Egypt', 30.0444, 31.2357],
            ['Acacia', 'Nairobi, Kenya', -1.2921, 36.8219],
            ['Baobab', 'Antananarivo, Madagascar', -18.8792, 47.5079],
            ['Yellowwood', 'Cape Town, South Africa', -33.9249, 18.4241],
            ['Neem', 'New Delhi, India', 28.6139, 77.2090],
            ['Teak', 'Jakarta, Indonesia', -6.2088, 106.8456],
            ['Cherry blossom', 'Tokyo, Japan', 35.6762, 139.6503],
            ['Eucalyptus', 'Sydney, Australia', -33.8688, 151.2093],
            ['Red maple', 'New York, USA', 40.7128, -74.0060],
            ['Douglas fir', 'Vancouver, Canada', 49.2827, -123.1207],
            ['Brazil nut', 'Manaus, Brazil', -3.1190, -60.0217],
            ['Quindío wax palm', 'Bogotá, Colombia', 4.7110, -74.0721],
        ];

        foreach ($places as $i => [$species, $label, $lat, $lng]) {
            $owner = $planters[$i % $planters->count()];

            $tree = $owner->trees()->create([
                'species' => $species,
                'notes' => "A {$species} planted near {$label} as part of the global reforestation drive.",
                'location_name' => $label,
                'latitude' => $lat,
                'longitude' => $lng,
                'gps_accuracy' => random_int(4, 30),
                'planted_on' => now()->subDays(random_int(15, 500))->toDateString(),
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            // Give roughly every third tree a short progress history.
            if ($i % 3 === 0) {
                $tree->updates()->create(['note' => 'First green shoots breaking through.', 'height_cm' => random_int(20, 60)]);
                $tree->updates()->create(['note' => 'Growing steadily after the seasonal rains.', 'height_cm' => random_int(90, 260)]);
            }
        }

        $this->command?->info('Seeded ' . count($places) . ' approved trees across the globe.');
    }
}
