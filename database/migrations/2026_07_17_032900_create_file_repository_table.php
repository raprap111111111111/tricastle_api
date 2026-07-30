<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_repository', function (Blueprint $table) {
            $table->id();
            $table->string('file_hash')->unique(); // SHA256 hash (dedup)
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->string('disk')->default('local'); // local, s3, etc.
            $table->string('storage_driver')->default('local');
            $table->integer('reference_count')->default(1); // How many docs use it
            $table->json('metadata')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->foreignId('uploaded_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('file_hash');
            $table->index('mime_type');
            $table->index('disk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_repository');
    }
};