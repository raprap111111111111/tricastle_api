<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applicant_guarantors', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('full_name');
            // keep age nullable for backward compatibility (optional)
            // $table->unsignedTinyInteger('age')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('applicant_guarantors', function (Blueprint $table) {
            $table->dropColumn('date_of_birth');
        });
    }
};