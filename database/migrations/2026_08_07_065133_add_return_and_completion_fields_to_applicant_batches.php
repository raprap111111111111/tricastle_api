<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicant_batches', function (Blueprint $table) {
            // ─── 🏠 Returned home early (before contract ended) ──
            $table->timestamp('returned_at')->nullable()->after('cancelled_by');
            $table->text('return_reason')->nullable()->after('returned_at');
            $table->foreignId('returned_by')
                  ->nullable()
                  ->after('return_reason')
                  ->constrained('users')
                  ->nullOnDelete();

            // ─── ✅ Contract completed successfully ──────────────
            $table->timestamp('completed_at')->nullable()->after('returned_by');
            $table->text('completion_notes')->nullable()->after('completed_at');
            $table->foreignId('completed_by')
                  ->nullable()
                  ->after('completion_notes')
                  ->constrained('users')
                  ->nullOnDelete();

            // ─── Indexes for filtering ──────────────────────────
            $table->index('returned_at');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('applicant_batches', function (Blueprint $table) {
            $table->dropForeign(['returned_by']);
            $table->dropForeign(['completed_by']);

            $table->dropIndex(['returned_at']);
            $table->dropIndex(['completed_at']);

            $table->dropColumn([
                'returned_at',
                'return_reason',
                'returned_by',
                'completed_at',
                'completion_notes',
                'completed_by',
            ]);
        });
    }
};