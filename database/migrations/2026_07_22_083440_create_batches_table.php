<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('batch_number')->unique();
            $table->string('name', 255);
            $table->string('country', 100)->nullable();
            $table->date('deployment_date')->nullable();

            $table->enum('status', [
                'draft',
                'ongoing',
                'deployed',
                'completed',
                'cancelled',
            ])->default('draft');

            // ─── Active flag (only ONE batch can be active at a time) ───
            $table->boolean('is_active')->default(false)->index();

            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};