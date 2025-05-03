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
        Schema::create('care_needs', function (Blueprint $table) {
            $table->increments('care_need_id');
            $table->integer('general_care_plan_id')->after('care_need_id');
            $table->integer('care_category_id')->after('general_care_plan_id');
            $table->string('frequency', 100)->after('care_category_id');
            $table->text('assistance_required')->after('frequency');

            // Foreign Key Constraints
            $table->foreign('general_care_plan_id')->references('general_care_plan_id')->on('general_care_plans')->onDelete('cascade');
            $table->foreign('care_category_id')->references('care_category_id')->on('care_categories')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('care_needs');
    }
};
