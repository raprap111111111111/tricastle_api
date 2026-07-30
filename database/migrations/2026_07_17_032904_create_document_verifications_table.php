<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_document_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('reviewed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->enum('status', [
                'pending',
                'in_progress',
                'completed',
                'requires_correction',
                'approved',
                'rejected'
            ])->default('pending');
            $table->json('verification_data')->nullable(); // Staff input data
            $table->json('source_data')->nullable();       // Original doc data
            $table->decimal('match_percentage', 5, 2)->default(0);
            $table->integer('total_fields')->default(0);
            $table->integer('matched_fields')->default(0);
            $table->integer('mismatched_fields')->default(0);
            $table->integer('missing_fields')->default(0);
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent_seconds')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('verified_by');
            $table->index('match_percentage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_verifications');
    }
};