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

        // [ species, place label, latitude, longitude ] — spread across the
        // provinces of Afghanistan.
        $places = [
            ['Chinar (plane tree)', 'Kabul', 34.5553, 69.2075],
            ['White mulberry', 'Mazar-i-Sharif, Balkh', 36.7090, 67.1109],
            ['Pomegranate', 'Herat', 34.3529, 62.2040],
            ['Almond', 'Kandahar', 31.6289, 65.7372],
            ['Walnut', 'Jalalabad, Nangarhar', 34.4265, 70.4515],
            ['Russian poplar (Safeda)', 'Kunduz', 36.7286, 68.8681],
            ['Apricot', 'Ghazni', 33.5450, 68.4173],
            ['Sea buckthorn', 'Bamyan', 34.8100, 67.8210],
            ['Willow', 'Faizabad, Badakhshan', 37.1279, 70.5792],
            ['Date palm', 'Lashkargah, Helmand', 31.5940, 64.3710],
            ['Plane tree', 'Charikar, Parwan', 35.0139, 69.1683],
            ['Ash', 'Pul-e-Khumri, Baghlan', 35.9483, 68.7150],
            ['Mulberry', 'Taloqan, Takhar', 36.7360, 69.5346],
            ['Pistachio', 'Maymana, Faryab', 35.9210, 64.7840],
            ['Silver poplar', 'Sheberghan, Jowzjan', 36.6676, 65.7529],
            ['Wild pine', 'Gardez, Paktia', 33.5975, 69.2258],
            ['Jujube', 'Farah', 32.3745, 62.1164],
            ['Tamarisk', 'Zaranj, Nimroz', 30.9585, 61.8600],
            ['Walnut', 'Bazarak, Panjshir', 35.3126, 69.5150],
            ['Juniper', 'Firozkoh, Ghor', 34.5200, 65.2510],
            ['Holm oak', 'Asadabad, Kunar', 34.8742, 71.1462],
            ['Apple', 'Nili, Daykundi', 33.7220, 66.1300],
            ['Fig', 'Khost', 33.3395, 69.9200],
            ['Elm', 'Puli Alam, Logar', 33.9950, 69.0170],
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

        $this->command?->info('Seeded '.count($places).' approved trees across Afghanistan.');
    }
}
