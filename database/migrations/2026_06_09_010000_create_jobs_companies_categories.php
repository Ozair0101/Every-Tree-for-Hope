<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Turns the careers module into a multi-company job board (jobs.af style):
 * jobs are attributed to a hiring Company and browsed by JobCategory.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable();
            $table->string('website')->nullable();
            $table->string('industry')->nullable();
            $table->string('location')->nullable();
            $table->text('about')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('job_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // translatable
            $table->string('slug')->unique();
            $table->string('icon')->nullable(); // material symbol name
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')
                ->constrained('companies')->nullOnDelete();
            $table->foreignId('job_category_id')->nullable()->after('company_id')
                ->constrained('job_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
            $table->dropConstrainedForeignId('job_category_id');
        });

        Schema::dropIfExists('job_categories');
        Schema::dropIfExists('companies');
    }
};
