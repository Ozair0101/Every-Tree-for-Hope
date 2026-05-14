<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');                        // e.g. "Standard Sponsorship"
            $table->decimal('price', 10, 2);                // e.g. 100.00
            $table->string('currency', 8)->default('USD');  // USD / EUR / AFN
            $table->unsignedInteger('trees_count');         // e.g. 30
            $table->text('description')->nullable();
            $table->string('badge_label')->nullable();      // e.g. "Most Popular"
            $table->json('allocations')->nullable();        // [{ label, percentage, tone }]
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_packages');
    }
};
