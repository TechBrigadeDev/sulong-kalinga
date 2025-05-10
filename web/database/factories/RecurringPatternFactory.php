<?php
// database/factories/RecurringPatternFactory.php
namespace Database\Factories;

use App\Models\RecurringPattern;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecurringPatternFactory extends Factory
{
    protected $model = RecurringPattern::class;

    public function definition()
    {
        $patternType = $this->faker->randomElement(['daily', 'weekly', 'monthly']);
        $dayOfWeek = $patternType === 'weekly' ? $this->faker->numberBetween(0, 6) : null;
        
        return [
            'pattern_type' => $patternType,
            'day_of_week' => $dayOfWeek,
            'recurrence_end' => $this->faker->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
        ];
    }
    
    /**
     * Configure the model factory to attach to an appointment
     */
    public function forAppointment($appointmentId)
    {
        return $this->state(function (array $attributes) use ($appointmentId) {
            return [
                'appointment_id' => $appointmentId,
                'visitation_id' => null,
            ];
        });
    }
    
    /**
     * Configure the model factory to attach to a visitation
     */
    public function forVisitation($visitationId)
    {
        return $this->state(function (array $attributes) use ($visitationId) {
            return [
                'appointment_id' => null,
                'visitation_id' => $visitationId,
            ];
        });
    }
}