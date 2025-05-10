<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\database\migrations\2023_05_06_add_illnesses_to_weekly_care_plans.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('weekly_care_plans', function (Blueprint $table) {
            $table->text('illnesses')->nullable()->after('assessment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_care_plans', function (Blueprint $table) {
            $table->dropColumn('illnesses');
        });
    }
};