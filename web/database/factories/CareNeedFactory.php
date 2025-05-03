<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CareNeed;

class CareNeedFactory extends Factory
{
    protected $model = CareNeed::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'general_care_plan_id' => 1, // This will be set in the seeder
            'care_category_id' => 1, // This will be set in the seeder
            'frequency' => $this->faker->word,
            'assistance_required' => $this->faker->sentence
        ];
    }
}