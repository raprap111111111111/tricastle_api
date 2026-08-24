<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_japan_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')
                  ->constrained('applicants')
                  ->cascadeOnDelete();

            // 'marucon' or 'non_marucon'
            $table->enum('affiliation_type', ['marucon', 'non_marucon']);

            $table->string('name');
            $table->string('batch_no')->nullable();
            $table->string('company_name')->nullable();
            $table->string('relation')->nullable();
            $table->string('contact_number')->nullable();

            $table->timestamps();

            $table->index('applicant_id');
            $table->index('affiliation_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_japan_contacts');
    }
};
