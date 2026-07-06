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
     * Run the database seeds. Idempotent: reruns re-assign roles without
     * creating duplicate users. Assumes RolesAndPermissionsSeeder has already
     * created the roles (it runs first in DatabaseSeeder).
     */
    public function run(): void
    {
        // Super Admin — bypasses every permission check.
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@everytreeforhope.org'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $superAdmin->syncRoles(['Super Admin']);

        // Admin — everything except Access Control.
        $admin = User::firstOrCreate(
            ['email' => 'admin@everytreeforhope.org'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $admin->syncRoles(['Admin']);

        // Read-only demo account.
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@everytreeforhope.org'],
            [
                'name' => 'Viewer User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $viewer->syncRoles(['Viewer']);

        // A non-panel user (no roles) — cannot access the admin panel.
        User::firstOrCreate(
            ['email' => 'user@everytreeforhope.org'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $this->command->info('Users seeded successfully!');
        $this->command->info('Super Admin: superadmin@everytreeforhope.org / password');
        $this->command->info('Admin:       admin@everytreeforhope.org / password');
        $this->command->info('Viewer:      viewer@everytreeforhope.org / password');
        $this->command->info('No-access:   user@everytreeforhope.org / password');
    }
}
