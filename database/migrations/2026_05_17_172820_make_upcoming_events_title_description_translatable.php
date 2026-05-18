<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add JSON holding columns
        Schema::table('upcoming_events', function (Blueprint $table) {
            $table->json('title_i18n')->nullable()->after('title');
            $table->json('description_i18n')->nullable()->after('description');
        });

        // 2. Backfill existing plain values as the English ("en") translation
        foreach (DB::table('upcoming_events')->select('id', 'title', 'description')->get() as $row) {
            DB::table('upcoming_events')->where('id', $row->id)->update([
                'title_i18n' => json_encode(['en' => (string) $row->title], JSON_UNESCAPED_UNICODE),
                'description_i18n' => json_encode(['en' => (string) $row->description], JSON_UNESCAPED_UNICODE),
            ]);
        }

        // 3. Drop the old plain columns
        Schema::table('upcoming_events', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });

        // 4. Rename the JSON columns into place
        Schema::table('upcoming_events', function (Blueprint $table) {
            $table->renameColumn('title_i18n', 'title');
            $table->renameColumn('description_i18n', 'description');
        });
    }

    public function down(): void
    {
        Schema::table('upcoming_events', function (Blueprint $table) {
            $table->string('title_plain')->nullable()->after('title');
            $table->text('description_plain')->nullable()->after('description');
        });

        foreach (DB::table('upcoming_events')->select('id', 'title', 'description')->get() as $row) {
            $t = json_decode((string) $row->title, true);
            $d = json_decode((string) $row->description, true);
            DB::table('upcoming_events')->where('id', $row->id)->update([
                'title_plain' => is_array($t) ? ($t['en'] ?? (reset($t) ?: '')) : (string) $row->title,
                'description_plain' => is_array($d) ? ($d['en'] ?? (reset($d) ?: '')) : (string) $row->description,
            ]);
        }

        Schema::table('upcoming_events', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });

        Schema::table('upcoming_events', function (Blueprint $table) {
            $table->renameColumn('title_plain', 'title');
            $table->renameColumn('description_plain', 'description');
        });
    }
};
