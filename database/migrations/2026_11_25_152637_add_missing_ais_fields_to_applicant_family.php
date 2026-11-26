<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicant_family', function (Blueprint $table) {
            if (!Schema::hasColumn('applicant_family', 'living_arrangement')) {
                $table->text('living_arrangement')->nullable()->after('mother_name');
            }
            if (!Schema::hasColumn('applicant_family', 'siblings_description')) {
                $table->text('siblings_description')->nullable()->after('living_arrangement');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applicant_family', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('applicant_family', 'living_arrangement')) {
                $columnsToDrop[] = 'living_arrangement';
            }
            if (Schema::hasColumn('applicant_family', 'siblings_description')) {
                $columnsToDrop[] = 'siblings_description';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};