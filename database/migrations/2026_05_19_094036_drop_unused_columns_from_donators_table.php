<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donators', function (Blueprint $table) {
            $table->dropColumn([
                'location_type',
                'email',
                'trees_sponsored',
                'is_featured',
                'notes',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('donators', function (Blueprint $table) {
            $table->string('location_type')->nullable();
            $table->string('email')->nullable();
            $table->integer('trees_sponsored')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->text('notes')->nullable();
        });
    }
};
