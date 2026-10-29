<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicant_batches', function (Blueprint $table) {
            $table->foreignId('batch_group_id')
                  ->nullable()
                  ->after('batch_id')
                  ->constrained('batch_groups')
                  ->nullOnDelete(); // If a group is deleted, keep applicant in batch, just remove group
        });
    }

    public function down(): void
    {
        Schema::table('applicant_batches', function (Blueprint $table) {
            $table->dropForeign(['batch_group_id']);
            $table->dropColumn('batch_group_id');
        });
    }
};