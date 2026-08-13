<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {

            // ─── Skill Classification ──────────────────────────────────────
            $table->enum('skill_category', [
                'skilled',
                'semi_skilled',
                'unskilled',
            ])->nullable()->after('nationality');

            $table->string('trade_or_occupation')->nullable()->after('skill_category');

            // ─── Language ──────────────────────────────────────────────────
            $table->boolean('understands_basic_english')
                  ->default(false)
                  ->after('trade_or_occupation');

            $table->enum('jlpt_level', ['N5', 'N4', 'N3', 'N2', 'N1'])
                  ->nullable()
                  ->after('understands_basic_english');

            // ─── Japan Deployment Readiness ────────────────────────────────
            $table->boolean('willing_to_be_deployed')
                  ->default(false)
                  ->after('jlpt_level');

            $table->boolean('japan_deployment_ready')
                  ->default(false)
                  ->after('willing_to_be_deployed');

            $table->string('preferred_work_location')
                  ->nullable()
                  ->after('japan_deployment_ready');

            // ─── Prior Japan Experience ────────────────────────────────────
            $table->boolean('previous_japan_experience')
                  ->default(false)
                  ->after('preferred_work_location');

            $table->unsignedTinyInteger('years_japan_experience')
                  ->default(0)
                  ->after('previous_japan_experience');

            // ─── TITP / SSW Certification ──────────────────────────────────
            $table->boolean('has_titp_certificate')
                  ->default(false)
                  ->after('years_japan_experience');

            $table->string('titp_occupation')
                  ->nullable()
                  ->after('has_titp_certificate');

            $table->boolean('ssw_eligible')
                  ->default(false)
                  ->after('titp_occupation');

            // ─── Salary ────────────────────────────────────────────────────
            $table->decimal('expected_salary', 12, 2)
                  ->nullable()
                  ->after('ssw_eligible');

            $table->char('expected_salary_currency', 3)
                  ->default('JPY')
                  ->after('expected_salary');

            $table->decimal('current_salary', 12, 2)
                  ->nullable()
                  ->after('expected_salary_currency');

            $table->char('current_salary_currency', 3)
                  ->default('PHP')
                  ->after('current_salary');

            // ─── Family ────────────────────────────────────────────────────
            $table->string('father_name')->nullable()->after('current_salary_currency');
            $table->string('father_occupation')->nullable()->after('father_name');
            $table->string('father_contact')->nullable()->after('father_occupation');

            $table->string('mother_name')->nullable()->after('father_contact');
            $table->string('mother_occupation')->nullable()->after('mother_name');
            $table->string('mother_contact')->nullable()->after('mother_occupation');

            $table->string('spouse_name')->nullable()->after('mother_contact');
            $table->string('spouse_occupation')->nullable()->after('spouse_name');
            $table->string('spouse_contact')->nullable()->after('spouse_occupation');

            // ─── Emergency Contact ─────────────────────────────────────────
            $table->string('emergency_contact_name')->nullable()->after('spouse_contact');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_relationship');
            $table->string('emergency_contact_address')->nullable()->after('emergency_contact_phone');

            // ─── Indexes ───────────────────────────────────────────────────
            $table->index('skill_category',            'applicants_skill_category_index');
            $table->index('understands_basic_english',  'applicants_english_index');
            $table->index('willing_to_be_deployed',     'applicants_willing_deploy_index');
            $table->index('japan_deployment_ready',     'applicants_japan_ready_index');
            $table->index('previous_japan_experience',  'applicants_japan_exp_index');
            $table->index('jlpt_level',                 'applicants_jlpt_index');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {

            // ─── Drop Indexes first ────────────────────────────────────────
            $table->dropIndex('applicants_skill_category_index');
            $table->dropIndex('applicants_english_index');
            $table->dropIndex('applicants_willing_deploy_index');
            $table->dropIndex('applicants_japan_ready_index');
            $table->dropIndex('applicants_japan_exp_index');
            $table->dropIndex('applicants_jlpt_index');

            // ─── Drop Columns ──────────────────────────────────────────────
            $table->dropColumn([
                // Skill
                'skill_category',
                'trade_or_occupation',

                // Language
                'understands_basic_english',
                'jlpt_level',

                // Deployment
                'willing_to_be_deployed',
                'japan_deployment_ready',
                'preferred_work_location',

                // Experience
                'previous_japan_experience',
                'years_japan_experience',

                // Certifications
                'has_titp_certificate',
                'titp_occupation',
                'ssw_eligible',

                // Salary
                'expected_salary',
                'expected_salary_currency',
                'current_salary',
                'current_salary_currency',

                // Family
                'father_name',
                'father_occupation',
                'father_contact',
                'mother_name',
                'mother_occupation',
                'mother_contact',
                'spouse_name',
                'spouse_occupation',
                'spouse_contact',

                // Emergency
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_phone',
                'emergency_contact_address',
            ]);
        });
    }
};