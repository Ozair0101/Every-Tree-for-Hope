<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catch-up migration for the device columns on `push_tokens`.
 *
 * `2026_07_26_120400_create_push_notification_tables` declares these columns in
 * a Schema::table() block, but that block was added to the file after the
 * migration had already been recorded as run — so it never executed against a
 * database that had already migrated. The PushToken model meanwhile depends on
 * `is_active`, `failure_count` and `locale`, which left every device
 * registration failing with "Unknown column 'is_active'".
 *
 * Each column is guarded by hasColumn(), so this is a no-op on a database built
 * from scratch (where 120400 does create them) and a repair on one that was
 * migrated before the edit. Both paths end in the same schema, which is the
 * only property that matters here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('push_tokens', function (Blueprint $table) {
            if (! Schema::hasColumn('push_tokens', 'device_id')) {
                $table->string('device_id', 100)->nullable()->after('platform');
            }
            if (! Schema::hasColumn('push_tokens', 'device_name')) {
                $table->string('device_name')->nullable()->after('device_id');
            }
            if (! Schema::hasColumn('push_tokens', 'app_version')) {
                $table->string('app_version', 20)->nullable()->after('device_name');
            }
            if (! Schema::hasColumn('push_tokens', 'os_version')) {
                $table->string('os_version', 20)->nullable()->after('app_version');
            }
            if (! Schema::hasColumn('push_tokens', 'locale')) {
                $table->string('locale', 5)->default('en')->after('os_version');
            }
            if (! Schema::hasColumn('push_tokens', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('locale');
            }
            if (! Schema::hasColumn('push_tokens', 'failure_count')) {
                $table->unsignedTinyInteger('failure_count')->default(0)->after('is_active');
            }
        });

        // Indexes are added separately: adding one for a column created in the
        // same closure fails on MySQL, which resolves the whole blueprint
        // against the pre-migration schema.
        Schema::table('push_tokens', function (Blueprint $table) {
            foreach (
                [
                    'push_tokens_user_id_is_active_index' => ['user_id', 'is_active'],
                    'push_tokens_device_id_is_active_index' => ['device_id', 'is_active'],
                ] as $name => $columns
            ) {
                if (! $this->indexExists($name)) {
                    $table->index($columns, $name);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('push_tokens', function (Blueprint $table) {
            foreach (['push_tokens_user_id_is_active_index', 'push_tokens_device_id_is_active_index'] as $name) {
                if ($this->indexExists($name)) {
                    $table->dropIndex($name);
                }
            }

            $table->dropColumn([
                'device_id',
                'device_name',
                'app_version',
                'os_version',
                'locale',
                'is_active',
                'failure_count',
            ]);
        });
    }

    private function indexExists(string $name): bool
    {
        return collect(Schema::getIndexes('push_tokens'))
            ->contains(fn (array $index) => $index['name'] === $name);
    }
};
