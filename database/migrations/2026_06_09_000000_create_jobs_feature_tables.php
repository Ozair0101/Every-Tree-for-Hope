<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Careers / Jobs feature — a self-contained module.
 *
 * Tables are intentionally prefixed `job_` (not `jobs`) to avoid colliding
 * with Laravel's queue `jobs` table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();

            // Translatable JSON columns ({"en": "...", "fa": "...", "ps": "..."})
            $table->json('title');
            $table->json('summary')->nullable();
            $table->json('description');
            $table->json('responsibilities')->nullable();
            $table->json('requirements')->nullable();
            $table->json('benefits')->nullable();

            // Structured / filterable attributes
            $table->string('department')->nullable();
            $table->string('employment_type')->default('full_time');
            $table->string('experience_level')->nullable();
            $table->string('location')->nullable();
            $table->string('province')->nullable();
            $table->boolean('is_remote')->default(false);
            $table->string('salary_range')->nullable();
            $table->unsignedInteger('openings')->default(1);
            $table->date('application_deadline')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['is_active', 'employment_type']);
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')
                ->constrained('job_postings')
                ->cascadeOnDelete();

            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('location')->nullable();
            $table->unsignedInteger('years_experience')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('resume_path');

            $table->enum('status', [
                'pending', 'reviewing', 'shortlisted', 'interviewing', 'rejected', 'hired',
            ])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index(['job_posting_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_postings');
    }
};
