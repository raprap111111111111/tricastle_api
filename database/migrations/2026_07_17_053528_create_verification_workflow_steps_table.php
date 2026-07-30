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
        Schema::create('verification_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_verification_id')->constrained()->cascadeOnDelete();
            $table->enum('step', [
                'uploaded',
                'ocr_extraction',
                'staff_review',
                'mismatch_detection',
                'correction_requested',
                'supervisor_approval',
                'admin_approval',
                'final_verified',
                'rejected'
            ]);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'skipped', 'failed']);
            $table->foreignId('performed_by')->nullable()->constrained('users');
            $table->json('data')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['document_verification_id', 'step']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_workflow_steps');
    }
};
