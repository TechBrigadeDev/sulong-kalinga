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
        Schema::create('visitation_archives', function (Blueprint $table) {
            $table->id('archive_id');
            $table->unsignedBigInteger('visitation_id');
            $table->unsignedBigInteger('original_visitation_id');
            $table->unsignedBigInteger('care_worker_id');
            $table->unsignedBigInteger('beneficiary_id');
            $table->date('visitation_date');
            $table->string('visit_type');
            $table->boolean('is_flexible_time')->default(false);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20);
            $table->date('date_assigned');
            $table->unsignedBigInteger('assigned_by');
            $table->timestamp('archived_at')->useCurrent();
            $table->string('reason');
            $table->unsignedBigInteger('archived_by');
            
            // Index to improve query performance
            $table->index('visitation_id');
            $table->index('original_visitation_id');
            $table->index('care_worker_id');
            $table->index('beneficiary_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitation_archives');
    }
};