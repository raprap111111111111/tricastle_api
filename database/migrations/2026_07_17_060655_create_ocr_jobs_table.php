<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocr_jobs', function (Blueprint $table) {
            $table->id();

            // ============================================
            // 🆔 Job Identification
            // ============================================
            $table->string('job_code')->unique();              // OCR-2024-0001
            $table->string('batch_id')->nullable();            // For bulk scans
            $table->string('external_job_id')->nullable();     // Provider's job ID

            // ============================================
            // 🔗 Relationships
            // ============================================
            $table->foreignId('applicant_document_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('file_repository_id')
                  ->nullable()
                  ->constrained('file_repository')
                  ->nullOnDelete();
            $table->foreignId('ocr_template_id')
                  ->nullable();                                // Will constrain after templates table exists

            // ============================================
            // 📊 Job Status
            // ============================================
            $table->enum('status', [
                'pending',              // Waiting to start
                'queued',               // In processing queue
                'processing',           // Currently being scanned
                'completed',            // Successfully done
                'failed',               // Scan failed
                'partial',              // Some fields extracted
                'requires_review',      // Low confidence, needs manual
                'cancelled',            // User cancelled
                'timeout',              // Took too long
                'retrying'              // Being retried
            ])->default('pending');

            $table->text('status_message')->nullable();

            // ============================================
            // 🤖 OCR Provider
            // ============================================
            $table->enum('provider', [
                'aws_textract',
                'google_vision',
                'azure_form_recognizer',
                'tesseract',
                'openai_vision',
                'manual',
                'custom_api'
            ])->default('tesseract');

            $table->string('provider_version')->nullable();    // API version used
            $table->json('provider_config')->nullable();       // Provider settings used

            // ============================================
            // 🔍 Detection Results
            // ============================================
            $table->string('detected_document_type')->nullable();
            $table->decimal('detection_confidence', 5, 2)->nullable();
            $table->boolean('is_document_type_matched')->default(false);
            $table->json('alternative_detections')->nullable(); // Other possible types

            // ============================================
            // 📊 Extraction Statistics
            // ============================================
            $table->integer('total_fields_expected')->default(0);
            $table->integer('total_fields_extracted')->default(0);
            $table->integer('total_fields_validated')->default(0);
            $table->integer('very_high_confidence_fields')->default(0);  // >95%
            $table->integer('high_confidence_fields')->default(0);       // 85-95%
            $table->integer('medium_confidence_fields')->default(0);     // 70-85%
            $table->integer('low_confidence_fields')->default(0);        // 50-70%
            $table->integer('very_low_confidence_fields')->default(0);   // <50%
            $table->integer('missing_fields')->default(0);
            $table->decimal('overall_confidence', 5, 2)->default(0);

            // ============================================
            // 🔄 Processing Info
            // ============================================
            $table->integer('attempt_number')->default(1);
            $table->integer('max_attempts')->default(3);
            $table->integer('processing_time_ms')->nullable();
            $table->integer('queue_wait_time_ms')->nullable();
            $table->bigInteger('image_size_bytes')->nullable();
            $table->integer('image_width')->nullable();
            $table->integer('image_height')->nullable();
            $table->integer('page_count')->default(1);
            $table->string('image_format')->nullable();        // jpg, png, pdf

            // ============================================
            // 🖼️ Image Quality Metrics
            // ============================================
            $table->decimal('image_quality_score', 5, 2)->nullable();
            $table->decimal('image_sharpness', 5, 2)->nullable();
            $table->decimal('image_brightness', 5, 2)->nullable();
            $table->boolean('is_rotated')->default(false);
            $table->integer('rotation_angle')->nullable();
            $table->boolean('is_blurry')->default(false);
            $table->boolean('has_glare')->default(false);
            $table->boolean('is_upside_down')->default(false);

            // ============================================
            // 📤 Results Data
            // ============================================
            $table->longText('raw_response')->nullable();      // Full OCR API response
            $table->json('extracted_fields')->nullable();      // Cleaned structured data
            $table->json('bounding_boxes')->nullable();        // Field positions
            $table->longText('extracted_text')->nullable();    // Full extracted text
            $table->json('detected_languages')->nullable();    // Languages found

            // ============================================
            // 🚨 Error Handling
            // ============================================
            $table->text('error_message')->nullable();
            $table->string('error_code')->nullable();
            $table->json('error_details')->nullable();
            $table->boolean('is_recoverable_error')->default(true);

            // ============================================
            // 💰 Cost Tracking
            // ============================================
            $table->decimal('api_cost', 10, 6)->default(0);
            $table->string('cost_currency', 3)->default('USD');
            $table->decimal('cost_per_page', 10, 6)->nullable();
            $table->boolean('is_free_tier')->default(false);

            // ============================================
            // 👤 User Info
            // ============================================
            $table->foreignId('initiated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('reviewed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('cancelled_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // ============================================
            // ⏱️ Timestamps
            // ============================================
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('retry_at')->nullable();

            // ============================================
            // 📝 Additional Info
            // ============================================
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('priority')->default(5);           // 1-10 (10=highest)

            $table->timestamps();
            $table->softDeletes();

            // ============================================
            // 🔍 Indexes
            // ============================================
            $table->index('status');
            $table->index('provider');
            $table->index('job_code');
            $table->index('batch_id');
            $table->index('priority');
            $table->index('detected_document_type');
            $table->index(['status', 'created_at']);
            $table->index(['status', 'priority']);
            $table->index('created_at');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocr_jobs');
    }
};