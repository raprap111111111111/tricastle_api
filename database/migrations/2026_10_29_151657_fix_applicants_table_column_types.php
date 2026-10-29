<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            // Change ENUMs to VARCHAR so partial/custom values never truncate
            $table->string('blood_type', 10)->nullable()->change();
            $table->string('dominant_hand', 20)->nullable()->change();
            $table->string('jlpt_level', 10)->nullable()->change();
            $table->string('facebook_url', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        // No down needed or keep as string
    }
};