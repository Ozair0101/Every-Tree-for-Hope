<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('involvement_requests', function (Blueprint $table) {
            if (Schema::hasColumn('involvement_requests', 'event_id')) {
                $table->dropForeign(['event_id']);
                $table->dropColumn('event_id');
            }
        });

        Schema::table('involvement_requests', function (Blueprint $table) {
            $table->foreignId('upcoming_event_id')
                ->nullable()
                ->after('type')
                ->constrained('upcoming_events')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('involvement_requests', function (Blueprint $table) {
            $table->dropForeign(['upcoming_event_id']);
            $table->dropColumn('upcoming_event_id');
        });

        Schema::table('involvement_requests', function (Blueprint $table) {
            $table->foreignId('event_id')
                ->nullable()
                ->after('type')
                ->constrained('events')
                ->nullOnDelete();
        });
    }
};
