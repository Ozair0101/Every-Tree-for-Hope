<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Photographs on a progress entry.
 *
 * A follow-up is several frames as often as a planting is — the trunk, the
 * canopy, the ruler against the stem — so a progress entry needs a gallery, not
 * a single slot. The single `tree_updates.image_path` column is kept for
 * backward compatibility (older app builds still post one `image`); when a
 * client sends `images[]` the frames land here, and the first is mirrored into
 * that column so existing single-image displays keep working.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tree_update_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tree_update_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['tree_update_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tree_update_images');
    }
};
