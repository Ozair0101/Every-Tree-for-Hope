<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Many photographs per tree, in two phases.
 *
 * Until now a tree held exactly one planting photo and one follow-up, in
 * `trees.image_path` and `trees.after_image_path`. A volunteer standing over a
 * newly planted sapling naturally takes several — the hole, the sapling, the
 * wider site — and being allowed only one forces a choice that loses the rest.
 *
 * ── Why the two original columns stay ────────────────────────────────────────
 * They are not dropped. They become the *cover* pointer: the one image chosen
 * to represent each phase. Everything already reads through them — the public
 * map, the Filament resource, `hasComparison()`, `withComparison()`, the
 * analytics follow-up rate, the mobile app's list rows — and denormalising the
 * cover onto the parent keeps every one of those working unchanged and without
 * a join. `Tree::syncCover()` is the single writer that keeps them true.
 *
 * The alternative, resolving the cover through a join on every read, would have
 * meant touching a dozen call sites to gain nothing a user could see.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tree_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tree_id')->constrained()->cascadeOnDelete();

            // 'before' = planting day. 'after' = the follow-up, weeks or months
            // later. VARCHAR rather than MySQL ENUM for the same reason as
            // every other status column here: adding a phase stays a code-only
            // deploy instead of an ALTER on a growing table.
            $table->string('phase', 16)->default('before');

            // Three copies, as the task attachments do: the compressed one is
            // served, the thumbnail fills lists, the original is what an
            // auditor looks at when a photo is disputed.
            $table->string('path');
            $table->string('thumbnail_path')->nullable();
            $table->string('original_path')->nullable();

            $table->string('caption', 500)->nullable();

            // Exactly one cover per (tree, phase) — enforced by a partial index
            // where the driver has them, and by Tree::syncCover() everywhere.
            $table->boolean('is_cover')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);

            // Per-image provenance, mirroring task_attachments. This is what
            // makes a photo evidence rather than decoration.
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('gps_accuracy')->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->string('device_make', 60)->nullable();
            $table->string('device_model', 80)->nullable();
            $table->string('device_os', 60)->nullable();
            $table->string('app_version', 40)->nullable();
            // 'exif' | 'client' | 'none' — only ever 'exif' when real GPS, a
            // real capture time or a real camera identity was actually found.
            $table->string('metadata_source', 16)->default('none');
            $table->json('exif')->nullable();

            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            // Metres from the tree's own coordinates to where this frame was
            // shot. Computed at write time so no read has to.
            $table->unsignedInteger('distance_meters')->nullable();

            $table->timestamps();

            // The gallery query: one tree, one phase, in display order.
            $table->index(['tree_id', 'phase', 'sort_order']);
            // The cover lookup, and the repair sweep that finds phases with none.
            $table->index(['tree_id', 'phase', 'is_cover']);
        });

        $this->backfill();
    }

    /**
     * Move the photos that already exist into the new table.
     *
     * Without this every tree recorded before today would show an empty gallery
     * while its cover column still pointed at a real file — the parent would
     * claim a photo the gallery could not produce.
     *
     * Chunked because this runs against production, and a single query
     * materialising every tree is how a migration times out.
     */
    private function backfill(): void
    {
        DB::table('trees')
            ->select([
                'id', 'image_path', 'after_image_path', 'after_image_thumbnail_path',
                'after_image_note', 'after_image_taken_at', 'after_image_latitude',
                'after_image_longitude', 'after_image_distance', 'after_image_device',
                'image_thumbnail_path', 'created_at',
            ])
            ->orderBy('id')
            ->chunkById(500, function ($trees) {
                $rows = [];

                foreach ($trees as $tree) {
                    if (filled($tree->image_path)) {
                        $rows[] = [
                            'tree_id' => $tree->id,
                            'phase' => 'before',
                            'path' => $tree->image_path,
                            'thumbnail_path' => $tree->image_thumbnail_path,
                            'is_cover' => true,
                            'sort_order' => 0,
                            'metadata_source' => 'none',
                            'created_at' => $tree->created_at,
                            'updated_at' => $tree->created_at,
                        ];
                    }

                    if (filled($tree->after_image_path)) {
                        $rows[] = [
                            'tree_id' => $tree->id,
                            'phase' => 'after',
                            'path' => $tree->after_image_path,
                            'thumbnail_path' => $tree->after_image_thumbnail_path,
                            'caption' => $tree->after_image_note,
                            'is_cover' => true,
                            'sort_order' => 0,
                            'captured_at' => $tree->after_image_taken_at,
                            'latitude' => $tree->after_image_latitude,
                            'longitude' => $tree->after_image_longitude,
                            'distance_meters' => $tree->after_image_distance,
                            'device_model' => $tree->after_image_device,
                            'metadata_source' => 'none',
                            'created_at' => $tree->after_image_taken_at ?? $tree->created_at,
                            'updated_at' => $tree->after_image_taken_at ?? $tree->created_at,
                        ];
                    }
                }

                if ($rows !== []) {
                    DB::table('tree_images')->insert($rows);
                }
            });
    }

    public function down(): void
    {
        // The cover columns on `trees` were never emptied, so dropping this
        // table loses only the extra photographs — every tree keeps the two it
        // had before. That is what makes this rollback safe to run.
        Schema::dropIfExists('tree_images');
    }
};
