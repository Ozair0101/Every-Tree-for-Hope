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
        Schema::table('faqs', function (Blueprint $table) {
            $table->json('category_i18n')->nullable()->after('category');
            $table->json('question_i18n')->nullable()->after('question');
            $table->json('answer_i18n')->nullable()->after('answer');
        });

        // 2. Backfill existing plain values as the English ("en") translation
        foreach (DB::table('faqs')->select('id', 'category', 'question', 'answer')->get() as $row) {
            DB::table('faqs')->where('id', $row->id)->update([
                'category_i18n' => $row->category !== null
                    ? json_encode(['en' => (string) $row->category], JSON_UNESCAPED_UNICODE)
                    : null,
                'question_i18n' => json_encode(['en' => (string) $row->question], JSON_UNESCAPED_UNICODE),
                'answer_i18n' => $row->answer !== null
                    ? json_encode(['en' => (string) $row->answer], JSON_UNESCAPED_UNICODE)
                    : null,
            ]);
        }

        // 3. Drop the old plain columns
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropColumn(['category', 'question', 'answer']);
        });

        // 4. Rename the JSON columns into place
        Schema::table('faqs', function (Blueprint $table) {
            $table->renameColumn('category_i18n', 'category');
            $table->renameColumn('question_i18n', 'question');
            $table->renameColumn('answer_i18n', 'answer');
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->string('category_plain')->nullable()->after('category');
            $table->text('question_plain')->nullable()->after('question');
            $table->text('answer_plain')->nullable()->after('answer');
        });

        $pick = function ($json) {
            $v = json_decode((string) $json, true);
            if (is_array($v)) {
                return $v['en'] ?? (reset($v) ?: null);
            }
            return $json;
        };

        foreach (DB::table('faqs')->select('id', 'category', 'question', 'answer')->get() as $row) {
            DB::table('faqs')->where('id', $row->id)->update([
                'category_plain' => $pick($row->category),
                'question_plain' => (string) $pick($row->question),
                'answer_plain' => $pick($row->answer),
            ]);
        }

        Schema::table('faqs', function (Blueprint $table) {
            $table->dropColumn(['category', 'question', 'answer']);
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->renameColumn('category_plain', 'category');
            $table->renameColumn('question_plain', 'question');
            $table->renameColumn('answer_plain', 'answer');
        });
    }
};
