<?php

use App\Models\Donator;
use App\Models\Partner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widen the code column and replace the old random sponsor codes
     * (ETH-XXXXXX) with new human-meaningful, name-based codes
     * (e.g. ETH-ABDULKARIMKH-01).
     */
    public function up(): void
    {
        Schema::table('donators', function (Blueprint $table) {
            $table->string('code', 40)->nullable()->change();
        });
        Schema::table('partners', function (Blueprint $table) {
            $table->string('code', 40)->nullable()->change();
        });

        foreach (Donator::query()->orderBy('id')->get() as $donator) {
            $donator->forceFill([
                'code' => Donator::generateUniqueSponsorCode($donator->sponsorCodeSource()),
            ])->saveQuietly();
        }

        foreach (Partner::query()->orderBy('id')->get() as $partner) {
            $partner->forceFill([
                'code' => Partner::generateUniqueSponsorCode($partner->sponsorCodeSource()),
            ])->saveQuietly();
        }
    }

    public function down(): void
    {
        // One-way data normalisation — nothing to revert.
    }
};
