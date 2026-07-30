<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('provider');           // facebook, google, etc.
            $table->string('provider_id');        // ID from provider
            $table->string('provider_token')->nullable();
            $table->string('provider_refresh_token')->nullable();
            $table->string('avatar')->nullable();
            $table->json('raw_data')->nullable(); // Full provider response
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_id']);
            $table->index('user_id');
            $table->index('provider');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};