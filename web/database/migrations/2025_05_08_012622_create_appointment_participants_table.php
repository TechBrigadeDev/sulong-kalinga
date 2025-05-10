<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_participants', function (Blueprint $table) {
            $table->id('appointment_participant_id');
            $table->unsignedBigInteger('appointment_id');
            $table->enum('participant_type', ['cose_user', 'beneficiary', 'family_member']);
            $table->integer('participant_id');
            $table->boolean('is_organizer')->default(false);
            $table->timestamps();
            
            $table->foreign('appointment_id')
                  ->references('appointment_id')
                  ->on('appointments')
                  ->onDelete('cascade');
                  
            $table->index(['participant_type', 'participant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_participants');
    }
};