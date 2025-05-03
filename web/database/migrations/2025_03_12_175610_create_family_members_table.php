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
        Schema::create('family_members', function (Blueprint $table) {
            $table->increments('family_member_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('birthday');
            $table->string('mobile', 18)->unique()->nullable();
            $table->string('landline', 8)->nullable();
            $table->string('email', 100)->unique();
            $table->text('photo')->nullable();
            $table->text('street_address');
            $table->string('gender', 50)->nullable();
            $table->integer('related_beneficiary_id');
            $table->string('relation_to_beneficiary', 50);
            $table->boolean('is_primary_caregiver')->default(0);
            $table->integer('portal_account_id');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->rememberToken();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('related_beneficiary_id')->references('beneficiary_id')->on('beneficiaries')->onDelete('no action');
            $table->foreign('portal_account_id')->references('id')->on('portal_accounts')->onDelete('no action');
            $table->foreign('created_by')->references('id')->on('cose_users')->onDelete('no action');
            $table->foreign('updated_by')->references('id')->on('cose_users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
