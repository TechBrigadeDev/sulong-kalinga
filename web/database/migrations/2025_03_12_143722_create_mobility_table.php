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
        Schema::create('mobility', function (Blueprint $table) {
            $table->increments('mobility_id');
            $table->integer('general_care_plan_id')->after('mobility_id');
            $table->text('walking_ability')->after('general_care_plan_id')->nullable();
            $table->text('assistive_devices')->after('walking_ability')->nullable();
            $table->text('transportation_needs')->after('assistive_devices')->nullable();

            // Foreign Key Constraints
            $table->foreign('general_care_plan_id')->references('general_care_plan_id')->on('general_care_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobility');
    }
};
