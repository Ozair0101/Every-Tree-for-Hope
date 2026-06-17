<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Voices of Nature" — a community wall where people anywhere in the world
 * share their ideas, findings and experiences about the natural world.
 *
 * Self-contained module: a post (voice), its comments, and per-visitor likes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voices', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();

            // Author (no account required — community is open to everyone).
            $table->string('author_name');
            $table->string('author_email')->nullable();   // private, never shown publicly
            $table->string('country')->nullable();

            // Content
            $table->string('category')->default('experience'); // idea | finding | experience | question
            $table->string('title');
            $table->text('body');
            $table->string('image_path')->nullable();

            // Engagement counters (denormalised for cheap reads)
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->unsignedInteger('views_count')->default(0);

            // Moderation — posts are hidden until an admin approves them.
            $table->string('status')->default('pending');  // pending | approved | rejected
            $table->boolean('is_featured')->default(false);

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['status', 'category']);
        });

        Schema::create('voice_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voice_id')->constrained()->cascadeOnDelete();
            $table->string('author_name');
            $table->text('body');
            $table->string('status')->default('approved'); // approved | hidden
            $table->timestamps();

            $table->index(['voice_id', 'status']);
        });

        // One like per visitor (fingerprint = session id, kept in a cookie).
        Schema::create('voice_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voice_id')->constrained()->cascadeOnDelete();
            $table->string('fingerprint', 64);
            $table->timestamps();

            $table->unique(['voice_id', 'fingerprint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voice_likes');
        Schema::dropIfExists('voice_comments');
        Schema::dropIfExists('voices');
    }
};
