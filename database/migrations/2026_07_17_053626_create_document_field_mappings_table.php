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
        Schema::create('document_field_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_document_id')->constrained()->cascadeOnDelete();
            $table->string('field_name');
            $table->string('field_label');
            $table->text('ocr_value')->nullable();          // What OCR read
            $table->text('staff_entered_value')->nullable(); // What staff typed
            $table->text('final_value')->nullable();         // Final approved
            $table->decimal('confidence_score', 5, 2)->nullable(); // OCR confidence
            $table->boolean('is_matched')->default(false);
            $table->boolean('was_corrected')->default(false);
            $table->timestamps();

            $table->index(['applicant_document_id', 'field_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_field_mappings');
    }
};
