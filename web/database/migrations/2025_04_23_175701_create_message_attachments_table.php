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
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id('attachment_id');
            $table->unsignedBigInteger('message_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // MIME type
            $table->integer('file_size'); // Size in bytes
            $table->boolean('is_image')->default(false); // Flag for images for easier frontend handling
            $table->timestamps();
            
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
        Schema::dropIfExists('message_attachments');
    }
};