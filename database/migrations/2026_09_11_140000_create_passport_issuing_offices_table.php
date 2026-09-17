<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ⚠️ MAKE SURE "return" IS WRITTEN HERE BEFORE "new class"
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passport_issuing_offices', function (Blueprint $table) {
            $table->id();
            $table->string('region')->nullable();
            $table->string('name');
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passport_issuing_offices');
    }
};