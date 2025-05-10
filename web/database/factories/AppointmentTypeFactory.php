<?php
// database/factories/AppointmentTypeFactory.php
namespace Database\Factories;

use App\Models\AppointmentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentTypeFactory extends Factory
{
    protected $model = AppointmentType::class;

    public function definition()
    {
        return [
            'type_name' => $this->faker->randomElement([
                'Skills Enhancement Training',
                'Quarterly Feedback Sessions',
                'Municipal Development Council (MDC) Participation',
                'Municipal Local Health Board Meeting',
                'LIGA Meeting',
                'Referrals to MHO',
                'Assessment and Review of Care Needs',
                'General Care Plan Finalization',
                'Project Team Meeting',
                'Mentoring Session',
                'Other Appointment'
            ]),
            'color_code' => $this->faker->hexColor,
            'description' => $this->faker->sentence,
        ];
    }
}