<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('internship_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_internship_id')->constrained()->cascadeOnDelete();
            $table->string('document_type')->default('moa'); // moa | contract | acceptance_letter
            $table->string('document_no')->unique();
            $table->string('file_path');
            $table->string('status')->default('generated');  // generated | signed | void
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('snapshot')->nullable();            // Complete audit snapshot of data used
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('internship_documents');
    }
};