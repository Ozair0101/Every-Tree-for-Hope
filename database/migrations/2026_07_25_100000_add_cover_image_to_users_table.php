<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds an optional profile cover photo (banner) to users.
 *
 * Like the other profile columns this is nullable — most accounts will never set
 * one, and the profile falls back to the brand gradient when it is absent. The
 * value is a relative path on the `public` disk, never a full URL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('profile_image');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cover_image');
        });
    }
};
