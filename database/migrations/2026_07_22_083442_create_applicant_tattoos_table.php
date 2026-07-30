<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_tattoos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')
                  ->constrained('applicants')
                  ->cascadeOnDelete();

            $table->string('location');                     // "left arm", "back"
            $table->enum('size', ['small', 'medium', 'large'])->nullable();
            $table->text('description')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_visible')->default(true);   // visible with normal clothes?

            $table->timestamps();

            $table->index('applicant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_tattoos');
    }
};