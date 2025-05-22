<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('name');
            $table->string('color_code', 20)->default('#6c757d'); // Default color
            $table->string('icon', 50)->nullable();
            $table->timestamps();
        });

        // Insert default expense categories
        $categories = [
            ['name' => 'Medical Supplies', 'color_code' => '#0d6efd', 'icon' => 'bi-capsule-pill'],
            ['name' => 'Medications', 'color_code' => '#6610f2', 'icon' => 'bi-capsule'],
            ['name' => 'Food & Nutrition', 'color_code' => '#198754', 'icon' => 'bi-basket'],
            ['name' => 'Transportation/Fuel', 'color_code' => '#0dcaf0', 'icon' => 'bi-truck'],
            ['name' => 'Facility Maintenance', 'color_code' => '#ffc107', 'icon' => 'bi-house-gear'],
            ['name' => 'Staff Training', 'color_code' => '#dc3545', 'icon' => 'bi-people'],
            ['name' => 'Administrative', 'color_code' => '#6c757d', 'icon' => 'bi-briefcase'],
            ['name' => 'Program Activities', 'color_code' => '#0d6efd', 'icon' => 'bi-calendar-event'],
            ['name' => 'Community Outreach', 'color_code' => '#198754', 'icon' => 'bi-people-fill'],
            ['name' => 'Emergency Response', 'color_code' => '#dc3545', 'icon' => 'bi-exclamation-triangle'],
            ['name' => 'Other', 'color_code' => '#6c757d', 'icon' => 'bi-question-circle']
        ];

        foreach ($categories as $category) {
            DB::table('expense_categories')->insert([
                'name' => $category['name'],
                'color_code' => $category['color_code'],
                'icon' => $category['icon'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('expense_categories');
    }
}