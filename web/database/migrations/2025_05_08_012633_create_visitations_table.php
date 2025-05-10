<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitations', function (Blueprint $table) {
            $table->id('visitation_id');
            $table->integer('care_worker_id');
            $table->integer('beneficiary_id');
            $table->enum('visit_type', ['routine_care_visit', 'service_request', 'emergency_visit']);
            $table->date('visitation_date');
            $table->boolean('is_flexible_time')->default(false);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('notes')->nullable();
            $table->date('date_assigned');
            $table->integer('assigned_by');
            $table->string('status', 20)->default('scheduled');
            $table->integer('confirmed_by_beneficiary')->nullable();
            $table->integer('confirmed_by_family')->nullable();
            $table->timestamp('confirmed_on')->nullable();
            $table->integer('work_shift_id')->nullable();
            $table->integer('visit_log_id')->nullable();
            $table->timestamps();
            
            $table->foreign('care_worker_id')
                  ->references('id')
                  ->on('cose_users')
                  ->onDelete('restrict');
                  
            $table->foreign('beneficiary_id')
                  ->references('beneficiary_id')
                  ->on('beneficiaries')
                  ->onDelete('restrict');
                  
            $table->foreign('assigned_by')
                  ->references('id')
                  ->on('cose_users')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitations');
    }
};