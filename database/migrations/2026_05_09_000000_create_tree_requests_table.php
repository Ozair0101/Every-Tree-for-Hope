<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tree_requests', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->unsignedInteger('number_of_trees');
            $table->string('water_source')->nullable();
            $table->string('responsible_person');
            $table->string('phone_whatsapp');
            $table->json('media_paths')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tree_requests');
    }
};
