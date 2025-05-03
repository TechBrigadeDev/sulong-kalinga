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
        Schema::create('weekly_care_plan_interventions', function (Blueprint $table) {
            $table->increments('wcp_intervention_id');
            $table->integer('weekly_care_plan_id')->after('wcp_intervention_id');
            $table->integer('intervention_id')->nullable()->after('weekly_care_plan_id'); // For pre-populated interventions
            $table->integer('care_category_id')->nullable()->after('intervention_id'); // For custom interventions
            $table->string('intervention_description', 255)->nullable()->after('intervention_id'); // For custom interventions
            $table->decimal('duration_minutes', 10, 2)->after('intervention_description');
            $table->boolean('implemented')->default(false);

            // Foreign Key Constraints
            $table->foreign('weekly_care_plan_id')->references('weekly_care_plan_id')->on('weekly_care_plans')->onDelete('no action');
            $table->foreign('intervention_id')->references('intervention_id')->on('interventions')->onDelete('no action');
            $table->foreign('care_category_id')->references('care_category_id')->on('care_categories')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_care_plan_interventions');
    }
};
