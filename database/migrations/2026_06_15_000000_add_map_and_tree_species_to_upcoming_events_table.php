<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('upcoming_events', function (Blueprint $table) {
            // Google Maps embed / share link / "lat,lng" — optional.
            $table->text('map_embed')->nullable()->after('province');
            // Species planned for this event (checkbox selections + free text).
            $table->json('tree_names')->nullable()->after('map_embed');
            $table->text('custom_tree_species')->nullable()->after('tree_names');
        });
    }

    public function down(): void
    {
        Schema::table('upcoming_events', function (Blueprint $table) {
            $table->dropColumn(['map_embed', 'tree_names', 'custom_tree_species']);
        });
    }
};
