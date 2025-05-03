<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\HealthHistory;

class HealthHistoryFactory extends Factory
{
    protected $model = HealthHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'general_care_plan_id' => 1, // This will be set in the seeder
            'medical_conditions' => $this->faker->sentence,
            'medications' => $this->faker->sentence,
            'allergies' => $this->faker->sentence,
            'immunizations' => $this->faker->sentence
        ];
    }
}