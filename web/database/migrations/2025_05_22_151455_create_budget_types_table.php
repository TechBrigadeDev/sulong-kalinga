<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateBudgetTypesTable extends Migration
{
    public function up()
    {
        Schema::create('budget_types', function (Blueprint $table) {
            $table->id('budget_type_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default budget types relevant to NGO operations
        $types = [
            ['name' => 'Regular Allocation', 'description' => 'Standard monthly budget allocation'],
            ['name' => 'Supplemental Budget', 'description' => 'Additional budget for special needs or programs'],
            ['name' => 'Grant Funding', 'description' => 'Funds received from grant or donor organizations'],
            ['name' => 'Program-specific', 'description' => 'Budget designated for specific programs or projects'],
            ['name' => 'Emergency Fund', 'description' => 'Budget for emergency situations'],
            ['name' => 'Adjustment', 'description' => 'Budget adjustment (increase or decrease)'],
        ];

        foreach ($types as $type) {
            DB::table('budget_types')->insert([
                'name' => $type['name'],
                'description' => $type['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('budget_types');
    }
}