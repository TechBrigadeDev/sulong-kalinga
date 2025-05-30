<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('weekly_care_plans', function (Blueprint $table) {
            $table->json('assessment_translation_sections')->nullable();
            $table->json('evaluation_translation_sections')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('weekly_care_plans', function (Blueprint $table) {
            $table->dropColumn([
                'assessment_translation_sections',
                'evaluation_translation_sections'
            ]);
        });
    }
};
