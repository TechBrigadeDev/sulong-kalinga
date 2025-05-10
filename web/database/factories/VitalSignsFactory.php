<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VitalSigns;
use App\Models\User;

class VitalSignsFactory extends Factory
{
    protected $model = VitalSigns::class;

    /**
     * Define the model's default state with realistic vital signs for elderly.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // More realistic blood pressure ranges for elderly
        $systolic = $this->faker->numberBetween(110, 160);
        $diastolic = $this->faker->numberBetween(70, 95);
        $bloodPressure = "{$systolic}/{$diastolic}";
        
        // Realistic body temperature (36.1 - 37.2)
        $bodyTemp = $this->faker->randomFloat(1, 36.1, 37.2);
        
        // Realistic pulse rate for elderly (60-100)
        $pulseRate = $this->faker->numberBetween(60, 100);
        
        // Realistic respiratory rate (12-20)
        $respiratoryRate = $this->faker->numberBetween(12, 20);
        
        return [
            'blood_pressure' => $bloodPressure,
            'body_temperature' => $bodyTemp,
            'pulse_rate' => $pulseRate,
            'respiratory_rate' => $respiratoryRate,
            'created_by' => User::where('role_id', 3)->inRandomOrder()->first()->id,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}