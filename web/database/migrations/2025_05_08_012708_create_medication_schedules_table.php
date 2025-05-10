<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_schedules', function (Blueprint $table) {
            $table->id('medication_schedule_id');
            $table->integer('beneficiary_id');
            $table->string('medication_name', 255);
            $table->string('dosage', 100);
            $table->string('medication_type', 50);
            $table->time('morning_time')->nullable();
            $table->time('noon_time')->nullable();
            $table->time('evening_time')->nullable();
            $table->time('night_time')->nullable();
            $table->boolean('as_needed')->default(false);
            $table->boolean('with_food_morning')->default(false);
            $table->boolean('with_food_noon')->default(false);
            $table->boolean('with_food_evening')->default(false);
            $table->boolean('with_food_night')->default(false);
            $table->text('special_instructions')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'completed', 'paused'])->default('active');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('beneficiary_id')
                  ->references('beneficiary_id')
                  ->on('beneficiaries')
                  ->onDelete('restrict');
                  
            $table->foreign('created_by')
                  ->references('id')
                  ->on('cose_users')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_schedules');
    }
};