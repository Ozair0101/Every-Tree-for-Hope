<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedInteger('trees_lost')->default(0)->after('trees_planted');
            $table->date('last_maintained_at')->nullable()->after('trees_lost');
            $table->text('maintenance_notes')->nullable()->after('last_maintained_at');
            $table->json('maintenance_visits')->nullable()->after('maintenance_notes');
            $table->json('maintenance_photos')->nullable()->after('maintenance_visits');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'trees_lost',
                'last_maintained_at',
                'maintenance_notes',
                'maintenance_visits',
                'maintenance_photos',
            ]);
        });
    }
};
