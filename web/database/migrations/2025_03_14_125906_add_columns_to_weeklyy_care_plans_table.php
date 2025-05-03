<?php

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
            $table->text('assessment_summary_draft')->nullable()->after('assessment');
            $table->text('assessment_translation_draft')->nullable()->after('assessment_summary_draft');
            $table->text('evaluation_summary_draft')->nullable()->after('evaluation');
            $table->text('evaluation_translation_draft')->nullable()->after('evaluation_summary_draft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_care_plans', function (Blueprint $table) {
            $table->dropColumn([
                'assessment_summary_draft',
                'assessment_translation_draft',
                'evaluation_summary_draft',
                'evaluation_translation_draft'
            ]);
        });
    }
};
