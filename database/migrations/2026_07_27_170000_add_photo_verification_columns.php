<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Photo verification.
 *
 * A photograph is the primary evidence a task was done, and until now the only
 * thing stored about one was its size and dimensions. These columns make each
 * image answer for itself: where it was taken, when, and on what.
 *
 * ── Three copies of every image, on purpose ──────────────────────────────────
 *
 *   original_path   exactly the bytes the device sent, never re-encoded.
 *                   This is the evidential copy: re-compression destroys EXIF
 *                   and rewrites pixels, so a challenged submission has to be
 *                   answerable from something untouched.
 *
 *   file_path       a compressed copy, which is what the app and the panel
 *                   actually load. A 6MB phone photo becomes a few hundred KB
 *                   with no visible loss at the sizes anyone views it.
 *
 *   thumbnail_path  a small square for grids and lists, so a review page with
 *                   twelve photos does not pull twelve full images.
 *
 * The storage cost is roughly 1.3× the original. That is the price of being
 * able to prove what a volunteer actually submitted, and it is worth paying.
 *
 * ── Where the metadata comes from ────────────────────────────────────────────
 * EXIF first, because it was written by the camera at the moment of capture and
 * cannot be adjusted by the app afterwards. When a photo carries none — most
 * social apps and some pickers strip it — the values fall back to what the
 * device reported alongside the upload, and `metadata_source` records which was
 * used so a reviewer knows how much weight to give them.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_attachments', function (Blueprint $table) {
            // Where the photograph was taken, per image — not per submission.
            // A submission carries one reading; ten photos can carry ten, and
            // one taken somewhere else entirely is exactly what matters.
            $table->decimal('latitude', 10, 7)->nullable()->after('duration_seconds');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->unsignedInteger('gps_accuracy')->nullable()->after('longitude');

            // The camera's own timestamp. Distinct from created_at, which is
            // when the file reached the server — those differ by days when a
            // submission comes out of the offline queue.
            $table->timestamp('captured_at')->nullable()->after('gps_accuracy');

            $table->string('device_make', 60)->nullable()->after('captured_at');
            $table->string('device_model', 80)->nullable()->after('device_make');
            $table->string('device_os', 60)->nullable()->after('device_model');
            $table->string('app_version', 20)->nullable()->after('device_os');

            // 'exif' | 'client' | 'none' — how much the values above can be trusted.
            $table->string('metadata_source', 12)->nullable()->after('app_version');

            $table->string('original_path')->nullable()->after('file_path');
            $table->string('thumbnail_path')->nullable()->after('original_path');
            $table->unsignedBigInteger('original_size')->nullable()->after('file_size');

            // The raw EXIF block, minus the binary thumbnail. Kept for the rare
            // case where a reviewer needs something this schema did not model —
            // lens, orientation, exposure. Never queried, so JSON is right.
            $table->json('exif')->nullable()->after('metadata_source');

            // "Photos taken at this place" and the chronological evidence view.
            $table->index(['task_id', 'captured_at']);
        });

        Schema::table('trees', function (Blueprint $table) {
            // Thumbnails for both halves of the comparison. The growth gallery
            // shows pairs; loading two full photos per row would make it
            // unusable on the connections this app runs over.
            $table->string('image_thumbnail_path')->nullable()->after('image_path');
            $table->string('after_image_thumbnail_path')->nullable()->after('after_image_path');

            // Where and when the follow-up was taken.
            //
            // The tree's own latitude/longitude describe where it stands. These
            // describe where the photographer stood — and a follow-up shot
            // kilometres from the tree is the single most useful thing to catch
            // in a before/after comparison.
            $table->decimal('after_image_latitude', 10, 7)->nullable()->after('after_image_taken_at');
            $table->decimal('after_image_longitude', 10, 7)->nullable()->after('after_image_latitude');
            $table->unsignedInteger('after_image_distance')->nullable()->after('after_image_longitude');
            $table->string('after_image_device', 120)->nullable()->after('after_image_distance');
        });
    }

    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            $table->dropColumn([
                'image_thumbnail_path', 'after_image_thumbnail_path',
                'after_image_latitude', 'after_image_longitude',
                'after_image_distance', 'after_image_device',
            ]);
        });

        Schema::table('task_attachments', function (Blueprint $table) {
            $table->dropIndex(['task_id', 'captured_at']);
            $table->dropColumn([
                'latitude', 'longitude', 'gps_accuracy', 'captured_at',
                'device_make', 'device_model', 'device_os', 'app_version',
                'metadata_source', 'exif',
                'original_path', 'thumbnail_path', 'original_size',
            ]);
        });
    }
};
