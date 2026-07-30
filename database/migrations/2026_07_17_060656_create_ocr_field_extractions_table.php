<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocr_field_extractions', function (Blueprint $table) {
            $table->id();

            // ============================================
            // 🔗 Relationships
            // ============================================
            $table->foreignId('ocr_job_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('applicant_document_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // ============================================
            // 📋 Field Information
            // ============================================
            $table->string('field_name');                     // passport_number
            $table->string('field_label');                    // "Passport Number"
            $table->string('field_type');                     // text, date, number, email
            $table->string('field_category')->nullable();     // personal, contact, document
            $table->boolean('is_required')->default(false);
            $table->boolean('is_primary_field')->default(false); // Key field
            $table->integer('sort_order')->default(0);

            // ============================================
            // 📤 Extraction Results
            // ============================================
            $table->text('extracted_value')->nullable();      // Raw from OCR
            $table->text('normalized_value')->nullable();     // Cleaned/formatted
            $table->text('validated_value')->nullable();      // After validation
            $table->text('final_value')->nullable();          // After manual review
            $table->text('display_value')->nullable();        // For display purposes

            // ============================================
            // 🎯 Confidence & Quality
            // ============================================
            $table->decimal('confidence_score', 5, 2)->default(0); // 0-100%
            $table->enum('confidence_level', [
                'very_high',    // >95%
                'high',         // 85-95%
                'medium',       // 70-85%
                'low',          // 50-70%
                'very_low',     // <50%
                'unknown'       // No confidence data
            ])->default('unknown');

            $table->decimal('character_confidence', 5, 2)->nullable(); // Char-level
            $table->decimal('word_confidence', 5, 2)->nullable();      // Word-level
            $table->integer('character_count')->nullable();
            $table->integer('word_count')->nullable();

            // ============================================
            // 📍 Location on Document
            // ============================================
            $table->json('bounding_box')->nullable();          // {x, y, width, height}
            $table->integer('page_number')->default(1);
            $table->decimal('x_coordinate', 8, 2)->nullable();
            $table->decimal('y_coordinate', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('rotation_angle', 5, 2)->nullable();

            // ============================================
            // ✅ Validation
            // ============================================
            $table->boolean('passed_validation')->default(false);
            $table->boolean('has_validation_errors')->default(false);
            $table->text('validation_errors')->nullable();
            $table->string('validation_rule_used')->nullable();
            $table->json('validation_details')->nullable();

            // ============================================
            // 📊 Status
            // ============================================
            $table->enum('status', [
                'extracted',            // OCR extracted
                'validated',            // Passed validation
                'requires_review',      // Low confidence, needs check
                'manually_corrected',   // Staff corrected it
                'accepted',             // Approved as-is
                'rejected',             // Bad extraction, ignored
                'missing',              // Field not found
                'skipped',              // Intentionally skipped
                'auto_filled'           // Filled from another source
            ])->default('extracted');

            // ============================================
            // ✏️ Manual Review & Correction
            // ============================================
            $table->boolean('was_manually_corrected')->default(false);
            $table->text('original_ocr_value')->nullable();   // Before any changes
            $table->foreignId('corrected_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->text('correction_reason')->nullable();
            $table->timestamp('corrected_at')->nullable();
            $table->integer('correction_count')->default(0);   // Times corrected

            // ============================================
            // 🔄 Cross-Reference (Comparing across documents)
            // ============================================
            $table->boolean('matches_other_documents')->default(false);
            $table->json('cross_reference_matches')->nullable(); // Which docs match
            $table->boolean('has_conflicts')->default(false);
            $table->json('conflict_details')->nullable();

            // ============================================
            // 🧠 AI/ML Predictions
            // ============================================
            $table->json('suggested_alternatives')->nullable(); // Alt values
            $table->decimal('spell_check_score', 5, 2)->nullable();
            $table->boolean('has_typo_suggestions')->default(false);

            // ============================================
            // 📝 Additional Info
            // ============================================
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->string('source')->default('ocr');          // ocr, manual, api

            $table->timestamps();

            // ============================================
            // 🔍 Indexes
            // ============================================
            $table->index('field_name');
            $table->index('confidence_level');
            $table->index('status');
            $table->index('is_primary_field');
            $table->index('was_manually_corrected');
            $table->index(['ocr_job_id', 'field_name']);
            $table->index(['ocr_job_id', 'status']);
            $table->index(['applicant_document_id', 'field_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocr_field_extractions');
    }
};