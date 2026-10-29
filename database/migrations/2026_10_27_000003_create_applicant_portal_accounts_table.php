<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('applicant_portal_accounts')) {
            Schema::create('applicant_portal_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('applicant_id')->unique()->constrained()->cascadeOnDelete();
                $table->string('email')->unique();
                $table->string('password');
                $table->enum('status', ['active', 'disabled'])->default('active');
                $table->timestamp('email_verified_at')->nullable();
                $table->timestamp('last_login_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('applicant_portal_accounts');
    }
};
