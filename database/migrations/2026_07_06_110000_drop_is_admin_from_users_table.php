<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Removes the legacy `is_admin` boolean now that panel access is fully
 * role-based (see App\Models\User::canAccessPanel).
 *
 * IMPORTANT: RolesAndPermissionsSeeder must run *before* this migration so
 * that every existing is_admin=true user has already been promoted to the
 * Super Admin role — otherwise those users would lose access.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_admin');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false)->after('password');
            });
        }
    }
};
