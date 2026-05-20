<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sponsor codes on donators & partners
        Schema::table('donators', function (Blueprint $table) {
            $table->string('code', 16)->nullable()->unique()->after('id');
        });
        Schema::table('partners', function (Blueprint $table) {
            $table->string('code', 16)->nullable()->unique()->after('id');
        });

        // 2. Backfill unique codes for existing rows
        foreach (DB::table('donators')->whereNull('code')->pluck('id') as $id) {
            DB::table('donators')->where('id', $id)->update(['code' => $this->uniqueCode('donators')]);
        }
        foreach (DB::table('partners')->whereNull('code')->pluck('id') as $id) {
            DB::table('partners')->where('id', $id)->update(['code' => $this->uniqueCode('partners')]);
        }

        // 3. Event ↔ sponsor pivots
        Schema::create('event_donator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('donator_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['event_id', 'donator_id']);
        });

        Schema::create('event_partner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['event_id', 'partner_id']);
        });
    }

    private function uniqueCode(string $table): string
    {
        do {
            $code = 'ETH-' . Str::upper(Str::random(6));
        } while (DB::table($table)->where('code', $code)->exists());

        return $code;
    }

    public function down(): void
    {
        Schema::dropIfExists('event_partner');
        Schema::dropIfExists('event_donator');

        Schema::table('donators', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
        Schema::table('partners', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};
