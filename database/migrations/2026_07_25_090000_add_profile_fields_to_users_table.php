<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends the users table with the self-service profile the mobile app collects
 * at registration.
 *
 * Every column is nullable. The existing rows are admin/staff accounts created
 * through Filament, which never supplied a surname, address, country or avatar;
 * making these required would fail the migration against live data and would
 * also break the admin panel's own user form, which does not know these fields.
 * The mobile RegisterRequest enforces what it needs at the point of entry
 * instead.
 *
 * `name` continues to hold the given (first) name so the column stays meaningful
 * to the existing web platform; the surname lives alongside it in `lastname`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('lastname')->nullable()->after('name');
            $table->string('country')->nullable()->after('email');
            $table->string('address')->nullable()->after('country');
            // Relative path on the `public` disk, e.g. profile-images/ab12.jpg —
            // never a full URL, so the storage host can change without a data fix.
            $table->string('profile_image')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['lastname', 'country', 'address', 'profile_image']);
        });
    }
};
