<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Which Afghan province a tree was planted in.
 *
 * Nullable so every existing record stays valid; the plant-a-tree form picks it
 * from the full list of provinces. Sits beside the free-text `location_name`,
 * which stays for the specific place ("Qargha hillside").
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            $table->string('province', 120)->nullable()->after('location_name');
        });
    }

    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            $table->dropColumn('province');
        });
    }
};
