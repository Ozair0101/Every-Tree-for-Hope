<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Task Management — progress, review, notification and audit tables.
 *
 *   task_progress        Interim "I am 40% done" updates from the field.
 *   task_reviews         An admin's evaluation of a submitted attempt.
 *   task_notifications   The in-app inbox for task events.
 *   task_activity_logs   Every action anyone took, ever.
 *
 * Progress vs. submission
 * -----------------------
 * These answer different questions and both are needed. A submission says "I am
 * finished, please review this" — it is reviewable, numbered by attempt, and
 * carries proof. A progress update says "I am partway, here is where I am right
 * now" — it is never reviewed, can be sent twenty times in a day, and is what
 * makes a long task visible to the office while it is still running. Watering
 * 200 saplings across three days would otherwise be silent until it was over.
 *
 * Review as a table, not columns
 * ------------------------------
 * A reviewer can look at the same attempt twice — a second opinion, an appeal
 * after the volunteer sends photos, a supervisor overruling a junior. Columns on
 * `task_submissions` could only ever remember the last opinion, and the earlier
 * one is precisely what an appeal needs. `task_submissions.status` remains as
 * the derived cache the review queue filters on.
 *
 * Notifications: why `task_notifications` and not `notifications`
 * ---------------------------------------------------------------
 * The app already has Laravel's `notifications` table (UUID key, morphed
 * notifiable, JSON payload), live and read by the mobile client through
 * `$user->notifications()`. Reshaping it would break the tree-approval inbox
 * already shipped. This is the task module's own inbox, with the flat
 * title/body/task_id/is_read shape the app screen actually wants, and it leaves
 * the existing table alone.
 *
 * Three notification tables sounds like two too many, so to be explicit:
 *   notifications        generic Laravel inbox — trees, voices (existing)
 *   task_notifications   the task inbox: what the user should SEE
 *   push_notifications   FCM delivery attempts: one row per device
 * One inbox row fans out to N delivery rows, linked by
 * `push_notifications.task_notification_id`.
 *
 * One audit log, not two
 * ----------------------
 * This table absorbed what began as a separate `task_status_histories`. Two
 * tables recording overlapping events meant writing two rows per transition and
 * gave drift somewhere to hide. Status changes keep their typed `from_status` /
 * `to_status` columns here, so the state-machine audit stays queryable while
 * everything else lands in the same timeline.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_assignment_id')->constrained()->cascadeOnDelete();
            // Denormalised from the assignment so the task timeline can be built
            // without a join. Kept in sync by the model.
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('progress_percentage'); // 0–100, clamped in the model
            $table->text('note')->nullable();

            // Where the volunteer was when they reported. Same decimal(10,7) as
            // `tasks` and `trees`, so a progress trail plots on the same map.
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('gps_accuracy')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // The progress trail for one volunteer's job, newest first.
            $table->index(['task_assignment_id', 'created_at']);
            // "What moved on this task today?" across all its assignees.
            $table->index(['task_id', 'created_at']);
        });

        Schema::create('task_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_assignment_id')->constrained()->cascadeOnDelete();
            // Which attempt was judged. Nullable because a supervisor may review
            // a volunteer's overall handling of an assignment rather than one
            // specific submission.
            $table->foreignId('task_submission_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();

            // Two different measures, both optional:
            //   score   objective marks out of 100 — "38 of 40 saplings alive"
            //   rating  subjective 1–5 stars for the quality of the work
            $table->decimal('score', 5, 2)->nullable();
            $table->unsignedTinyInteger('rating')->nullable(); // 1–5, validated in the model

            $table->text('comments')->nullable();
            $table->string('review_status', 20); // App\Enums\TaskReviewStatus

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at');
            $table->timestamps();

            // The review history of one attempt, newest first.
            $table->index(['task_assignment_id', 'reviewed_at']);
            $table->index(['task_submission_id', 'reviewed_at']);
            // A reviewer's own output, and the quality dashboard.
            $table->index(['reviewed_by', 'reviewed_at']);
            $table->index(['review_status', 'reviewed_at']);
        });

        Schema::create('task_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('body');
            // Event key: task.assigned, task.due_soon, task.approved, …
            // The app switches its icon and deep link on this.
            $table->string('type', 60);

            // Nullable so a notification survives its task being purged — the
            // user's inbox should not silently lose entries. The app hides the
            // "open task" button when it is null.
            $table->foreignId('task_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('task_assignment_id')->nullable()->constrained()->nullOnDelete();

            // `is_read` is redundant with `read_at != null` and that is
            // deliberate: the unread badge is the single most-run query in the
            // app, and a boolean leads a composite index far better than a
            // nullable timestamp. The model keeps the two in lockstep.
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->json('data')->nullable(); // deep-link payload for the app
            $table->timestamps();

            // The inbox: this user's notifications, newest first.
            $table->index(['user_id', 'created_at']);
            // The unread badge count.
            $table->index(['user_id', 'is_read']);
            $table->index(['task_id', 'created_at']);
        });

        Schema::create('task_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            // Set when the action belonged to one assignee rather than to the
            // task as a whole.
            $table->foreignId('task_assignment_id')->nullable()->constrained()->nullOnDelete();
            // Nullable + SET NULL: deleting a staff member must not erase the
            // record that something happened. An audit trail that can be
            // rewritten by deleting an account is not an audit trail.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('action', 40);        // App\Enums\TaskActivityAction
            $table->text('description');         // human-readable, already composed

            // Typed transition data, populated for status changes. Keeping these
            // as real columns rather than burying them in `meta` is what lets
            // "when was this approved?" stay an indexed query.
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20)->nullable();

            // Request provenance. IPv6 needs 45 characters.
            $table->string('ip', 45)->nullable();
            $table->string('device')->nullable(); // user agent, or "Samsung SM-A155F / app 1.4.2"

            $table->json('meta')->nullable();    // changed fields, source (api|panel|system)
            $table->timestamps();

            // The task timeline — the detail screen's activity feed.
            $table->index(['task_id', 'created_at']);
            // "What has this person been doing?"
            $table->index(['user_id', 'created_at']);
            // "Everything approved last month" — reporting.
            $table->index(['action', 'created_at']);
            $table->index(['task_assignment_id', 'created_at']);
        });

        // Link a delivery attempt back to the inbox row it came from: one
        // notification fans out to every device the user has registered.
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->foreignId('task_notification_id')->nullable()->after('push_token_id')
                ->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('task_notification_id');
        });

        Schema::dropIfExists('task_activity_logs');
        Schema::dropIfExists('task_notifications');
        Schema::dropIfExists('task_reviews');
        Schema::dropIfExists('task_progress');
    }
};
