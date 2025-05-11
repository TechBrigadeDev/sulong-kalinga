<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('appointment_exceptions', function (Blueprint $table) {
            $table->id('exception_id');
            $table->unsignedBigInteger('appointment_id');
            $table->date('exception_date');
            $table->string('status'); // 'canceled' or other possible statuses
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('appointment_id')
                  ->references('appointment_id')
                  ->on('appointments')
                  ->onDelete('cascade');
            $table->foreign('created_by')
                  ->references('id')
                  ->on('cose_users');
            
            // Ensure each date can only have one exception per appointment
            $table->unique(['appointment_id', 'exception_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_exceptions');
    }
};