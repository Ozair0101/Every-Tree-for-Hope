<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Task Management — core tables.
 *
 *   task_categories       Grouping for tasks (Planting, Watering, Survey, …).
 *   tasks                 The work item itself.
 *   task_status_histories Append-only audit trail of every status change.
 *
 * Design notes
 * ------------
 * • Enum-ish columns (`priority`, `status`) are stored as short VARCHARs cast to
 *   PHP backed enums, not as MySQL ENUM. A native ENUM needs an ALTER TABLE —
 *   a full table rebuild and a write lock on large tables — every time a case is
 *   added. VARCHAR + the enum class keeps the values validated in code and makes
 *   adding a status a code-only deploy.
 *
 * • GPS uses decimal(10,7), the same precision as `trees` — ~1cm, far beyond
 *   phone-GPS accuracy — so the two features can be plotted on one map without
 *   any conversion.
 *
 * • `created_by` is nullable with ON DELETE SET NULL. A task is a record of work
 *   that actually happened; deleting the staff member who wrote it must not
 *   delete the history of what volunteers did. The column is always populated on
 *   insert — nullable only describes what survives a user deletion.
 *
 * • Soft deletes on `tasks`: cancelling is a status, but an operator who removes
 *   a task entirely should not silently orphan submissions and audit rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#059669');  // hex, for app + panel badges
            $table->string('icon', 50)->nullable();          // lucide/heroicon name
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'position']);
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Human-quotable identifier (TSK-2026-000042). Volunteers and staff
            // refer to tasks over the phone and in WhatsApp; a bare auto-increment
            // id is easy to mishear and leaks how many tasks exist.
            //
            // Nullable because the reference embeds the row id, so it can only be
            // written once the insert has returned one — `Task::booted()` fills it
            // immediately after. MySQL permits repeated NULLs under a unique
            // index, so the transient gap costs nothing.
            $table->string('reference', 24)->nullable()->unique();

            $table->foreignId('task_category_id')->nullable()->constrained()->nullOnDelete();

            // Ties the task into the existing domain. A watering task belongs to
            // a past planting Event; a preparation task belongs to an
            // UpcomingEvent. Both optional — standalone tasks are normal.
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('upcoming_event_id')->nullable()->constrained()->nullOnDelete();

            // Subtasks. Deleting a parent takes its children with it.
            $table->foreignId('parent_task_id')->nullable()->constrained('tasks')->cascadeOnDelete();

            $table->string('title', 180);
            $table->text('description')->nullable();     // what and why, shown in the list
            $table->longText('instructions')->nullable(); // step-by-step, shown on the detail screen

            $table->string('priority', 20)->default('medium'); // App\Enums\TaskPriority
            $table->string('status', 20)->default('draft');    // App\Enums\TaskStatus

            $table->dateTime('start_date')->nullable();
            $table->dateTime('due_date')->nullable();

            $table->decimal('estimated_hours', 6, 2)->nullable();
            // Roll-up of approved submissions' hours_spent. Denormalised on
            // purpose: every workload report reads it, nothing writes it but the
            // review action.
            $table->decimal('actual_hours', 6, 2)->nullable();

            // Geofence. `radius` is metres from the point; a submission outside
            // it is flagged rather than blocked, because rural GPS drifts.
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('radius')->nullable()->default(150);
            $table->string('location_name')->nullable();  // "Qargha Lake, west bank"

            // Submission requirements, enforced by the API's submit endpoint.
            $table->boolean('requires_photo')->default(true);
            $table->boolean('requires_geo_check')->default(true);
            $table->boolean('requires_review')->default(true); // false → auto-approve on submit

            // Null = unlimited. Guards the "open task anyone can pick up" case.
            $table->unsignedSmallInteger('max_assignees')->nullable();

            // 0–100, derived from checklist completion. Cached so list screens
            // do not aggregate child rows per task.
            $table->unsignedTinyInteger('progress')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('published_at')->nullable();  // left Draft
            $table->timestamp('completed_at')->nullable();  // reached Approved
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // ── Indexes ──────────────────────────────────────────────────────
            // Admin queue: "open tasks, soonest deadline first".
            $table->index(['status', 'due_date']);
            // Triage board: "critical work that is not finished".
            $table->index(['priority', 'status']);
            // Overdue sweep run by the reminder scheduler.
            $table->index(['due_date', 'status']);
            $table->index(['task_category_id', 'status']);
            $table->index(['created_by', 'status']);
            $table->index('parent_task_id');
            // Map bounding-box lookups ("tasks near me"). A composite B-tree is
            // the portable choice; migrate to a SPATIAL POINT index only if the
            // map query ever becomes a measured bottleneck.
            $table->index(['latitude', 'longitude']);
        });

        // NOTE: the status audit trail lives in `task_activity_logs`
        // (2026_07_26_120500). It began life here as a separate
        // `task_status_histories` table and was merged into the general
        // activity log — two tables recording overlapping events meant writing
        // two rows per transition and gave drift somewhere to hide.
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('task_categories');
    }
};
