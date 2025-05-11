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
        Schema::create('appointment_occurrences', function (Blueprint $table) {
            $table->id('occurrence_id');
            $table->unsignedBigInteger('appointment_id');
            $table->date('occurrence_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('status', 20)->default('scheduled');
            $table->boolean('is_modified')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for faster querying
            $table->index('appointment_id');
            $table->index('occurrence_date');
            $table->index(['appointment_id', 'occurrence_date']);
            $table->index('status');
            
            $table->foreign('appointment_id')
                  ->references('appointment_id')
                  ->on('appointments')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_occurrences');
    }
};