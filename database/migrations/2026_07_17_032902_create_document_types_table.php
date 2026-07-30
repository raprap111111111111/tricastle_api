<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Passport, NBI Clearance, etc.
            $table->string('code')->unique(); // PASSPORT, NBI, MEDICAL, etc.
            $table->text('description')->nullable();
            $table->json('required_fields')->nullable(); // Fields to extract
            $table->json('validation_rules')->nullable(); // Rules per field
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('validity_days')->nullable(); // e.g., 365 for 1 year
            $table->integer('expiry_warning_days')->default(30);
            $table->string('category')->default('primary'); // primary, supporting
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('is_active');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};