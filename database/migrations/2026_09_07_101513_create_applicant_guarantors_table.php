<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('applicant_guarantors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('sequence'); // 1 or 2
            $table->string('full_name');
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('nationality')->default('Filipino');
            $table->text('address')->nullable();
            $table->string('residence_cert_no')->nullable();
            $table->date('residence_cert_issued_at')->nullable();
            $table->string('residence_cert_place')->nullable();
            $table->string('relationship')->nullable();
            $table->timestamps();

            $table->unique(['applicant_id', 'sequence']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('applicant_guarantors');
    }
};