<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user for Filament
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@everytreeforhope.org',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);

        // Create additional admin users
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@everytreeforhope.org',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);

        // Create regular test users
        User::create([
            'name' => 'Test User',
            'email' => 'user@everytreeforhope.org',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        // Create additional regular users using factory
        User::factory(5)->create([
            'is_admin' => false,
        ]);

        $this->command->info('Users seeded successfully!');
        $this->command->info('Admin login: admin@everytreeforhope.org / password');
        $this->command->info('Super Admin login: superadmin@everytreeforhope.org / password');
        $this->command->info('Test User login: user@everytreeforhope.org / password');
    }
}
