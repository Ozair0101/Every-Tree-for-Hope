<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * User-planted trees.
 *
 * A volunteer records a tree they planted in the field — species, a note, a
 * photo and, crucially, the GPS coordinates where it stands. Each submission is
 * held for moderation (mirroring the Voices wall): it appears on the planter's
 * profile, the public map and the public list only once an admin approves it.
 *
 * `tree_updates` are the progress log — follow-up photos and notes a planter
 * adds over time to show the tree growing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('species');                 // e.g. "Chinar (Plane tree)"
            $table->text('notes')->nullable();         // why/where, in the planter's words
            $table->string('location_name')->nullable(); // optional human place label

            // The heart of the feature: where the tree physically stands.
            // 7 decimal places ≈ 1cm precision, far beyond phone-GPS accuracy.
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('gps_accuracy')->nullable(); // metres, as reported by the device

            $table->date('planted_on');
            $table->string('image_path')->nullable();

            // Moderation — hidden from profile/public/map until approved.
            $table->string('status')->default('pending'); // pending | approved | rejected
            $table->string('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('tree_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tree_id')->constrained()->cascadeOnDelete();
            $table->text('note');
            $table->string('image_path')->nullable();
            $table->unsignedInteger('height_cm')->nullable(); // optional growth metric
            $table->timestamps();

            $table->index(['tree_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tree_updates');
        Schema::dropIfExists('trees');
    }
};
