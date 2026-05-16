<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('involvement_requests', function (Blueprint $table) {
            $table->text('experience')->nullable()->after('message');
            $table->string('province')->nullable()->after('experience');
            $table->string('home_address')->nullable()->after('province');
            $table->string('leisure_time')->nullable()->after('home_address');
            $table->string('current_job')->nullable()->after('leisure_time');
            $table->string('cv_path')->nullable()->after('current_job');
        });
    }

    public function down(): void
    {
        Schema::table('involvement_requests', function (Blueprint $table) {
            $table->dropColumn([
                'experience',
                'province',
                'home_address',
                'leisure_time',
                'current_job',
                'cv_path',
            ]);
        });
    }
};
