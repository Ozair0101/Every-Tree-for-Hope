<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Task Management — assignment and checklist tables.
 *
 *   task_assignments            Who is attached to a task, and their own progress.
 *   task_checklist_items        The steps a task is broken into.
 *   task_checklist_completions  Which assignee ticked which step.
 *
 * Why assignment is its own table rather than a `assigned_to` column on `tasks`
 * ---------------------------------------------------------------------------
 * A plantation task is rarely one person's job — "water the 40 saplings at
 * Qargha" goes to a team. A single foreign key would force one row per
 * volunteer, duplicating the title, instructions, geofence and due date, and
 * making "how many tasks are open?" unanswerable. Splitting assignment out
 * gives each volunteer an independent lifecycle (accepted / declined / started)
 * against one shared definition of the work, and makes reviewers and watchers
 * fall out of the same structure for free.
 *
 * Checklist completions are keyed by assignment, not by user, so the same
 * volunteer reassigned to a task later starts from a clean checklist.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('role', 20)->default('assignee'); // App\Enums\TaskAssignmentRole
            $table->string('status', 20)->default('pending'); // App\Enums\TaskAssignmentStatus

            // The one accountable person when a task is shared by a team. Used
            // for escalation pushes; enforced in code, not by a partial unique
            // index (MySQL has none).
            $table->boolean('is_primary')->default(false);

            // ── Lifecycle clock ──────────────────────────────────────────────
            // Each column is the moment one milestone happened, so "how long did
            // volunteers sit on this before starting?" is a subtraction, not a
            // scan of the history table.
            //
            // `assigned_at` is deliberately not `created_at`. created_at is when
            // the row was inserted; assigned_at is when the business event
            // happened. They diverge whenever a coordinator records an assignment
            // agreed verbally in the field the day before, or when historical
            // data is imported. Reports must key off the business event.
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Free-text note carried by the assignment: why the volunteer
            // declined, why a coordinator reassigned it, any handover detail.
            // One column rather than one per reason — the text is only ever read
            // by a human, never filtered on.
            $table->text('remarks')->nullable();

            // Push bookkeeping. `last_notified_at` throttles reminder spam;
            // `reminders_sent` caps how many times we nag before escalating to
            // the task creator.
            $table->timestamp('last_notified_at')->nullable();
            $table->unsignedTinyInteger('reminders_sent')->default(0);

            $table->timestamps();

            // A user holds each role on a task at most once, but may be both an
            // assignee and a watcher.
            $table->unique(['task_id', 'user_id', 'role']);
            // "My Tasks" — the single hottest query in the mobile app.
            $table->index(['user_id', 'status']);
            $table->index(['task_id', 'role', 'status']);
            // Reminder sweep: active assignments not pushed to recently.
            $table->index(['status', 'last_notified_at']);
            // Volunteer workload reports: "what did Ahmad take on this month?"
            $table->index(['user_id', 'assigned_at']);
        });

        Schema::create('task_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            // A required step must be ticked before the task can be submitted.
            $table->boolean('is_required')->default(true);
            // Ticking this step demands a photo — e.g. "photograph the dug pit".
            $table->boolean('requires_photo')->default(false);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['task_id', 'position']);
        });

        Schema::create('task_checklist_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_checklist_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at');
            $table->string('note')->nullable();
            $table->timestamps();

            // One tick per step per assignment — makes the ticking endpoint an
            // idempotent upsert, which matters on a flaky mobile connection.
            $table->unique(['task_checklist_item_id', 'task_assignment_id'], 'task_checklist_unique');
            $table->index('task_assignment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_checklist_completions');
        Schema::dropIfExists('task_checklist_items');
        Schema::dropIfExists('task_assignments');
    }
};
