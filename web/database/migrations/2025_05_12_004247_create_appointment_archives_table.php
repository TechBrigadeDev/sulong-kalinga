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
        Schema::create('appointment_archives', function (Blueprint $table) {
            $table->id('archive_id');
            $table->unsignedBigInteger('appointment_id');
            $table->unsignedBigInteger('original_appointment_id');
            $table->string('title', 255);
            $table->unsignedBigInteger('appointment_type_id');
            $table->text('description')->nullable();
            $table->string('other_type_details', 255)->nullable();
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_flexible_time')->default(false);
            $table->string('meeting_location', 255)->nullable();
            $table->string('status', 20);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamp('archived_at')->useCurrent();
            $table->string('reason');
            $table->unsignedBigInteger('archived_by');
            
            // Index to improve query performance
            $table->index('appointment_id');
            $table->index('original_appointment_id');
            $table->index('appointment_type_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_archives');
    }
};