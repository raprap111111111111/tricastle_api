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
        Schema::create('document_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shared_by')->constrained('users');
            $table->foreignId('shared_with')->constrained('users');
            $table->enum('permission', ['view', 'comment', 'edit']);
            $table->text('message')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('shared_with');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_shares');
    }
};
