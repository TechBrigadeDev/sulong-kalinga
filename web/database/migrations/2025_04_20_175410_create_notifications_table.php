<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\database\migrations\2023_04_20_create_notifications_table.php

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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->unsignedInteger('user_id');
            $table->string('user_type', 20); // beneficiary, family_member, or core_staff
            $table->string('message_title', 255)->nullable();
            $table->text('message');
            $table->timestamp('date_created');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            // Add index for faster queries
            $table->index(['user_id', 'user_type']);
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};