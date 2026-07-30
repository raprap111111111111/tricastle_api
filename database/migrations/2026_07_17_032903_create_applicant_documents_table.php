<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_documents', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('applicant_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('document_type_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('file_repository_id')
                  ->nullable()
                  ->constrained('file_repository')
                  ->nullOnDelete();

            // File Information (denormalized for quick access)
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();          // pdf, jpg, png
            $table->bigInteger('file_size')->nullable();      // in bytes
            $table->string('mime_type')->nullable();
            $table->string('file_hash')->nullable();          // SHA256 for verification

            // OCR & Extraction Data
            $table->json('extracted_data')->nullable();        // Raw OCR data
            $table->json('validated_data')->nullable();        // Cleaned/verified data
            $table->decimal('ocr_confidence', 5, 2)->nullable(); // OCR confidence score

            // Document Status
            $table->enum('status', [
                'uploaded',
                'pending_verification',
                'under_review',
                'verified',
                'rejected',
                'expired',
                'requires_correction'
            ])->default('uploaded');

            // Document Dates
            $table->date('document_date')->nullable();         // Issue date on document
            $table->date('expiry_date')->nullable();           // Document expiration
            $table->boolean('is_expired')->default(false);
            $table->boolean('expiry_notified')->default(false);

            // Versioning
            $table->integer('version')->default(1);
            $table->boolean('is_current_version')->default(true);

            // Verification Metadata
            $table->timestamp('last_verified_at')->nullable();
            $table->foreignId('last_verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Upload Info
            $table->foreignId('uploaded_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Rejection Info
            $table->text('rejection_reason')->nullable();
            $table->foreignId('rejected_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();

            // Additional Metadata
            $table->json('metadata')->nullable();              // Extra info (page count, etc.)
            $table->text('notes')->nullable();                 // Internal notes

            // Priority (for verification queue)
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])
                  ->default('normal');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('status');
            $table->index('expiry_date');
            $table->index('is_expired');
            $table->index('priority');
            $table->index('is_current_version');
            $table->index(['applicant_id', 'document_type_id']);
            $table->index(['status', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_documents');
    }
};