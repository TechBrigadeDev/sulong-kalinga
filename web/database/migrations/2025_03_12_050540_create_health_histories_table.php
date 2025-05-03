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
        Schema::create('health_histories', function (Blueprint $table) {
            $table->increments('health_history_id');
            $table->integer('general_care_plan_id')->after('health_history_id');
            $table->text('medical_conditions')->nullable();
            $table->text('medications')->nullable();
            $table->text('allergies')->nullable();
            $table->text('immunizations')->nullable();
            
            // Foreign Key Constraints
            $table->foreign('general_care_plan_id')->references('general_care_plan_id')->on('general_care_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_histories');
    }
};
