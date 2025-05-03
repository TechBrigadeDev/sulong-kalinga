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
        Schema::create('interventions', function (Blueprint $table) {
            $table->increments('intervention_id');
            $table->integer('care_category_id')->after('intervention_id');
            $table->string('intervention_description', 255)->after('care_category_id');
    
            // Foreign Key Constraints
            $table->foreign('care_category_id')->references('care_category_id')->on('care_categories')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};
