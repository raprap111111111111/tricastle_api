<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_mismatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_verification_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('field_name');         // e.g., first_name, passport_no
            $table->string('field_label');        // Human readable label
            $table->text('source_value')->nullable();      // Value from document
            $table->text('entered_value')->nullable();     // Value staff entered
            $table->enum('severity', [
                'low',
                'moderate',
                'critical'
            ])->default('low');
            $table->enum('mismatch_type', [
                'value_mismatch',
                'missing_in_document',
                'missing_in_system',
                'format_mismatch',
                'date_mismatch'
            ])->default('value_mismatch');
            $table->enum('status', [
                'open',
                'correction_requested',
                'corrected',
                'waived',
                'escalated'
            ])->default('open');
            $table->text('resolution_notes')->nullable();
            $table->foreignId('resolved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('severity');
            $table->index('status');
            $table->index('field_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_mismatches');
    }
};