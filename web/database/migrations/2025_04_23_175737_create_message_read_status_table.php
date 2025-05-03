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
        Schema::create('message_read_status', function (Blueprint $table) {
            $table->id('read_status_id');
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('reader_id');
            $table->string('reader_type', 20); // 'cose_staff', 'beneficiary', 'family_member'
            $table->timestamp('read_at')->useCurrent();
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('message_id');
            $table->index(['reader_id', 'reader_type']);
            
            // Make sure we only have one record per message per reader
            $table->unique(['message_id', 'reader_id', 'reader_type'], 'unique_message_reader');
            
            // Foreign key to messages
            $table->foreign('message_id')
                  ->references('message_id')
                  ->on('messages')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_read_status');
    }
};