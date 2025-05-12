<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_patterns', function (Blueprint $table) {
            $table->id('pattern_id');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('visitation_id')->nullable();
            $table->enum('pattern_type', ['daily', 'weekly', 'monthly']);
            // Changed from integer to string to support multiple days of week
            $table->string('day_of_week', 20)->nullable(); // Comma-separated values like "1,3,5" for Mon,Wed,Fri
            $table->date('recurrence_end')->nullable();
            $table->timestamps();
            
            $table->foreign('appointment_id')
                  ->references('appointment_id')
                  ->on('appointments')
                  ->onDelete('cascade');
                  
            $table->foreign('visitation_id')
                  ->references('visitation_id')
                  ->on('visitations')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_patterns');
    }
};