<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CognitiveFunction;

class CognitiveFunctionFactory extends Factory
{
    protected $model = CognitiveFunction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'general_care_plan_id' => 1, // This will be set in the seeder
            'memory' => $this->faker->sentence,
            'thinking_skills' => $this->faker->sentence,
            'orientation' => $this->faker->sentence,
            'behavior' => $this->faker->sentence
        ];
    }
}