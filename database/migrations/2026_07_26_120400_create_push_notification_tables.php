<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Firebase Cloud Messaging — device registry, preferences and delivery log.
 *
 *   device_tokens             One row per app install that may receive a push.
 *   notification_preferences  Per-user, per-event opt-outs.
 *   push_notifications        Outbox and delivery log for every push attempted.
 *
 * How this sits next to the existing `notifications` table
 * -------------------------------------------------------
 * The app already has Laravel's database-notifications table backing the in-app
 * bell. That stays the record of *what the user should see*. These tables cover
 * *delivery to the device*, which is a different problem with different failure
 * modes: tokens expire, apps get uninstalled, FCM rate-limits and returns
 * per-token errors. Keeping them apart means a failed push never corrupts the
 * in-app inbox, and one logical notification can fan out to a user's three
 * devices as three delivery rows.
 *
 * Token hygiene
 * -------------
 * FCM tokens rotate. The registry is keyed by the token itself, so a re-register
 * from the same install is an upsert. When FCM returns UNREGISTERED or
 * INVALID_ARGUMENT for a token, the delivery worker sets `is_active = false`
 * rather than deleting the row — the history of what was sent stays intact.
 */
return new class extends Migration
{
    public function up(): void
    {
        // The device registry already exists: `push_tokens` (2026_07_26_084546)
        // backs the Expo push channel. Extending it beats adding a second
        // table — two registries would mean two registration endpoints, two
        // cleanup jobs, and an app that has to guess which one a given device
        // is in.
        //
        // The columns that extension needs (device_id, device_name, app_version,
        // os_version, locale, is_active, failure_count) are added by
        // `2026_07_27_031054_add_device_columns_to_push_tokens_table`, which owns
        // them outright. They were briefly declared here as well; two migrations
        // adding the same columns meant two migrations *dropping* them on
        // rollback, and the second drop failed against a schema the first had
        // already changed. One owner per column is the rule that avoids it.

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('channel', 20);  // push | email | database
            // Event key: task.assigned, task.due_soon, task.overdue,
            // task.submitted, task.approved, task.rejected, task.commented.
            $table->string('event_key', 60);
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            // Absence of a row means "enabled" — only opt-outs are stored, so
            // the table stays small no matter how many users exist.
            $table->unique(['user_id', 'channel', 'event_key'], 'notification_pref_unique');
        });

        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('push_token_id')->nullable()->constrained('push_tokens')->nullOnDelete();

            // What the push is about — usually a Task, sometimes a
            // TaskSubmission or TaskComment. Lets the app deep-link on tap.
            $table->nullableMorphs('related');

            $table->string('event_key', 60);   // task.assigned, task.due_soon, …
            $table->string('title');
            $table->text('body');
            // FCM data payload: {"type":"task","task_id":42,"screen":"TaskDetail"}
            $table->json('data')->nullable();

            $table->string('status', 20)->default('queued'); // queued | sent | failed | skipped
            $table->string('fcm_message_id')->nullable();    // FCM's returned name
            $table->string('error_code', 60)->nullable();    // UNREGISTERED, QUOTA_EXCEEDED, …
            $table->text('error_message')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            // Worker: pick up queued rows oldest first.
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'event_key', 'created_at']);
            // Throttle check: "did we already push this event for this task?"
            $table->index(['event_key', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
        Schema::dropIfExists('notification_preferences');

        // push_tokens is deliberately untouched here — its device columns belong
        // to 2026_07_27_031054, which reverses them itself.
    }
};
