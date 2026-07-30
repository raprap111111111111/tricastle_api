<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('team')->nullable()->after('assigned_staff_id');
            $table->foreignId('supervisor_id')
                  ->nullable()
                  ->after('team')
                  ->constrained('users')
                  ->nullOnDelete();
            $table->enum('visibility', ['public', 'team', 'private'])
                  ->default('public')
                  ->after('supervisor_id');
            
            $table->index('team');
            $table->index('visibility');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn(['team', 'supervisor_id', 'visibility']);
        });
    }
};