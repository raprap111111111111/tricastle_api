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

            $table->enum('status', [
                'applied',
                'shortlisted',
                'interview_scheduled',
                'interview_passed',
                'interview_failed',
                'medical_pending',
                'medical_passed',
                'medical_failed',
                'exam_pending',
                'exam_passed',
                'exam_failed',
                'accepted',
                'rejected',
                'withdrawn',
                'deployed',
            ])->default('applied');

            $table->date('applied_at')->useCurrent();
            $table->date('interview_date')->nullable();
            $table->date('medical_date')->nullable();
            $table->date('exam_date')->nullable();
            $table->date('accepted_at')->nullable();
            $table->date('deployed_at')->nullable();

            $table->decimal('exam_score', 5, 2)->nullable();
            $table->text('interview_notes')->nullable();
            $table->text('medical_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->foreignId('processed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate application to same batch
            $table->unique(['applicant_id', 'batch_id']);
            $table->index('status');
            $table->index('applied_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_batches');
    }
};