<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One person's saved posts.
 *
 * Strictly private: a favourite says something about the reader, not about the
 * tree, so there is no count on the post and no way to see anyone else's list.
 * That is why this is not modelled like `tree_likes`, which is public and whose
 * total is shown.
 *
 * The (user, tree) pair is unique, so favouriting twice is idempotent rather
 * than a way to fill the table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tree_favourites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tree_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'tree_id']);
            // The list query: this user's favourites, newest saved first.
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tree_favourites');
    }
};
