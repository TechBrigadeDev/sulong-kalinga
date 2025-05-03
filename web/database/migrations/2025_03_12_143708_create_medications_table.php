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
        Schema::create('medications', function (Blueprint $table) {
            $table->increments('medications_id');
            $table->integer('general_care_plan_id')->after('medications_id');
            $table->string('medication', 100)->after('general_care_plan_id');
            $table->string('dosage', 100)->after('medication');
            $table->string('frequency', 100)->after('dosage');
            $table->text('administration_instructions')->after('frequency');

            // Foreign Key Constraints
            $table->foreign('general_care_plan_id')->references('general_care_plan_id')->on('general_care_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
