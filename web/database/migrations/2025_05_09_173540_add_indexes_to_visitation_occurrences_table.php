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
        Schema::table('visitation_occurrences', function (Blueprint $table) {
            // Create a composite index to help with filtering occurrences by date ranges
            $table->index(['occurrence_date', 'status']);
            
            // Ensure all date columns have appropriate indexes
            $table->index(['occurrence_date', 'visitation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitation_occurrences', function (Blueprint $table) {
            $table->dropIndex(['occurrence_date', 'status']);
            $table->dropIndex(['occurrence_date', 'visitation_id']);
        });
    }
};