<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('correction_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code')->unique(); // CR-2024-0001
            $table->foreignId('document_verification_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('applicant_document_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('requested_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->enum('severity', [
                'low',
                'moderate',
                'critical'
            ])->default('low');
            $table->enum('status', [
                'pending',
                'under_review',
                'approved',
                'rejected',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->text('description');
            $table->json('fields_to_correct')->nullable(); // Which fields
            $table->json('correction_data')->nullable();   // New values
            $table->text('justification')->nullable();
            $table->boolean('requires_approval')->default(false);
            $table->boolean('requires_new_document')->default(false);
            $table->timestamp('due_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('severity');
            $table->index('request_code');
            $table->index('requires_approval');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correction_requests');
    }
};