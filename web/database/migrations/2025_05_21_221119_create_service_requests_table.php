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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id('service_request_id');
            $table->unsignedBigInteger('sender_id');
            $table->enum('sender_type', ['beneficiary', 'family_member']);
            $table->unsignedBigInteger('beneficiary_id');
            $table->foreignId('service_type_id')->constrained('service_request_types', 'service_type_id');
            $table->unsignedBigInteger('care_worker_id')->nullable();
            $table->date('service_date')->nullable();
            $table->time('service_time')->nullable();
            $table->text('message');
            $table->enum('status', ['new', 'approved', 'rejected', 'completed'])->default('new');
            $table->boolean('read_status')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->enum('action_type', ['approved', 'rejected', 'completed', null])->nullable();
            $table->unsignedBigInteger('action_taken_by')->nullable();
            $table->timestamp('action_taken_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('beneficiary_id')->references('beneficiary_id')->on('beneficiaries');
            $table->foreign('care_worker_id')->references('id')->on('users');
            $table->foreign('action_taken_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};