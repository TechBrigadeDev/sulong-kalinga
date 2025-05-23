<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetAllocationsTable extends Migration
{
    public function up()
    {
        Schema::create('budget_allocations', function (Blueprint $table) {
            $table->id('budget_allocation_id');
            $table->decimal('amount', 14, 2); // Large decimal for potentially large budget amounts
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('budget_type_id')->constrained('budget_types', 'budget_type_id');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users', 'id');
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('budget_allocations');
    }
}