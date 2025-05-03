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
        Schema::rename('users', 'cose_users'); // Rename users table to cose_user

        Schema::table('cose_users', function (Blueprint $table) {
            // Add new columns
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('birthday');
            $table->string('civil_status', 20)->nullable();
            $table->string('educational_background', 100)->nullable();
            $table->string('mobile', 18)->unique();
            $table->string('landline', 8)->nullable();
            $table->string('personal_email', 255)->unique();
            // $table->string('email_address', 100)->unique();
            // $table->string('password_hash', 255);
            $table->text('address')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('nationality', 50)->nullable();
            $table->string('volunteer_status', 20);
            $table->date('status_start_date');
            $table->date('status_end_date')->nullable();
            $table->integer('role_id');
            $table->string('status', 20);
            $table->integer('organization_role_id')->nullable();
            $table->integer('assigned_municipality_id')->nullable();
            $table->integer('assigned_care_manager_id')->nullable()->after('assigned_municipality_id');
            $table->text('photo')->nullable();
            $table->text('government_issued_id')->nullable();
            $table->string('sss_id_number', 20)->nullable();
            $table->string('philhealth_id_number', 20)->nullable();
            $table->string('pagibig_id_number', 20)->nullable();
            $table->text('cv_resume')->nullable();
            $table->integer('updated_by')->nullable(); // Track who updated the record

            // Foreign Key Constraints
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('no action');
            $table->foreign('assigned_care_manager_id')->references('id')->on('cose_users')->onDelete('set null');

            // Keep timestamps (created_at, updated_at)
            //$table->timestamps(); // Automatically adds created_at and updated_at

            // Keep remember_token if needed for authentication
            //$table->rememberToken(); // Already exists
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cose_users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['assigned_care_manager_id']);
            $table->dropColumn([
                'first_name', 'last_name', 'birthday', 'civil_status', 'educational_background', 
                'mobile', 'landline', 'email_address', 'password_hash', 'address', 
                'gender', 'religion', 'nationality', 'volunteer_status', 'status_start_date', 
                'status_end_date', 'role_id', 'status', 'organization_role_id', 'assigned_municipality_id',
                'assigned_care_manager_id', 'photo', 'government_issued_id', 'sss_id_number', 'philhealth_id_number', 
                'pagibig_id_number', 'cv_resume', 'updated_by'
            ]);
        });

        Schema::rename('cose_users', 'users'); // Revert table name change
    }
};
