<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('involvement_requests', function (Blueprint $table) {
            $table->foreignId('event_id')
                ->nullable()
                ->after('type')
                ->constrained('events')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('involvement_requests', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
        });
    }
};
