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
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->increments('vital_signs_id');
            $table->string('blood_pressure'); // Store as string to handle format like "120/80"
            $table->decimal('body_temperature', 4, 1); // Allow for decimal temp like 36.5°C
            $table->integer('pulse_rate'); // Store as integer
            $table->integer('respiratory_rate'); // Store as integer
            $table->integer('created_by');
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('created_by')->references('id')->on('cose_users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};
