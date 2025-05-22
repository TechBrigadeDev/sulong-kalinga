<?php

namespace Database\Factories;

use App\Models\BudgetAllocation;
use App\Models\BudgetType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetAllocationFactory extends Factory
{
    protected $model = BudgetAllocation::class;

    public function definition()
    {
        // Get all admin users for random assignment
        $adminIds = User::where('role_id', 1)->pluck('id')->toArray();
        $adminId = !empty($adminIds) ? $this->faker->randomElement($adminIds) : null;
        
        // Get all budget type IDs for random assignment
        $typeIds = BudgetType::pluck('budget_type_id')->toArray();
        
        // Create date range (1-3 months)
        $startDate = $this->faker->dateTimeBetween('-12 months', '+3 months');
        $endDate = clone $startDate;
        $endDate->modify('+' . rand(1, 3) . ' months');
        
        return [
            'amount' => $this->faker->randomElement([10000, 15000, 20000, 25000, 30000, 50000]),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'budget_type_id' => $this->faker->randomElement($typeIds),
            'description' => $this->faker->optional(0.7)->sentence(),
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}