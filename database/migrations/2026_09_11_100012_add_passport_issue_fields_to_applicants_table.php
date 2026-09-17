<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('applicants', function (Blueprint $table) {
            $table->date('passport_issue_date')->nullable()->after('passport_expiry');
            $table->string('passport_issue_place')->nullable()->after('passport_issue_date');
        });
    }

    public function down(): void {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn(['passport_issue_date', 'passport_issue_place']);
        });
    }
};