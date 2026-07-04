<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Simple site-wide key/value settings store.
 *
 * First use: the "Donate" button link, so it can be changed once from the
 * admin panel instead of editing every page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed the current donate campaign link so the button works immediately.
        DB::table('settings')->insert([
            'key' => 'donate_url',
            'value' => '#?campaign=camp_01KWF62PF5VRFFG9P753VMPMGG',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
