<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('correction_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('correction_request_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('approver_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->enum('decision', [
                'pending',
                'approved',
                'rejected',
                'escalated'
            ])->default('pending');
            $table->text('comments')->nullable();
            $table->json('conditions')->nullable(); // Conditions for approval
            $table->integer('approval_level')->default(1); // 1=supervisor, 2=admin
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->index('decision');
            $table->index('approver_id');
            $table->index('approval_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correction_approvals');
    }
};