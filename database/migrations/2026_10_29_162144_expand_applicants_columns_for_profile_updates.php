<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('facebook_url', 255)->nullable()->change();
            $table->string('mobile', 30)->nullable()->change();
            $table->string('emergency_contact_phone', 30)->nullable()->change();
            $table->string('current_address', 255)->nullable()->change();
            $table->string('permanent_address', 255)->nullable()->change();
            $table->string('emergency_contact_address', 255)->nullable()->change();
            $table->string('blood_type', 10)->nullable()->change();
            $table->string('dominant_hand', 20)->nullable()->change();
            $table->string('jlpt_level', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        // No down migration required
    }
};