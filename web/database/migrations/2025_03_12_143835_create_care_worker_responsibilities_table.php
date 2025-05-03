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
        Schema::create('care_worker_responsibilities', function (Blueprint $table) {
            $table->increments('cw_responsibility_id');
            $table->integer('general_care_plan_id')->after('cw_responsibility_id');
            $table->integer('care_worker_id')->after('general_care_plan_id');
            $table->text('task_description')->after('care_worker_id');

            // Foreign Key Constraints
            $table->foreign('general_care_plan_id')->references('general_care_plan_id')->on('general_care_plans')->onDelete('no action');
            $table->foreign('care_worker_id')->references('id')->on('cose_users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('care_worker_responsibilities');
    }
};
