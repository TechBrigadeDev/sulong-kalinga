<?php

namespace Database\Factories;

use App\Models\CareWorkerResponsibility;
use App\Models\GeneralCarePlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CareWorkerResponsibilityFactory extends Factory
{
    protected $model = CareWorkerResponsibility::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'general_care_plan_id' => GeneralCarePlan::inRandomOrder()->first()->general_care_plan_id,
            'care_worker_id' => User::where('role_id', 3)->inRandomOrder()->first()->id,
            'task_description' => $this->faker->sentence,
        ];
    }
}