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
            // Assessment sections
            $table->json('assessment_summary_sections')->nullable()->after('assessment_summary_draft');
            // Evaluation sections
            $table->json('evaluation_summary_sections')->nullable()->after('evaluation_summary_draft');
            // Final versions after editing
            $table->text('assessment_summary_final')->nullable()->after('assessment_summary_sections');
            $table->text('evaluation_summary_final')->nullable()->after('evaluation_summary_sections');
            // Status flags
            $table->boolean('has_ai_summary')->default(false)->after('evaluation_summary_final');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_care_plans', function (Blueprint $table) {
            $table->dropColumn([
                'assessment_summary_sections',
                'evaluation_summary_sections',
                'assessment_summary_final',
                'evaluation_summary_final',
                'has_ai_summary'
            ]);
        });
    }
};