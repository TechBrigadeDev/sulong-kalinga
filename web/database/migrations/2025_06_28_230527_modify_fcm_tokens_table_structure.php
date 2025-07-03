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
        Schema::table('fcm_tokens', function (Blueprint $table) {
            // Drop old columns and foreign key constraints
            $table->dropForeign(['mobile_device_id']);
            $table->dropColumn(['mobile_device_id', 'is_active', 'last_used_at']);
            
            // Add new user_id column
            $table->unsignedBigInteger('user_id')->after('id');
            
            // Add role column to track user type for easier querying
            $table->string('role')->after('user_id')->comment('beneficiary, family_member, or cose_staff');
            
            // Add index for better performance
            $table->index(['user_id', 'role']);
            
            // Make token unique per user and role combination
            $table->unique(['user_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            // Drop new columns and constraints
            $table->dropUnique(['user_id', 'role']);
            $table->dropIndex(['user_id', 'role']);
            $table->dropColumn(['user_id', 'role']);
            
            // Restore old columns
            $table->unsignedBigInteger('mobile_device_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            
            $table->foreign('mobile_device_id')->references('id')->on('mobile_devices');
        });
    }
};
