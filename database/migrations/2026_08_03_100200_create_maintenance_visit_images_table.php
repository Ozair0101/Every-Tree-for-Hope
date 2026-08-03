<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Photographs attached to a maintenance visit.
 *
 * A separate table rather than a JSON column on the visit, mirroring
 * `tree_update_images`: a visit holds several frames, each needs its own order,
 * and cascading the delete keeps orphaned rows from outliving their visit.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_visit_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_visit_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_visit_images');
    }
};
