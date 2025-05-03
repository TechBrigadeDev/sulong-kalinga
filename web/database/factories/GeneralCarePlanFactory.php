<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\GeneralCarePlan;
use App\Models\User;

class GeneralCarePlanFactory extends Factory
{
    protected $model = GeneralCarePlan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random user with role_id 3
        $care_worker_id = User::where('role_id', 3)->inRandomOrder()->first()->id;

        return [
            'care_worker_id' => $care_worker_id,
            'emergency_plan' => $this->faker->paragraph,
            'review_date' => $this->faker->date,
            'created_at' => $this->faker->date(),
            'updated_at' => now(),
        ];
    }
}