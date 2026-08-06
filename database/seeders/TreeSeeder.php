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

        // [ species, province, place label, latitude, longitude ] — one entry per
        // province, so the "Trees by province" page has a full leaderboard. The
        // province names match the mobile picker's list exactly.
        $places = [
            ['Chinar (plane tree)', 'Kabul', 'Kabul city', 34.5553, 69.2075],
            ['White mulberry', 'Balkh', 'Mazar-i-Sharif', 36.7090, 67.1109],
            ['Pomegranate', 'Herat', 'Herat city', 34.3529, 62.2040],
            ['Almond', 'Kandahar', 'Kandahar city', 31.6289, 65.7372],
            ['Walnut', 'Nangarhar', 'Jalalabad', 34.4265, 70.4515],
            ['Russian poplar (Safeda)', 'Kunduz', 'Kunduz city', 36.7286, 68.8681],
            ['Apricot', 'Ghazni', 'Ghazni city', 33.5450, 68.4173],
            ['Sea buckthorn', 'Bamyan', 'Bamyan valley', 34.8100, 67.8210],
            ['Willow', 'Badakhshan', 'Faizabad', 37.1279, 70.5792],
            ['Date palm', 'Helmand', 'Lashkargah', 31.5940, 64.3710],
            ['Plane tree', 'Parwan', 'Charikar', 35.0139, 69.1683],
            ['Ash', 'Baghlan', 'Pul-e-Khumri', 35.9483, 68.7150],
            ['Mulberry', 'Takhar', 'Taloqan', 36.7360, 69.5346],
            ['Pistachio', 'Faryab', 'Maymana', 35.9210, 64.7840],
            ['Silver poplar', 'Jowzjan', 'Sheberghan', 36.6676, 65.7529],
            ['Wild pine', 'Paktia', 'Gardez', 33.5975, 69.2258],
            ['Jujube', 'Farah', 'Farah city', 32.3745, 62.1164],
            ['Tamarisk', 'Nimroz', 'Zaranj', 30.9585, 61.8600],
            ['Walnut', 'Panjshir', 'Bazarak', 35.3126, 69.5150],
            ['Juniper', 'Ghor', 'Firozkoh', 34.5200, 65.2510],
            ['Holm oak', 'Kunar', 'Asadabad', 34.8742, 71.1462],
            ['Apple', 'Daykundi', 'Nili', 33.7220, 66.1300],
            ['Fig', 'Khost', 'Khost city', 33.3395, 69.9200],
            ['Elm', 'Logar', 'Puli Alam', 33.9950, 69.0170],
        ];

        foreach ($places as $i => [$species, $province, $label, $lat, $lng]) {
            $owner = $planters[$i % $planters->count()];

            // A batch size per record, so the province leaderboard has a spread
            // of totals rather than one tree everywhere.
            $count = random_int(5, 300);

            $tree = $owner->trees()->create([
                'species' => $species,
                'tree_count' => $count,
                'notes' => "{$count} {$species} planted in {$label}, {$province}, as part of the reforestation drive.",
                'location_name' => $label,
                'province' => $province,
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
