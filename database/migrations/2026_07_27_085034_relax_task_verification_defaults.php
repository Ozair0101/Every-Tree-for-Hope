<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Turn the geo-fence and photo requirements off by default.
 *
 * The admin form no longer asks for coordinates, an on-site radius, or the
 * photo / geo-check toggles — they were detail nobody was filling in
 * meaningfully. But the columns defaulted to 1, so simply dropping the fields
 * from the form would have left every new task silently demanding a photo and
 * an on-site GPS check *with no coordinates to check against* — strictly worse
 * than before it was removed.
 *
 * So the defaults move to 0. The columns stay: the mobile API, the submission
 * flow and GpsVerificationService all read them, and a task created before this
 * migration keeps whatever it was given. What changes is only what a new task
 * assumes when nobody says otherwise.
 *
 * `requires_review` deliberately stays 1. Removing its toggle means choosing one
 * behaviour for everyone, and "work is checked before it counts" is the one the
 * review queue, the submissions resource and the reviewer notifications are all
 * built around.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Blueprint->change(), not a raw ALTER.
        //
        // `ALTER TABLE … ALTER COLUMN … SET DEFAULT` is MySQL syntax that SQLite
        // does not implement at all, and the test suite runs on SQLite — so the
        // raw form migrated cleanly in production and took every single test
        // down with a syntax error.
        //
        // The original reason for avoiding ->change() was Doctrine rewriting the
        // column definition. That has not applied since Laravel 11: column
        // changes are native per-driver, and SQLite is handled by a table
        // rebuild. The full definition is restated below precisely because
        // ->change() replaces it — an omitted `nullable()` here would silently
        // make the column NOT NULL.
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('requires_photo')->default(false)->change();
            $table->boolean('requires_geo_check')->default(false)->change();
            // Kept nullable with a null default rather than dropped: MySQL in
            // strict mode rejects an insert that omits a column with no default
            // clause, and that is every insert now the form field is gone.
            $table->unsignedInteger('radius')->nullable()->default(null)->change();
        });

        // Existing tasks that were never given coordinates cannot pass a geo
        // check — the flag on them is a trap that would fail submissions from
        // the field. Clear it where there is nothing to measure against.
        DB::table('tasks')
            ->whereNull('latitude')
            ->orWhereNull('longitude')
            ->update(['requires_geo_check' => false]);
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('requires_photo')->default(true)->change();
            $table->boolean('requires_geo_check')->default(true)->change();
            $table->unsignedInteger('radius')->nullable()->default(150)->change();
        });
    }
};
