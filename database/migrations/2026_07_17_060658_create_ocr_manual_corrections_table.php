<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocr_manual_corrections', function (Blueprint $table) {
            $table->id();

            // ============================================
            // 🔗 Relationships
            // ============================================
            $table->foreignId('ocr_job_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('ocr_field_extraction_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('applicant_document_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('ocr_template_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            // ============================================
            // 📋 Field Information
            // ============================================
            $table->string('field_name');
            $table->string('field_label');
            $table->string('field_type');

            // ============================================
            // 📝 Correction Details
            // ============================================
            $table->text('original_value')->nullable();        // What OCR extracted
            $table->text('corrected_value');                   // What it should be
            $table->text('previous_correction')->nullable();   // If corrected before
            $table->text('reason')->nullable();                // Why it was wrong
            $table->text('explanation')->nullable();           // Detailed explanation

            // ============================================
            // 🏷️ Correction Classification
            // ============================================
            $table->enum('correction_type', [
                'ocr_misread',              // Character recognition error
                'wrong_field',              // Extracted wrong field
                'missed_field',             // Field not extracted
                'format_error',             // Wrong format (date, number)
                'partial_extraction',       // Only got part of value
                'wrong_language',           // Language detection error
                'poor_image_quality',       // Image was bad
                'template_mismatch',        // Wrong template used
                'punctuation_error',        // Missing/wrong punctuation
                'case_error',               // Upper/lower case wrong
                'spacing_error',            // Extra or missing spaces
                'special_character',        // Special char misread
                'number_letter_confusion',  // 0 vs O, 1 vs l, etc.
                'similar_character',        // e.g., 5 vs S
                'handwritten_text',         // Handwriting issue
                'stamp_or_seal',            // Overlapping stamps
                'other'
            ])->nullable();

            $table->enum('severity', [
                'trivial',      // Minor cosmetic
                'minor',        // Small correction
                'moderate',     // Notable fix
                'major',        // Significant change
                'critical'      // Big error caught
            ])->default('minor');

            // ============================================
            // 📊 Impact Metrics
            // ============================================
            $table->decimal('confidence_before', 5, 2)->nullable();
            $table->decimal('confidence_after', 5, 2)->nullable();
            $table->integer('characters_changed')->nullable();
            $table->decimal('similarity_score', 5, 2)->nullable(); // How similar
            $table->integer('edit_distance')->nullable();          // Levenshtein
            $table->boolean('was_critical_field')->default(false); // Important field?

            // ============================================
            // 👤 User Info
            // ============================================
            $table->foreignId('corrected_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('reviewed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // ============================================
            // ✅ Verification of Correction
            // ============================================
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->boolean('is_disputed')->default(false);
            $table->text('dispute_reason')->nullable();

            // ============================================
            // 🧠 Machine Learning / Training
            // ============================================
            $table->boolean('used_for_training')->default(false);
            $table->timestamp('trained_at')->nullable();
            $table->string('training_batch_id')->nullable();
            $table->boolean('improved_accuracy')->default(false);
            $table->decimal('accuracy_improvement', 5, 2)->nullable();
            $table->boolean('added_to_pattern_library')->default(false);

            // ============================================
            // 📈 Pattern Detection
            // ============================================
            $table->boolean('is_recurring_error')->default(false); // Same error many times
            $table->integer('occurrence_count')->default(1);
            $table->json('similar_correction_ids')->nullable();    // Related corrections
            $table->string('error_pattern_id')->nullable();        // Group similar errors

            // ============================================
            // 🖼️ Context Information
            // ============================================
            $table->string('provider_used')->nullable();       // Which OCR provider
            $table->string('template_used')->nullable();       // Template code
            $table->json('image_metadata')->nullable();        // Image details
            $table->json('surrounding_text')->nullable();      // Text near the field
            $table->json('field_position')->nullable();        // Where on document

            // ============================================
            // ⏱️ Timing
            // ============================================
            $table->integer('time_to_correct_seconds')->nullable(); // How long staff took
            $table->timestamp('correction_started_at')->nullable();
            $table->timestamp('correction_completed_at')->nullable();

            // ============================================
            // 📝 Additional Info
            // ============================================
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->json('tags')->nullable();

            $table->timestamps();

            // ============================================
            // 🔍 Indexes
            // ============================================
            $table->index('field_name');
            $table->index('correction_type');
            $table->index('severity');
            $table->index('used_for_training');
            $table->index('is_verified');
            $table->index('is_recurring_error');
            $table->index('is_disputed');
            $table->index('was_critical_field');
            $table->index(['field_name', 'correction_type']);
            $table->index(['corrected_by', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocr_manual_corrections');
    }
};