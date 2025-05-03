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
        Schema::create('messages', function (Blueprint $table) {
            $table->id('message_id');
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('sender_type', 20); // 'cose_staff', 'beneficiary', 'family_member'
            $table->text('content')->nullable(); // Allowing null when there's only an attachment
            $table->timestamp('message_timestamp')->useCurrent();
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('conversation_id');
            $table->index(['sender_id', 'sender_type']);
            
            // Foreign key to conversations
            $table->foreign('conversation_id')
                  ->references('conversation_id')
                  ->on('conversations')
                  ->onDelete('cascade');
        });
        
        // After creating the messages table, update the conversations table to reference last_message_id
        Schema::table('conversations', function (Blueprint $table) {
            $table->foreign('last_message_id')
                  ->references('message_id')
                  ->on('messages')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint in conversations table first
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['last_message_id']);
        });
        
        Schema::dropIfExists('messages');
    }
};