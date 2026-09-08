<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('name_on_document')->nullable()->after('name');
            $table->string('registration_no')->nullable()->after('name_on_document');

            // Company Signatory (President, Managing Director, etc.)
            $table->string('signatory_name')->nullable()->after('contact_phone');
            $table->string('signatory_title')->nullable()->after('signatory_name');
            $table->string('signatory_passport')->nullable()->after('signatory_title');
            $table->string('signatory_id_type')->nullable()->after('signatory_passport');
            $table->string('signatory_id_no')->nullable()->after('signatory_id_type');
            $table->string('signatory_id_issued')->nullable()->after('signatory_id_no');

            // Work site / Internship location
            $table->text('site_address')->nullable()->after('address');
            $table->string('industry')->nullable()->after('site_address');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'name_on_document',
                'registration_no',
                'signatory_name',
                'signatory_title',
                'signatory_passport',
                'signatory_id_type',
                'signatory_id_no',
                'signatory_id_issued',
                'site_address',
                'industry',
            ]);
        });
    }
};