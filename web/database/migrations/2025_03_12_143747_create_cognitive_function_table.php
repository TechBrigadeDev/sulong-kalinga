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
        Schema::create('cognitive_function', function (Blueprint $table) {
            $table->increments('cognitive_function_id');
            $table->integer('general_care_plan_id')->after('cognitive_function_id');
            $table->text('memory')->after('general_care_plan_id')->nullable();
            $table->text('thinking_skills')->after('memory')->nullable();
            $table->text('orientation')->after('thinking_skills')->nullable();
            $table->text('behavior')->after('orientation')->nullable();

            // Foreign Key Constraints
            $table->foreign('general_care_plan_id')->references('general_care_plan_id')->on('general_care_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cognitive_function');
    }
};
