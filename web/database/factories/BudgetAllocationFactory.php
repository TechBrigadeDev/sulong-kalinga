<?php

namespace Database\Factories;

use App\Models\BudgetAllocation;
use App\Models\BudgetType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetAllocationFactory extends Factory
{
    protected $model = BudgetAllocation::class;

    // Cache static arrays to avoid repeated DB queries
    protected static $adminIds = null;
    protected static $typeIds = null;
    protected static $budgetTypeDescriptions = [
        'Regular Allocation' => 'Standard monthly budget for ongoing operations and essential services.',
        'Supplemental Budget' => 'Additional funds allocated for special projects or urgent needs.',
        'Grant Funding' => 'Funds received from external grants or donor organizations for specific initiatives.',
        'Program-specific' => 'Budget dedicated to targeted programs or projects for beneficiaries.',
        'Emergency Fund' => 'Reserved budget for immediate response to emergencies and disasters.',
        'Adjustment' => 'Budget adjustment to reflect changes in funding or operational requirements.',
    ];

    public function definition()
    {
        // Cache admin IDs
        if (is_null(self::$adminIds)) {
            self::$adminIds = User::where('role_id', 1)->pluck('id')->toArray();
        }
        $adminId = !empty(self::$adminIds) ? $this->faker->randomElement(self::$adminIds) : User::factory();

        // Cache budget type IDs and names
        if (is_null(self::$typeIds)) {
            self::$typeIds = BudgetType::pluck('budget_type_id', 'name')->toArray();
        }
        $typeNames = array_keys(self::$typeIds);
        $typeName = $this->faker->randomElement($typeNames);
        $typeId = self::$typeIds[$typeName];

        // Create date range (1-3 months)
        $startDate = $this->faker->dateTimeBetween('-12 months', '2025-07-31');
        $endDate = (clone $startDate)->modify('+' . rand(1, 3) . ' months');

        return [
            'budget_type_id' => $typeId,
            'description' => self::$budgetTypeDescriptions[$typeName] ?? 'General budget allocation for organizational needs.',
            'amount' => $this->faker->numberBetween(10000, 50000),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => $startDate,
            'updated_at' => now(),
        ];
    }
}