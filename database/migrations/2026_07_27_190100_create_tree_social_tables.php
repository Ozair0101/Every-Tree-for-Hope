<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Likes, comments and shares on a planted tree.
 *
 * The community wall for plantings. `Voice` already has likes and comments, but
 * its comments are flat and keyed on a typed-in `author_name` — fine for an
 * anonymous public wall, wrong here: only signed-in users may react, so the
 * author is a real `user_id`, and replies nest.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tree_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tree_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // The like *is* the pair. A unique index rather than a check in the
            // controller, because a double-tap on a slow connection fires twice
            // and only the database can arbitrate that race.
            $table->unique(['tree_id', 'user_id']);
            // "Which of these trees have I liked?" for a page of the feed.
            $table->index(['user_id', 'tree_id']);
        });

        Schema::create('tree_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tree_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            /*
             | Two parent columns, deliberately.
             |
             | `parent_id` is who was replied to — the real conversational edge,
             | so "Ahmad replied to Sara" survives however deep it went.
             |
             | `root_id` is the top-level comment the whole sub-thread hangs
             | from. It exists because unbounded indentation is unreadable on a
             | phone: the UI renders two levels, and every reply below the first
             | is flattened into the same thread with the recipient named. That
             | is what Instagram and YouTube do, and it is the reason this is one
             | index lookup rather than a recursive walk.
             |
             | Null `root_id` means the comment is itself a root.
            */
            $table->foreignId('parent_id')->nullable()
                ->constrained('tree_comments')->cascadeOnDelete();
            $table->foreignId('root_id')->nullable()
                ->constrained('tree_comments')->cascadeOnDelete();

            $table->text('body');

            // Moderation, kept deliberately light: comments are visible at once
            // and can be hidden after the fact. Holding every comment for review
            // would kill a conversation that only works in real time.
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // The thread query: roots of a tree, oldest first.
            $table->index(['tree_id', 'root_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::table('trees', function (Blueprint $table) {
            // Shares leave no row of their own — there is nothing to record
            // about "opened the OS share sheet" beyond that it happened — so
            // this is a plain counter rather than a table.
            $table->unsignedInteger('shares_count')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            $table->dropColumn('shares_count');
        });

        // Comments first: its self-referencing foreign keys must go before the
        // table they point at can be dropped.
        Schema::dropIfExists('tree_comments');
        Schema::dropIfExists('tree_likes');
    }
};
