<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            if (!Schema::hasColumn('applicants', 'applied_position')) {
                $table->string('applied_position')->nullable()->after('applicant_code');
            }
            if (!Schema::hasColumn('applicants', 'trade_test_try')) {
                $table->string('trade_test_try')->nullable()->after('applied_position');
            }
            if (!Schema::hasColumn('applicants', 'trade_test_date')) {
                $table->date('trade_test_date')->nullable()->after('trade_test_try');
            }
            if (!Schema::hasColumn('applicants', 'birthplace')) {
                $table->string('birthplace')->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('applicants', 'religion')) {
                $table->string('religion')->nullable()->after('civil_status');
            }
            if (!Schema::hasColumn('applicants', 'english_proficiency_pct')) {
                $table->unsignedTinyInteger('english_proficiency_pct')->default(0)->after('religion');
            }
        });

        if (Schema::hasTable('applicant_educations')) {
            Schema::table('applicant_educations', function (Blueprint $table) {
                if (!Schema::hasColumn('applicant_educations', 'remarks')) {
                    $table->text('remarks')->nullable()->after('honors');
                }
            });
        }

        if (Schema::hasTable('applicant_employments')) {
            Schema::table('applicant_employments', function (Blueprint $table) {
                if (!Schema::hasColumn('applicant_employments', 'is_overseas')) {
                    $table->boolean('is_overseas')->default(false)->after('country');
                }
                if (!Schema::hasColumn('applicant_employments', 'salary_unit')) {
                    $table->enum('salary_unit', ['per_day', 'per_month', 'per_year'])->default('per_month')->after('salary_currency');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $columns = array_filter([
                'applied_position',
                'trade_test_try',
                'trade_test_date',
                'birthplace',
                'religion',
                'english_proficiency_pct',
            ], fn($col) => Schema::hasColumn('applicants', $col));

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });

        if (Schema::hasTable('applicant_educations')) {
            Schema::table('applicant_educations', function (Blueprint $table) {
                if (Schema::hasColumn('applicant_educations', 'remarks')) {
                    $table->dropColumn('remarks');
                }
            });
        }

        if (Schema::hasTable('applicant_employments')) {
            Schema::table('applicant_employments', function (Blueprint $table) {
                $cols = array_filter(['is_overseas', 'salary_unit'], fn($c) => Schema::hasColumn('applicant_employments', $c));
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
