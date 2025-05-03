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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->increments('beneficiary_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('civil_status', 50);
            $table->string('gender', 50);
            $table->date('birthday');
            $table->string('primary_caregiver', 255)->nullable();
            $table->string('mobile', 18)->nullable();
            $table->string('landline', 8)->nullable();
            $table->text('street_address');
            $table->integer('barangay_id');
            $table->integer('municipality_id');
            $table->integer('category_id');
            $table->string('emergency_contact_name', 255);
            $table->string('emergency_contact_relation', 50)->nullable();
            $table->string('emergency_contact_mobile', 18);
            $table->string('emergency_contact_email', 100)->nullable();
            $table->text('emergency_procedure');
            $table->integer('beneficiary_status_id');
            $table->string('status_reason', 255)->nullable();
            $table->integer('general_care_plan_id')->nullable();
            $table->integer('portal_account_id');
            $table->text('photo')->nullable();
            $table->text('beneficiary_signature')->nullable();
            $table->text('care_worker_signature')->nullable();
            $table->text('general_care_plan_doc')->nullable();
            $table->text('care_service_agreement_doc')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
            $table->rememberToken();

            // Foreign Key Constraints
            $table->foreign('municipality_id')->references('municipality_id')->on('municipalities')->onDelete('no action');
            $table->foreign('category_id')->references('category_id')->on('beneficiary_categories')->onDelete('no action');
            $table->foreign('beneficiary_status_id')->references('beneficiary_status_id')->on('beneficiary_status')->onDelete('no action');
            $table->foreign('portal_account_id')->references('id')->on('portal_accounts')->onDelete('no action');
            $table->foreign('created_by')->references('id')->on('cose_users')->onDelete('no action');
            $table->foreign('updated_by')->references('id')->on('cose_users')->onDelete('no action');
            $table->foreign('barangay_id')->references('barangay_id')->on('barangays')->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
