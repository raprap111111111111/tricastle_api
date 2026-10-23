<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('passport_issuing_offices', function (Blueprint $table) {
            if (!Schema::hasColumn('passport_issuing_offices', 'code')) {
                $table->string('code')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('passport_issuing_offices', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('passport_issuing_offices', 'province')) {
                $table->string('province')->nullable()->after('city');
            }
            if (!Schema::hasColumn('passport_issuing_offices', 'sort_order')) {
                $table->unsignedSmallInteger('sort_order')->default(0)->after('is_active');
            }
            if (!Schema::hasColumn('passport_issuing_offices', 'notes')) {
                $table->text('notes')->nullable()->after('sort_order');
            }
        });
    }

    public function down(): void
    {
        Schema::table('passport_issuing_offices', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('passport_issuing_offices', 'code')) {
                $table->dropUnique(['code']);
                $columnsToDrop[] = 'code';
            }
            if (Schema::hasColumn('passport_issuing_offices', 'city')) {
                $columnsToDrop[] = 'city';
            }
            if (Schema::hasColumn('passport_issuing_offices', 'province')) {
                $columnsToDrop[] = 'province';
            }
            if (Schema::hasColumn('passport_issuing_offices', 'sort_order')) {
                $columnsToDrop[] = 'sort_order';
            }
            if (Schema::hasColumn('passport_issuing_offices', 'notes')) {
                $columnsToDrop[] = 'notes';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};