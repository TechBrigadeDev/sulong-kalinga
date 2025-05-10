<?php
// database/factories/AppointmentFactory.php
namespace Database\Factories;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition()
    {
        $isFlexibleTime = $this->faker->boolean(20); // 20% chance of flexible time
        $startTime = $isFlexibleTime ? null : $this->faker->dateTimeBetween('08:00', '16:00')->format('H:i:00');
        $endTime = $isFlexibleTime ? null : $this->faker->dateTimeBetween($startTime, '17:00')->format('H:i:00');
        
        $appointmentType = AppointmentType::inRandomOrder()->first() ?? 
                          AppointmentType::factory()->create();
        
        $otherTypeDetails = $appointmentType->type_name === 'Other Appointment' ?
                           $this->faker->sentence : null;
                           
        return [
            'appointment_type_id' => $appointmentType->appointment_type_id,
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'other_type_details' => $otherTypeDetails,
            'date' => $this->faker->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_flexible_time' => $isFlexibleTime,
            'meeting_location' => $this->faker->address,
            'status' => $this->faker->randomElement(['scheduled', 'completed', 'canceled']),
            'notes' => $this->faker->text,
            'created_by' => User::where('role_id', '<=', 2)->inRandomOrder()->first()->id ?? 1,
        ];
    }
}