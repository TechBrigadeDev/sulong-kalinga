<?php
// database/factories/MedicationScheduleFactory.php
namespace Database\Factories;

use App\Models\MedicationSchedule;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicationScheduleFactory extends Factory
{
    protected $model = MedicationSchedule::class;

    public function definition()
    {
        $medicationTypes = [
            'tablet', 'capsule', 'liquid', 'injection', 'inhaler', 'topical', 'drops', 'other'
        ];
        
        $asNeeded = $this->faker->boolean(10); // 10% chance of being as-needed
        
        // For non-as-needed medications, determine which times to administer
        $morningEnabled = $asNeeded ? false : $this->faker->boolean(70);
        $noonEnabled = $asNeeded ? false : $this->faker->boolean(40);
        $eveningEnabled = $asNeeded ? false : $this->faker->boolean(60);
        $nightEnabled = $asNeeded ? false : $this->faker->boolean(30);
        
        // At least one time should be enabled if not as-needed
        if (!$asNeeded && !($morningEnabled || $noonEnabled || $eveningEnabled || $nightEnabled)) {
            $morningEnabled = true;
        }
        
        return [
            'beneficiary_id' => Beneficiary::inRandomOrder()->first()->beneficiary_id ?? 
                              Beneficiary::factory()->create()->beneficiary_id,
            'medication_name' => $this->faker->randomElement([
                'Metformin', 'Lisinopril', 'Atorvastatin', 'Levothyroxine', 
                'Albuterol', 'Warfarin', 'Furosemide', 'Omeprazole',
                'Amlodipine', 'Metoprolol', 'Sertraline', 'Hydrochlorothiazide'
            ]),
            'dosage' => $this->faker->randomElement([
                '500mg', '10mg', '20mg', '50mcg', '100mcg', '5mg', '40mg', '25mg', '2.5mg'
            ]),
            'medication_type' => $this->faker->randomElement($medicationTypes),
            'morning_time' => $morningEnabled ? $this->faker->dateTimeBetween('06:00', '09:00')->format('H:i:00') : null,
            'noon_time' => $noonEnabled ? $this->faker->dateTimeBetween('11:00', '13:00')->format('H:i:00') : null,
            'evening_time' => $eveningEnabled ? $this->faker->dateTimeBetween('16:00', '19:00')->format('H:i:00') : null,
            'night_time' => $nightEnabled ? $this->faker->dateTimeBetween('20:00', '23:00')->format('H:i:00') : null,
            'as_needed' => $asNeeded,
            'with_food_morning' => $morningEnabled ? $this->faker->boolean(70) : false,
            'with_food_noon' => $noonEnabled ? $this->faker->boolean(90) : false,
            'with_food_evening' => $eveningEnabled ? $this->faker->boolean(80) : false,
            'with_food_night' => $nightEnabled ? $this->faker->boolean(50) : false,
            'special_instructions' => $this->faker->boolean(70) ? $this->faker->sentence : null,
            'start_date' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'end_date' => $this->faker->boolean(70) ? 
                          $this->faker->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d') : null,
            'status' => $this->faker->randomElement(['active', 'completed', 'paused']),
            'created_by' => User::where('role_id', '<=', 2)->inRandomOrder()->first()->id ?? 1,
        ];
    }
}