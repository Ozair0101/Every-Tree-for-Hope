<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Give findings the same threaded comments trees already have.
 *
 * `voice_comments` carried only an author *name*, so two things were impossible:
 * replying to a comment, and opening the profile of whoever wrote one. Both are
 * expected here because the tree feed does them, and a reader has no reason to
 * think the two walls behave differently.
 *
 * Everything added is nullable, which is what keeps existing rows valid: a
 * comment left before this migration has no account behind it and no parent,
 * and both remain legitimate states. `author_name` therefore stays — it is the
 * only identity an anonymous comment has, and dropping it would erase the
 * attribution on every historical row.
 *
 * `root_id` mirrors tree_comments: the UI draws two levels and flattens
 * anything deeper, so it needs the top of a thread in one hop rather than
 * walking parent links up the chain.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voice_comments', function (Blueprint $table) {
            // Null for the anonymous comments the public web form still allows.
            $table->foreignId('user_id')->nullable()->after('voice_id')
                ->constrained()->nullOnDelete();

            // Deleting a comment takes its replies with it — a reply whose
            // parent is gone is an orphan nobody can make sense of.
            $table->foreignId('parent_id')->nullable()->after('user_id')
                ->constrained('voice_comments')->cascadeOnDelete();

            $table->foreignId('root_id')->nullable()->after('parent_id')
                ->constrained('voice_comments')->cascadeOnDelete();

            // The thread query: every reply under one root, oldest first.
            $table->index(['voice_id', 'root_id']);
        });
    }

    public function down(): void
    {
        Schema::table('voice_comments', function (Blueprint $table) {
            $table->dropIndex(['voice_id', 'root_id']);
            $table->dropConstrainedForeignId('root_id');
            $table->dropConstrainedForeignId('parent_id');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
