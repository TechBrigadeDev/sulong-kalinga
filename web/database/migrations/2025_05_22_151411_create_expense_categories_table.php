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
            ['name' => 'Medical Supplies', 'color_code' => '#0d6efd', 'icon' => 'bi-capsule-pill'],        // Blue
            ['name' => 'Medications', 'color_code' => '#6f42c1', 'icon' => 'bi-capsule'],                  // Purple
            ['name' => 'Food & Nutrition', 'color_code' => '#198754', 'icon' => 'bi-basket'],              // Green
            ['name' => 'Transportation/Fuel', 'color_code' => '#17a2b8', 'icon' => 'bi-truck'],            // Teal
            ['name' => 'Facility Maintenance', 'color_code' => '#ffc107', 'icon' => 'bi-house-gear'],      // Yellow
            ['name' => 'Staff Training', 'color_code' => '#dc3545', 'icon' => 'bi-people'],                // Red
            ['name' => 'Administrative', 'color_code' => '#fd7e14', 'icon' => 'bi-briefcase'],             // Orange
            ['name' => 'Program Activities', 'color_code' => '#20c997', 'icon' => 'bi-calendar-event'],    // Mint
            ['name' => 'Community Outreach', 'color_code' => '#8540f5', 'icon' => 'bi-people-fill'],       // Violet
            ['name' => 'Emergency Response', 'color_code' => '#e83e8c', 'icon' => 'bi-exclamation-triangle'], // Pink
            ['name' => 'Other', 'color_code' => '#6c757d', 'icon' => 'bi-question-circle']                 // Gray
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