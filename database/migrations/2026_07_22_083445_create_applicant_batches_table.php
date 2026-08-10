<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')
                  ->constrained('applicants')
                  ->cascadeOnDelete();
            $table->foreignId('batch_id')
                  ->constrained('batches')
                  ->cascadeOnDelete();

            // ─── Batch-specific Status ───────────────
            // This is the applicant's journey WITHIN a batch
            $table->enum('status', [
                'assigned',              // Just assigned to batch
                'interview_scheduled',
                'interview_passed',
                'interview_failed',
                'medical_pending',
                'medical_passed',
                'medical_failed',
                'exam_pending',
                'exam_passed',
                'exam_failed',
                'accepted',              // Passed everything
                'rejected',              // Failed in batch process
                'withdrawn',             // Applicant withdrew
                'deployed',              // Successfully deployed
                'returned',              // 🏠 Came home before contract ended
                'completed',             // ✅ Contract completed successfully
            ])->default('assigned');

            // ─── Dates ──────────────────────────────
            $table->timestamp('assigned_at')->useCurrent();
            $table->date('interview_date')->nullable();
            $table->date('medical_date')->nullable();
            $table->date('exam_date')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('deployed_at')->nullable();

            // ─── Scores & Notes ─────────────────────
            $table->decimal('exam_score', 5, 2)->nullable();
            $table->text('interview_notes')->nullable();
            $table->text('medical_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('remarks')->nullable();

            // ─── Processed By ───────────────────────
            $table->foreignId('processed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate assignment to same batch
            $table->unique(['applicant_id', 'batch_id']);
            $table->index('status');
            $table->index('assigned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_batches');
    }
};