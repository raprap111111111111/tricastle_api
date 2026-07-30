<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_expiry_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_document_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('applicant_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->integer('days_until_expiry');
            $table->enum('alert_type', ['30_days', '60_days', '90_days', 'expired']);
            $table->boolean('email_sent')->default(false);
            $table->boolean('notification_sent')->default(false);
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('notification_sent_at')->nullable();
            $table->date('expiry_date');
            $table->timestamps();

            $table->index('alert_type');
            $table->index('expiry_date');
            $table->index('email_sent');
            $table->unique(['applicant_document_id', 'alert_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_expiry_alerts');
    }
};