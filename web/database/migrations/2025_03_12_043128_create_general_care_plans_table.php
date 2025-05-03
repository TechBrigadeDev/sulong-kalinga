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
        Schema::create('general_care_plans', function (Blueprint $table) {
            $table->increments('general_care_plan_id');
            $table->integer('care_worker_id')->after('general_care_plan');
            $table->text('emergency_plan')->after('care_worker_id');
            $table->date('review_date')->after('emergency_plan');
            $table->timestamps();

            // Foreign Key Constraints
        $table->foreign('care_worker_id')->references('id')->on('cose_users')->onDelete('no action');
        });

        
        
        // Separate Migration after creation of Beneficiary Table:
        // $table->foreign('beneficiary_id')->references('beneficiaries')->on('beneficiary_id')->onDelete('no action');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_care_plans');
    }
};