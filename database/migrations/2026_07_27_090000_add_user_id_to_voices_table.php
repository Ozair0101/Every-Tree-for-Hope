<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optionally attribute a voice to a signed-in user.
 *
 * The community wall stays open to everyone — most voices carry only an author
 * name and no account. But when a logged-in user posts, we record their id so
 * the app can show them "my voices" (including ones still awaiting moderation).
 * Nullable, and null-on-delete so removing a user never deletes their posts.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voices', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('voices', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropColumn('user_id');
        });
    }
};
