<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Mobility;

class MobilityFactory extends Factory
{
    protected $model = Mobility::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'general_care_plan_id' => 1, // This will be set in the seeder
            'walking_ability' => $this->faker->sentence,
            'assistive_devices' => $this->faker->sentence,
            'transportation_needs' => $this->faker->sentence
        ];
    }
}