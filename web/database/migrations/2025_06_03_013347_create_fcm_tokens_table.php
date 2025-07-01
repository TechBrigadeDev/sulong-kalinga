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
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('role')->comment('beneficiary, family_member, or cose_staff');
            $table->foreignId('mobile_device_id')->constrained('mobile_devices')->onDelete('cascade');
            $table->string('token')->unique();
            $table->timestamps();

            // Enforce one token per user/role/device
            $table->unique(['user_id', 'role', 'mobile_device_id'], 'fcm_tokens_user_id_role_mobile_device_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
