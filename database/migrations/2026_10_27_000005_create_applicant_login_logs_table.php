<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('applicant_login_logs')) {
            Schema::create('applicant_login_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('applicant_portal_account_id')
                      ->nullable()
                      ->constrained()
                      ->nullOnDelete();
                $table->string('email')->index();
                $table->enum('status', ['success', 'failed'])->index();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 500)->nullable();
                $table->boolean('password_was_hashed')->default(true);
                $table->timestamp('attempted_at')->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('applicant_login_logs');
    }
};
