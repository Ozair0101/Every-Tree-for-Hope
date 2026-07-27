<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Task Management — planning tables.
 *
 *   task_templates       Reusable blueprints ("Weekly watering round").
 *   task_template_items  The checklist a template stamps onto each new task.
 *   task_recurrences     Schedules that generate tasks from a template.
 *   task_dependencies    "This cannot start until that finishes."
 *
 * Recurrence is the reason this module exists at scale: tree maintenance is
 * inherently repetitive — water every third day through summer, inspect
 * monthly, prune each spring. Without templates an operator hand-types the same
 * task hundreds of times a season.
 *
 * Generated tasks are ordinary rows in `tasks`, not a special kind. A scheduled
 * job reads recurrences whose `next_run_at` has passed, stamps out a real task,
 * and advances the pointer. Editing a generated task never touches its
 * template, and deleting a template leaves already-generated work standing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('task_category_id')->nullable()->constrained()->nullOnDelete();

            // Defaults stamped onto each generated task. Mirrors the matching
            // columns on `tasks`; a template is a partial task.
            $table->string('title_template', 180);   // supports :date / :month placeholders
            $table->text('task_description')->nullable();
            $table->longText('instructions')->nullable();
            $table->string('priority', 20)->default('medium');
            $table->decimal('estimated_hours', 6, 2)->nullable();
            $table->unsignedInteger('radius')->nullable()->default(150);
            $table->boolean('requires_photo')->default(true);
            $table->boolean('requires_geo_check')->default(true);
            $table->boolean('requires_review')->default(true);
            // Days from generation to the generated task's due_date.
            $table->unsignedSmallInteger('duration_days')->default(1);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'task_category_id']);
        });

        Schema::create('task_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_template_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('requires_photo')->default(false);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['task_template_id', 'position']);
        });

        Schema::create('task_recurrences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_template_id')->constrained()->cascadeOnDelete();
            $table->string('name');

            $table->string('frequency', 20);                          // daily | weekly | monthly
            // "every N days/weeks/months". Named `repeat_every` rather than
            // `interval` because INTERVAL is a MySQL reserved word — Laravel
            // quotes it correctly, but any hand-written SQL would not.
            $table->unsignedSmallInteger('repeat_every')->default(1);
            $table->json('weekdays')->nullable();                   // [1,3,5] — weekly only
            $table->unsignedTinyInteger('day_of_month')->nullable(); // monthly only
            $table->time('time_of_day')->nullable();                // local start time

            $table->date('starts_on');
            $table->date('ends_on')->nullable();                    // null = open-ended
            // Hard cap as a safety net: a misconfigured rule cannot flood the
            // table with thousands of tasks.
            $table->unsignedSmallInteger('max_occurrences')->nullable();
            $table->unsignedSmallInteger('occurrences_count')->default(0);

            // Where the generated tasks land, and who gets them. Stored as JSON
            // because this is configuration read only by the generator — never
            // joined or filtered on.
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('location_name')->nullable();
            $table->json('assignee_user_ids')->nullable();
            $table->json('reviewer_user_ids')->nullable();

            $table->timestamp('last_generated_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // The generator's only query: due, active schedules.
            $table->index(['is_active', 'next_run_at']);
        });

        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            // `task_id` is blocked by `depends_on_task_id`.
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('depends_on_task_id')->constrained('tasks')->cascadeOnDelete();
            $table->string('type', 30)->default('finish_to_start');
            $table->timestamps();

            $table->unique(['task_id', 'depends_on_task_id']);
            // Reverse lookup: "what does finishing this unblock?"
            $table->index('depends_on_task_id');
            // Note: self-dependency and cycles are rejected in the application
            // layer — MySQL cannot express a recursive CHECK.
        });

        // Link generated tasks back to their origin. Added here rather than in
        // the core migration because the referenced tables are created above.
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('task_template_id')->nullable()->after('parent_task_id')
                ->constrained()->nullOnDelete();
            $table->foreignId('task_recurrence_id')->nullable()->after('task_template_id')
                ->constrained()->nullOnDelete();
            // The scheduled slot this task fills. Combined with the unique index
            // it makes generation idempotent: a scheduler that runs twice, or a
            // replayed queue job, cannot create the same occurrence again.
            $table->date('occurrence_date')->nullable()->after('task_recurrence_id');

            $table->unique(['task_recurrence_id', 'occurrence_date'], 'tasks_recurrence_occurrence_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Order matters: MySQL uses the composite unique index to satisfy
            // the task_recurrence_id foreign key, and refuses to drop an index
            // a constraint still needs (errno 1553). Constraints first, then
            // the index, then the columns.
            $table->dropForeign(['task_recurrence_id']);
            $table->dropForeign(['task_template_id']);
            $table->dropUnique('tasks_recurrence_occurrence_unique');
            $table->dropColumn(['task_recurrence_id', 'task_template_id', 'occurrence_date']);
        });

        Schema::dropIfExists('task_dependencies');
        Schema::dropIfExists('task_recurrences');
        Schema::dropIfExists('task_template_items');
        Schema::dropIfExists('task_templates');
    }
};
