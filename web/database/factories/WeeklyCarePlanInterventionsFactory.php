<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\WeeklyCarePlanInterventions;
use App\Models\WeeklyCarePlan;
use App\Models\Intervention;
use App\Models\CareCategory;

class WeeklyCarePlanInterventionsFactory extends Factory
{
    protected $model = WeeklyCarePlanInterventions::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'weekly_care_plan_id' => WeeklyCarePlan::inRandomOrder()->first()->weekly_care_plan_id,
            'intervention_id' => Intervention::inRandomOrder()->first()->intervention_id,
            'care_category_id' => CareCategory::inRandomOrder()->first()->care_category_id,
            'intervention_description' => $this->faker->sentence,
            'duration_minutes' => $this->faker->randomFloat(2, 10, 60),
            'implemented' => 1,
        ];
    }
}