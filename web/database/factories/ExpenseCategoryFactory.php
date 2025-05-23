<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseCategoryFactory extends Factory
{
    protected $model = ExpenseCategory::class;

    public function definition()
    {
        // We'll rely on the seeded categories instead of generating random ones
        return [
            'name' => $this->faker->word,
            'color_code' => $this->faker->hexColor(),
            'icon' => 'bi-' . $this->faker->word
        ];
    }
}