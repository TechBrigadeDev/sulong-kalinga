<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('appointment_id');
            $table->unsignedBigInteger('appointment_type_id');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('other_type_details', 255)->nullable();
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_flexible_time')->default(false);
            $table->string('meeting_location', 255)->nullable();
            $table->string('status', 20)->default('scheduled');
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('appointment_type_id')
                  ->references('appointment_type_id')
                  ->on('appointment_types')
                  ->onDelete('restrict');
                  
            $table->foreign('created_by')
                  ->references('id')
                  ->on('cose_users')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};