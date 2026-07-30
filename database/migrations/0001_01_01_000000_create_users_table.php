<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // ============================================
            // 👤 Personal Information
            // ============================================
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();              // Jr., Sr., III
            $table->string('full_name')->nullable();           // Auto-generated
            
            // ============================================
            // 📧 Contact Information
            // ============================================
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            
            // ============================================
            // 🔐 Authentication
            // ============================================
            $table->string('password')->nullable();            // Nullable for social login
            $table->rememberToken();
            
            // ============================================
            // 🖼️ Profile
            // ============================================
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            
            // ============================================
            // 💼 Employment Details
            // ============================================
            $table->string('employee_code')->unique()->nullable();  // EMP-001
            $table->string('department')->nullable();               // HR, Documents, etc.
            $table->string('position')->nullable();                 // Job title
            $table->date('hired_date')->nullable();
            $table->foreignId('supervisor_id')                      // Reports to
                  ->nullable()
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
            
            // ============================================
            // 🏠 Address
            // ============================================
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('country')->default('Philippines');
            $table->string('postal_code')->nullable();
            
            // ============================================
            // ⚙️ Account Status
            // ============================================
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->integer('login_count')->default(0);
            
            // ============================================
            // 🔒 Security
            // ============================================
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            
            // ============================================
            // 🎨 Preferences
            // ============================================
            $table->string('locale', 10)->default('en');
            $table->string('timezone')->default('Asia/Manila');
            $table->string('theme', 20)->default('light');    // light, dark
            $table->json('preferences')->nullable();           // Custom preferences
            
            // ============================================
            // 📊 Metadata
            // ============================================
            $table->json('metadata')->nullable();              // Extra flexible data
            $table->text('notes')->nullable();                 // Admin notes
            
            // ============================================
            // 📅 Timestamps
            // ============================================
            $table->timestamps();
            $table->softDeletes();
            
            // ============================================
            // 🔍 Indexes for Performance
            // ============================================
            $table->index('email');
            $table->index('employee_code');
            $table->index('is_active');
            $table->index('department');
            $table->index('last_login_at');
            $table->index(['first_name', 'last_name']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};