<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donators', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->text('impact')->nullable(); // Description of their impact (trees planted, etc.)
            $table->string('location')->nullable(); // Donor's location (city, country)
            $table->decimal('financial_support', 10, 2)->nullable(); // Amount donated
            $table->string('location_type')->nullable(); // Type: International, Local, etc.
            $table->enum('status', ['verified', 'unverified'])->default('unverified');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('profile_image')->nullable(); // Path to profile image
            $table->text('notes')->nullable(); // Additional notes about donor
            $table->date('donation_date')->nullable(); // When they started donating
            $table->integer('trees_sponsored')->default(0); // Number of trees sponsored
            $table->boolean('is_featured')->default(false); // For featuring on website
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donators');
    }
};
