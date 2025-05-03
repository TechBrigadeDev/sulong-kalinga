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
        Schema::create('weekly_care_plans', function (Blueprint $table) {
            $table->increments('weekly_care_plan_id');
            $table->integer('beneficiary_id')->after('weekly_care_plan_id');
            $table->integer('care_worker_id')->after('beneficiary_id');
            $table->integer('vital_signs_id')->after('care_manager_id');
            $table->date('date');
            $table->text('assessment');
            $table->text('evaluation_recommendations');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('acknowledged_by_beneficiary')->nullable(); // beneficiary registered
            $table->integer('acknowledged_by_family')->nullable(); // family member registered
            $table->text('acknowledgement_signature')->nullable(); // for beneficiary or witness without system access

            $table->timestamps();

             // Foreign Key Constraints
             $table->foreign('beneficiary_id')->references('beneficiary_id')->on('beneficiaries')->onDelete('no action');
             $table->foreign('care_worker_id')->references('id')->on('cose_users')->onDelete('no action');
             $table->foreign('vital_signs_id')->references('vital_signs_id')->on('vital_signs')->onDelete('no action');
             $table->foreign('acknowledged_by_beneficiary')->references('beneficiary_id')->on('beneficiaries')->onDelete('no action');
             $table->foreign('acknowledged_by_family')->references('family_member_id')->on('family_members')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_care_plans');
    }
};
