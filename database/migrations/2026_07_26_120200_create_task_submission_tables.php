<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Task Management — submission, attachment and discussion tables.
 *
 *   task_submissions  One attempt at completing a task, with proof-of-presence.
 *   task_attachments  Polymorphic files for tasks, submissions and comments.
 *   task_comments     Threaded discussion, with staff-only internal notes.
 *
 * Why submissions are rows, not columns on the assignment
 * -------------------------------------------------------
 * Rejection is a first-class outcome here: a reviewer sends work back and the
 * volunteer tries again. Storing the submission on the assignment would
 * overwrite the rejected attempt and destroy the evidence of what was wrong the
 * first time. Each attempt is its own immutable row, numbered by `attempt`, so
 * the full back-and-forth survives.
 *
 * Proof-of-presence
 * -----------------
 * Every submission captures the device's own GPS reading at the moment of
 * submission. The server computes the haversine distance to the task's centre
 * and stores it in `distance_meters` — computing it once at write time keeps the
 * review queue from doing trigonometry over thousands of rows. Being outside the
 * geofence sets `is_within_geofence = false`; it does not reject the submission,
 * because rural GPS drifts by tens of metres and a hard block would strand
 * honest volunteers. The reviewer sees the flag and decides.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_assignment_id')->constrained()->cascadeOnDelete();
            // Denormalised from the assignment so the reviewer's list can show
            // "who submitted" without a second join. Kept in sync by the model.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('attempt')->default(1);

            $table->text('note')->nullable();                     // what the volunteer did
            $table->decimal('hours_spent', 6, 2)->nullable();     // feeds tasks.actual_hours

            // Proof of presence, captured by the device at submit time.
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('gps_accuracy')->nullable();  // metres, as the device reported
            $table->unsignedInteger('distance_meters')->nullable(); // to the task centre
            $table->boolean('is_within_geofence')->nullable();    // null = task had no geofence
            // The phone's own clock at capture. Compared against created_at to
            // spot work logged long after the fact, or a tampered device clock.
            $table->timestamp('device_captured_at')->nullable();

            // The attempt's own state, derived from its latest review. Who
            // reviewed it, what they scored it and what they said lives in
            // `task_reviews` (2026_07_26_120500) — a reviewer may look at the
            // same attempt twice, and one set of columns here could only ever
            // remember the last opinion. This column is the cache the review
            // queue filters on; `task_reviews` is the record.
            $table->string('status', 20)->default('pending');     // App\Enums\TaskSubmissionStatus

            $table->timestamps();

            // Retrying re-uses the assignment with the next attempt number; the
            // unique key makes a double-tapped submit button a duplicate-key
            // error instead of two rows in the review queue.
            $table->unique(['task_assignment_id', 'attempt']);
            $table->index(['task_id', 'status']);
            // The reviewer queue: oldest pending first.
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();
            // Attachable: Task (reference material from the office — the site
            // map, a species sheet), TaskSubmission (the volunteer's proof
            // photos), TaskComment. One table means one upload endpoint, one
            // validation path and one cleanup job.
            //
            // Why the morph *and* task_assignment_id below: the morph records
            // precisely what the file belongs to, which matters most for
            // submissions — attempt 1 was rejected for dark photos and attempt 2
            // has new ones, and a file keyed only to the assignment could not
            // tell them apart. The assignment column is the denormalised index
            // for "every file this volunteer uploaded for this job", which is
            // the query the app and the reviewer actually run.
            $table->morphs('attachable');
            // Every attachment belongs to some task, whatever it hangs off.
            // A polymorphic relation cannot be cascaded by the database, so
            // without this a deleted task would leave the attachment rows of its
            // submissions and comments pointing at nothing. This column is the
            // only real foreign key on the table, and it is what guarantees no
            // orphans survive. It also makes "every file on this task" — the
            // reviewer's gallery — a single indexed query.
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_assignment_id')->nullable()
                ->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            // Relative path on the named disk — never a full URL, so the storage
            // host can change without a data fix (same rule as users.profile_image).
            $table->string('disk', 30)->default('public');
            $table->string('file_path');
            $table->string('file_name');                    // as the device named it
            $table->string('file_type', 20);                // App\Enums\TaskAttachmentType
            $table->string('mime_type', 120)->nullable();   // the real thing, for serving
            $table->unsignedBigInteger('file_size')->nullable(); // bytes

            // Populated for images and video so the app can lay out the grid and
            // reserve player space before the file finishes downloading — which
            // matters on the connections this app runs over.
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable(); // video + audio

            $table->timestamps();

            // morphs() already indexes (attachable_type, attachable_id).
            // "Everything uploaded for this assignment, newest first."
            $table->index(['task_assignment_id', 'file_type']);
            // The reviewer's gallery: every file on a task, grouped by kind.
            $table->index(['task_id', 'file_type']);
        });

        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('task_comments')->cascadeOnDelete();

            $table->text('body');
            // Internal notes are staff-only: the API strips them for anyone who
            // is merely an assignee. Lets reviewers discuss a weak submission
            // without the volunteer reading it.
            $table->boolean('is_internal')->default(false);
            $table->timestamp('edited_at')->nullable();

            $table->timestamps();
            $table->softDeletes(); // a deleted comment leaves a tombstone in the thread

            $table->index(['task_id', 'is_internal', 'created_at']);
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('task_attachments');
        Schema::dropIfExists('task_submissions');
    }
};
