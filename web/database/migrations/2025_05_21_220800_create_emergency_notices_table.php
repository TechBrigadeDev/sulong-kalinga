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
        Schema::create('emergency_notices', function (Blueprint $table) {
            $table->id('notice_id');
            $table->unsignedBigInteger('sender_id');
            $table->enum('sender_type', ['beneficiary', 'family_member']);
            $table->unsignedBigInteger('beneficiary_id');
            $table->foreignId('emergency_type_id')->constrained('emergency_types', 'emergency_type_id');
            $table->text('message');
            $table->enum('status', ['new', 'in_progress', 'resolved', 'archived'])->default('new');
            $table->boolean('read_status')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->enum('action_type', ['responded', 'in_progress', 'resolved', null])->nullable();
            $table->unsignedBigInteger('action_taken_by')->nullable();
            $table->timestamp('action_taken_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('beneficiary_id')->references('beneficiary_id')->on('beneficiaries');
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->foreign('action_taken_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_notices');
    }
};