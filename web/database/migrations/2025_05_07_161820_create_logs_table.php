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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Who did the action
            $table->string('entity_type');         // E.g. 'beneficiary', 'municipality'
            $table->unsignedBigInteger('entity_id')->nullable(); // ID of the affected entity
            $table->string('type');                // Enum: create, update, archive, delete
            $table->text('message');               // Description of the change
            $table->timestamps();                  // created_at = when the action happened
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
