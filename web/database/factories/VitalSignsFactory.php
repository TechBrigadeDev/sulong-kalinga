<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VitalSigns;

class VitalSignsFactory extends Factory
{
    protected $model = VitalSigns::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate systolic (110-170) and diastolic (60-100) values
        $systolic = $this->faker->numberBetween(110, 170);
        $diastolic = $this->faker->numberBetween(60, 100);

        return [
            // Format blood pressure as "systolic/diastolic"
            'blood_pressure' => "{$systolic}/{$diastolic}",
            
            // Body temperature with 1 decimal place in normal range
            'body_temperature' => $this->faker->randomFloat(1, 36.0, 37.5),
            
            // Pulse rate as integer in normal range
            'pulse_rate' => $this->faker->numberBetween(60, 100),
            
            // Respiratory rate as integer in normal range
            'respiratory_rate' => $this->faker->numberBetween(12, 20),
            
            // Required user ID for created_by field
            'created_by' => 1, // This will be set in the seeder
            
            // Add timestamps
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }

    /**
     * Define a state for fever condition
     */
    public function fever()
    {
        return $this->state(function (array $attributes) {
            return [
                'body_temperature' => $this->faker->randomFloat(1, 37.6, 39.5),
            ];
        });
    }
    
    /**
     * Define a state for high blood pressure
     */
    public function highBloodPressure()
    {
        return $this->state(function (array $attributes) {
            $systolic = $this->faker->numberBetween(140, 190);
            $diastolic = $this->faker->numberBetween(90, 120);
            
            return [
                'blood_pressure' => "{$systolic}/{$diastolic}",
            ];
        });
    }
    
    /**
     * Define a state for low blood pressure
     */
    public function lowBloodPressure()
    {
        return $this->state(function (array $attributes) {
            $systolic = $this->faker->numberBetween(80, 100);
            $diastolic = $this->faker->numberBetween(50, 65);
            
            return [
                'blood_pressure' => "{$systolic}/{$diastolic}",
            ];
        });
    }
}