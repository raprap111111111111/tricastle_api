<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicant_documents', function (Blueprint $table) {
            $table->enum('visibility', ['public', 'restricted', 'confidential'])
                  ->default('public')
                  ->after('priority');
            $table->boolean('is_locked')->default(false)->after('visibility');
            $table->foreignId('locked_by')
                  ->nullable()
                  ->after('is_locked')
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('locked_at')->nullable()->after('locked_by');
            
            $table->index('visibility');
            $table->index('is_locked');
        });
    }

    public function down(): void
    {
        Schema::table('applicant_documents', function (Blueprint $table) {
            $table->dropForeign(['locked_by']);
            $table->dropColumn(['visibility', 'is_locked', 'locked_by', 'locked_at']);
        });
    }
};