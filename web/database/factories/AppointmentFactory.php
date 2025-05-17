<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startTime = $this->faker->dateTimeBetween('08:00', '16:00')->format('H:i:s');
        $endTime = Carbon::parse($startTime)->addHours(rand(1, 3))->format('H:i:s');
        $isFlexibleTime = $this->faker->boolean(20); // 20% chance of being flexible time
        
        return [
            'appointment_type_id' => AppointmentType::inRandomOrder()->first()->appointment_type_id ?? 1,
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'other_type_details' => null,
            'date' => $this->faker->dateTimeBetween('+1 days', '+2 months'),
            'start_time' => $isFlexibleTime ? null : $startTime,
            'end_time' => $isFlexibleTime ? null : $endTime,
            'is_flexible_time' => $isFlexibleTime,
            'meeting_location' => $this->faker->randomElement(['Conference Room', 'Office', 'Community Hall', 'Training Room', 'Remote/Virtual']),
            'status' => 'scheduled',
            'notes' => $this->faker->optional(70)->paragraph(),
            'created_by' => User::where('role_id', '<=', 2)->inRandomOrder()->first()->id ?? 1,
            'updated_by' => null
        ];
    }

    /**
     * Indicate that the appointment is completed.
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => $this->faker->dateTimeBetween('-2 months', '-1 day'),
                'status' => 'completed',
            ];
        });
    }

    /**
     * Indicate that the appointment is canceled.
     */
    public function canceled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'canceled',
            ];
        });
    }
}