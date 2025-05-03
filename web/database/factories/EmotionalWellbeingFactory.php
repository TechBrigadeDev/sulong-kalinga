<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EmotionalWellbeing;

class EmotionalWellbeingFactory extends Factory
{
    protected $model = EmotionalWellbeing::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'general_care_plan_id' => 1, // This will be set in the seeder
            'mood' => $this->faker->sentence,
            'social_interactions' => $this->faker->sentence,
            'emotional_support_needs' => $this->faker->sentence
        ];
    }
}