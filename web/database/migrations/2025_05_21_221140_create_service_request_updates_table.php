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
        Schema::create('service_request_updates', function (Blueprint $table) {
            $table->id('update_id');
            $table->foreignId('service_request_id')->constrained('service_requests', 'service_request_id')->onDelete('cascade');
            $table->text('message');
            $table->enum('update_type', ['approval', 'rejection', 'assignment', 'completion', 'note'])->default('note');
            $table->string('status_change_to')->nullable();
            $table->unsignedBigInteger('updated_by');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request_updates');
    }
};