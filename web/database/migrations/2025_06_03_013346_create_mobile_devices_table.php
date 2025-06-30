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
        Schema::create('mobile_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type')->nullable(); // e.g. 'beneficiary', 'family_member', 'cose_staff'
            $table->string('device_uuid')->unique(); // Unique identifier from the device
            $table->string('device_type');           // e.g. 'android', 'ios'
            $table->string('device_model')->nullable();
            $table->string('os_version')->nullable();
            $table->timestamps();

            // Indexes for fast lookup
            $table->index(['user_id', 'user_type']);
            $table->index('device_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_devices');
    }
};
