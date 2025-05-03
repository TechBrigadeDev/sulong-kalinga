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
        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id('conversation_participant_id');
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('participant_id');
            $table->string('participant_type', 20); // 'cose_staff', 'beneficiary', 'family_member'
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable(); // In case participants leave conversations
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index(['conversation_id']);
            $table->index(['participant_id', 'participant_type']);
            
            // Foreign key to conversations
            $table->foreign('conversation_id')
                  ->references('conversation_id')
                  ->on('conversations')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_participants');
    }
};