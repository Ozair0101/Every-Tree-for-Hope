<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module B — Field Data Capture.
 *
 * Two additions to the planting record so it can be captured offline and
 * represent a batch:
 *
 *  - client_uuid: the idempotency key. The app queues a planting while offline
 *    and replays it when the signal returns; without a client-generated key a
 *    dropped response would turn one planting into two. Unique, so the second
 *    arrival is recognised as the same write (see {@see \App\Models\Concerns\HasClientUuid}).
 *  - tree_count: a field record is often "40 saplings at this spot", not a
 *    single stem. Defaults to 1 so every existing row keeps its meaning.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            $table->uuid('client_uuid')->nullable()->unique()->after('id');
            $table->unsignedInteger('tree_count')->default(1)->after('species');
        });
    }

    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            $table->dropUnique(['client_uuid']);
            $table->dropColumn(['client_uuid', 'tree_count']);
        });
    }
};
