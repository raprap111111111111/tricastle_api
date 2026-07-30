<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocr_templates', function (Blueprint $table) {
            $table->id();

            // ============================================
            // 📋 Template Information
            // ============================================
            $table->foreignId('document_type_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('name');                            // "Philippine Passport 2020"
            $table->string('code')->unique();                  // TMPL-PASSPORT-PH-2020
            $table->string('version')->default('1.0.0');
            $table->text('description')->nullable();
            $table->string('thumbnail_path')->nullable();      // Preview image

            // ============================================
            // 🔍 Document Identification
            // ============================================
            $table->json('detection_keywords')->nullable();    // Text patterns to detect
            $table->json('detection_patterns')->nullable();    // Regex patterns
            $table->json('detection_features')->nullable();    // Visual features
            $table->string('sample_image_path')->nullable();   // Reference image
            $table->decimal('detection_threshold', 5, 2)->default(75.00); // Min confidence

            // ============================================
            // 📏 Document Specifications
            // ============================================
            $table->integer('expected_width')->nullable();     // In pixels
            $table->integer('expected_height')->nullable();
            $table->decimal('aspect_ratio', 5, 3)->nullable(); // width/height
            $table->string('orientation')->default('portrait'); // portrait, landscape
            $table->string('paper_size')->nullable();          // A4, Letter, ID card
            $table->integer('expected_pages')->default(1);
            $table->string('color_mode')->nullable();          // color, grayscale, bw

            // ============================================
            // 📋 Field Definitions
            // ============================================
            $table->json('field_definitions');                 // Complete field structure
            $table->json('field_positions')->nullable();       // Expected coordinates
            $table->json('field_relationships')->nullable();   // How fields relate
            $table->json('validation_rules');                  // Per-field rules
            $table->json('required_fields')->nullable();       // Must-have fields
            $table->json('optional_fields')->nullable();

            // ============================================
            // 🤖 OCR Provider Settings
            // ============================================
            $table->enum('preferred_provider', [
                'aws_textract',
                'google_vision',
                'azure_form_recognizer',
                'tesseract',
                'openai_vision',
                'custom_api'
            ])->nullable();

            $table->json('provider_settings')->nullable();     // Provider-specific config
            $table->json('fallback_providers')->nullable();    // Backup providers
            $table->integer('confidence_threshold')->default(70); // Min acceptable

            // ============================================
            // 🖼️ Image Processing
            // ============================================
            $table->boolean('requires_preprocessing')->default(false);
            $table->json('preprocessing_steps')->nullable();   // Rotate, enhance, etc.
            $table->boolean('auto_rotate')->default(true);
            $table->boolean('auto_enhance')->default(false);
            $table->boolean('auto_deskew')->default(true);
            $table->boolean('remove_background')->default(false);
            $table->boolean('binarize')->default(false);

            // ============================================
            // 🌍 Language & Region
            // ============================================
            $table->string('primary_language', 10)->default('en');
            $table->json('supported_languages')->nullable();   // ['en', 'ja', 'tl']
            $table->string('country_code', 2)->default('PH');
            $table->string('region')->nullable();              // Asia, Europe

            // ============================================
            // 📊 Performance Statistics
            // ============================================
            $table->integer('times_used')->default(0);
            $table->integer('successful_scans')->default(0);
            $table->integer('failed_scans')->default(0);
            $table->decimal('success_rate', 5, 2)->default(0);
            $table->decimal('avg_confidence', 5, 2)->default(0);
            $table->decimal('avg_processing_time_ms', 10, 2)->default(0);
            $table->timestamp('last_used_at')->nullable();

            // ============================================
            // 🧠 Learning & Improvement
            // ============================================
            $table->integer('correction_count')->default(0);   // Total manual corrections
            $table->json('common_errors')->nullable();         // Common OCR mistakes
            $table->json('improvement_suggestions')->nullable();
            $table->timestamp('last_trained_at')->nullable();

            // ============================================
            // ⚙️ Status & Configuration
            // ============================================
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);     // Default for doc type
            $table->boolean('is_verified')->default(false);    // Admin verified
            $table->boolean('is_public')->default(true);       // Available to all
            $table->boolean('is_beta')->default(false);        // Testing phase
            $table->integer('priority')->default(5);           // Selection priority

            // ============================================
            // 👥 Access Control
            // ============================================
            $table->json('allowed_roles')->nullable();         // Which roles can use
            $table->json('restricted_users')->nullable();      // Users blocked from using

            // ============================================
            // 👤 Meta Information
            // ============================================
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            // ============================================
            // 📝 Additional Info
            // ============================================
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->text('changelog')->nullable();             // Version changes

            $table->timestamps();
            $table->softDeletes();

            // ============================================
            // 🔍 Indexes
            // ============================================
            $table->index('document_type_id');
            $table->index('code');
            $table->index('is_active');
            $table->index('is_default');
            $table->index('is_verified');
            $table->index('priority');
            $table->index(['primary_language', 'country_code']);
            $table->index(['document_type_id', 'is_active']);
            $table->index('last_used_at');
        });

        // Now add foreign key constraint to ocr_jobs for ocr_template_id
        Schema::table('ocr_jobs', function (Blueprint $table) {
            $table->foreign('ocr_template_id')
                  ->references('id')
                  ->on('ocr_templates')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Remove foreign key first
        Schema::table('ocr_jobs', function (Blueprint $table) {
            $table->dropForeign(['ocr_template_id']);
        });

        Schema::dropIfExists('ocr_templates');
    }
};