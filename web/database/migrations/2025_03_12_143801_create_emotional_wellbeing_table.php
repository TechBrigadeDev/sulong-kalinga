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
        Schema::create('emotional_wellbeing', function (Blueprint $table) {
            $table->increments('emotional_wellbeing_id');
            $table->integer('general_care_plan_id')->after('emotional_wellbeing_id');
            $table->text('mood')->after('general_care_plan_id')->nullable();
            $table->text('social_interactions')->after('mood')->nullable();
            $table->text('emotional_support_needs')->after('social_interactions')->nullable();

            // Foreign Key Constraints
            $table->foreign('general_care_plan_id')->references('general_care_plan_id')->on('general_care_plans')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emotional_wellbeing');
    }
};
