<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Expo push tokens, one row per device a user has signed in on.
 *
 * A user legitimately has several (phone plus tablet), so this is a hasMany
 * rather than a column on `users`. The token itself is unique across the whole
 * table, not per user: a physical device that a second person signs into hands
 * the same token to a new account, and that row must MOVE rather than
 * duplicate — otherwise the previous user keeps receiving the new user's
 * notifications on a phone that is no longer theirs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Expo tokens look like ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx].
            $table->string('token')->unique();
            $table->string('platform', 20)->nullable();

            // Lets a cleanup job drop tokens not seen for months. Expo rejects
            // stale tokens and there is no other signal that a device is gone.
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_tokens');
    }
};
