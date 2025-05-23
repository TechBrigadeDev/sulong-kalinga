<?php

namespace Database\Factories;

use App\Models\BudgetType;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetTypeFactory extends Factory
{
    protected $model = BudgetType::class;

    public function definition()
    {
        // We'll rely on the seeded budget types instead of generating random ones
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence
        ];
    }
}