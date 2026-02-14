<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed all data
        $this->call([
            CleanTreeSpeciesSeeder::class,
            EventSeeder::class,
            TeamSeeder::class,
            DonatorSeeder::class,
            PartnerSeeder::class,
            MediaSeeder::class,
            ContactMessageSeeder::class,
        ]);
    }
}
