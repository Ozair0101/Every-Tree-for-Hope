<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Public UUIDs, offline-sync keys, and a repair for schema drift.
 *
 * ── Where UUIDs help, and where they hurt ────────────────────────────────────
 * Not as primary keys. On InnoDB the primary key is the clustered index and is
 * copied into every secondary index; a random 36-char UUID there means page
 * splits on insert and several extra bytes per row per index. On
 * `task_activity_logs`, which grows to millions of rows, that is a real cost for
 * no benefit. The auto-increment bigint keys stay.
 *
 * UUIDs earn their place in exactly two roles:
 *
 *  1. `tasks.uuid` — the identifier the mobile API exposes. A sequential id in a
 *     URL tells anyone who looks how many tasks the organisation has ever
 *     created, and invites walking the range. The UUID is the route key; the
 *     bigint stays the join key.
 *
 *  2. `client_uuid` on submissions and progress — the offline idempotency key.
 *     This app is used in the field on connections that drop mid-request. The
 *     client cannot tell a lost response from a lost request, so it retries; the
 *     unique index turns that retry into a no-op instead of a duplicate
 *     submission in the review queue. The client generates it, so it survives
 *     the app being killed between attempts.
 *
 * `Str::orderedUuid()` (a time-ordered COMB UUID) is used rather than v4: values
 * generated close together sort close together, so the unique index appends
 * rather than scattering writes across the B-tree.
 *
 * ── Repair ───────────────────────────────────────────────────────────────────
 * The task migrations were applied to a database part-way through development,
 * before `device_tokens` was consolidated into the pre-existing `push_tokens`.
 * Every step below is guarded, so this is a no-op on a database built fresh from
 * the current migrations and a fix on one that captured the earlier revision.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->repairPushTokenConsolidation();

        // ── Public route key ────────────────────────────────────────────────
        if (! Schema::hasColumn('tasks', 'uuid')) {
            Schema::table('tasks', function (Blueprint $table) {
                // Nullable purely so the column can be added to a table that
                // already holds rows; the backfill below and the model's
                // `creating` hook mean it is never null in practice.
                $table->uuid('uuid')->nullable()->after('id')->unique();
            });

            // chunkById, not a bare update: a large table should not be rewritten
            // in one statement holding a long transaction.
            DB::table('tasks')->whereNull('uuid')->orderBy('id')->chunkById(500, function ($tasks) {
                foreach ($tasks as $task) {
                    DB::table('tasks')->where('id', $task->id)
                        ->update(['uuid' => (string) Str::orderedUuid()]);
                }
            });
        }

        // ── Offline idempotency keys ────────────────────────────────────────
        // Nullable: writes made from the admin panel have no client to generate
        // one, and MySQL permits repeated NULLs under a unique index.
        if (! Schema::hasColumn('task_submissions', 'client_uuid')) {
            Schema::table('task_submissions', function (Blueprint $table) {
                $table->uuid('client_uuid')->nullable()->after('id')->unique();
            });
        }

        if (! Schema::hasColumn('task_progress', 'client_uuid')) {
            Schema::table('task_progress', function (Blueprint $table) {
                $table->uuid('client_uuid')->nullable()->after('id')->unique();
            });
        }
    }

    /**
     * Bring a database that captured the pre-consolidation revision into line.
     */
    private function repairPushTokenConsolidation(): void
    {
        // The device columns on `push_tokens` are NOT touched here:
        // 2026_07_27_031054 owns them and already handles both the fresh and the
        // already-migrated case. Repeating the work would give those columns two
        // owners, and a second owner is a second thing trying to drop them on
        // rollback.

        // 1. Re-point the delivery log at push_tokens.
        if (Schema::hasTable('push_notifications')
            && Schema::hasColumn('push_notifications', 'device_token_id')
            && ! Schema::hasColumn('push_notifications', 'push_token_id')) {

            Schema::table('push_notifications', function (Blueprint $table) {
                $table->dropForeign(['device_token_id']);
                $table->dropColumn('device_token_id');
            });

            Schema::table('push_notifications', function (Blueprint $table) {
                $table->foreignId('push_token_id')->nullable()->after('user_id')
                    ->constrained('push_tokens')->nullOnDelete();
            });
        }

        // 2. Retire the duplicate registry. Dropped rather than kept because two
        //    device tables is precisely the ambiguity this consolidation removed.
        if (Schema::hasTable('device_tokens')) {
            Schema::dropIfExists('device_tokens');
        }
    }

    public function down(): void
    {
        foreach (['tasks' => 'uuid', 'task_submissions' => 'client_uuid', 'task_progress' => 'client_uuid'] as $table => $column) {
            if (Schema::hasColumn($table, $column)) {
                Schema::table($table, function (Blueprint $blueprint) use ($column) {
                    $blueprint->dropUnique([$column]);
                    $blueprint->dropColumn($column);
                });
            }
        }

        // The repair is deliberately not reversed: recreating `device_tokens`
        // would restore a duplicate registry nothing reads.
    }
};
