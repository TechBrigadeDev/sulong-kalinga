<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition()
    {
        // Get all admin users for random assignment
        $adminIds = User::where('role_id', 1)->pluck('id')->toArray();
        $adminId = !empty($adminIds) ? $this->faker->randomElement($adminIds) : null;
        
        // Get all category IDs for random assignment
        $categoryIds = ExpenseCategory::pluck('category_id')->toArray();
        
        return [
            'title' => $this->faker->sentence(3),
            'category_id' => $this->faker->randomElement($categoryIds),
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'payment_method' => $this->faker->randomElement(['cash', 'check', 'bank_transfer', 'gcash']),
            'date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'receipt_number' => strtoupper($this->faker->randomElement(['INV-', 'OR-', 'RCT-'])) . $this->faker->numberBetween(10000, 99999),
            'description' => $this->faker->paragraph(1),
            'receipt_path' => null, // No actual file upload in seeding
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}