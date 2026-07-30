<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();              
            $table->string('name');
            $table->string('name_japanese')->nullable();

            $table->foreignId('category_id')
                  ->constrained('company_categories')
                  ->restrictOnDelete();

            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('prefecture')->nullable();       
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Japan');

            $table->string('contact_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};