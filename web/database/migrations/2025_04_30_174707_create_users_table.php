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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable(); // Beneficiaries may not have email
            $table->unique('email', 'unified_users_email_unique');
            $table->string('username')->nullable()->unique(); // For beneficiary/family_member login
            $table->string('password');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mobile')->nullable();
            $table->unsignedTinyInteger('role_id');
            $table->string('status')->nullable();
            $table->string('user_type'); // 'cose', 'beneficiary', 'family_member'
            $table->unsignedBigInteger('cose_user_id')->nullable();
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->unsignedBigInteger('family_member_id')->nullable();
            $table->timestamps();

            // Indexes for fast lookup
            $table->index('username');
            $table->index('beneficiary_id');
            $table->index('family_member_id');
        });

        // Copy all cose_users into users table
        if (\Schema::hasTable('cose_users')) {
            $coseUsers = \DB::table('cose_users')->get();
            foreach ($coseUsers as $coseUser) {
                \DB::table('users')->insert([
                    'email' => $coseUser->email,
                    'username' => null,
                    'password' => $coseUser->password,
                    'first_name' => $coseUser->first_name,
                    'last_name' => $coseUser->last_name,
                    'mobile' => $coseUser->mobile,
                    'role_id' => $coseUser->role_id,
                    'status' => $coseUser->status,
                    'user_type' => 'cose',
                    'cose_user_id' => $coseUser->id,
                    'beneficiary_id' => null,
                    'family_member_id' => null,
                    'created_at' => $coseUser->created_at,
                    'updated_at' => $coseUser->updated_at,
                ]);
            }
        }

        // Copy all beneficiaries into users table
        if (\Schema::hasTable('beneficiaries')) {
            $beneficiaries = \DB::table('beneficiaries')->get();
            foreach ($beneficiaries as $b) {
                \DB::table('users')->insert([
                    'email' => null,
                    'username' => $b->username,
                    'password' => $b->password,
                    'first_name' => $b->first_name,
                    'last_name' => $b->last_name,
                    'mobile' => $b->mobile,
                    'role_id' => 5, // adjust as needed for beneficiary role
                    'status' => $b->beneficiary_status_id,
                    'user_type' => 'beneficiary',
                    'cose_user_id' => null,
                    'beneficiary_id' => $b->beneficiary_id,
                    'family_member_id' => null,
                    'created_at' => $b->created_at,
                    'updated_at' => $b->updated_at,
                ]);
            }
        }

        // Copy all family_members into users table
        if (\Schema::hasTable('family_members')) {
            $familyMembers = \DB::table('family_members')->get();
            foreach ($familyMembers as $fm) {
                \DB::table('users')->insert([
                    'email' => $fm->email,
                    'username' => $fm->username ?? null,
                    'password' => $fm->password,
                    'first_name' => $fm->first_name,
                    'last_name' => $fm->last_name,
                    'mobile' => $fm->mobile,
                    'role_id' => 6, // adjust as needed for family member role
                    'status' => null,
                    'user_type' => 'family_member',
                    'cose_user_id' => null,
                    'beneficiary_id' => null,
                    'family_member_id' => $fm->family_member_id,
                    'created_at' => $fm->created_at,
                    'updated_at' => $fm->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
