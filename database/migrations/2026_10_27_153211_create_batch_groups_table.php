<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_groups', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('batch_id')
                  ->constrained('batches')
                  ->cascadeOnDelete();
                  
            $table->string('name', 100); // e.g., "Group 1", "Group A"
            $table->string('color', 50)->default('blue'); // For UI badges
            $table->integer('max_slots')->nullable(); // Optional capacity limit
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_groups');
    }
};