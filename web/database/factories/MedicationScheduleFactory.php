<?php
// database/factories/MedicationScheduleFactory.php
namespace Database\Factories;

use App\Models\MedicationSchedule;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class MedicationScheduleFactory extends Factory
{
    protected $model = MedicationSchedule::class;
    
    /**
     * Realistic medication data with appropriate dosages and instructions
     */
    protected $medications = [
        [
            'name' => 'Metformin',
            'dosages' => ['500mg', '850mg', '1000mg'],
            'types' => ['tablet'],
            'instructions' => [
                'Take with meals to reduce stomach upset',
                'Avoid alcohol while taking this medication',
                'Report any unusual tiredness or weakness',
                'Maintain regular blood sugar monitoring'
            ],
        ],
        [
            'name' => 'Lisinopril',
            'dosages' => ['5mg', '10mg', '20mg'],
            'types' => ['tablet'],
            'instructions' => [
                'Take at the same time each day',
                'Monitor blood pressure regularly',
                'May cause dizziness when standing up quickly',
                'Report persistent dry cough to your doctor'
            ],
        ],
        [
            'name' => 'Atorvastatin',
            'dosages' => ['10mg', '20mg', '40mg'],
            'types' => ['tablet'],
            'instructions' => [
                'Take in the evening',
                'Avoid grapefruit juice',
                'Report any unexplained muscle pain',
                'Follow cholesterol-lowering diet as recommended'
            ],
        ],
        [
            'name' => 'Amlodipine',
            'dosages' => ['2.5mg', '5mg', '10mg'],
            'types' => ['tablet'],
            'instructions' => [
                'May be taken with or without food',
                'Take at the same time each day',
                'Monitor for swelling in ankles or feet',
                'Continue taking even if feeling well'
            ],
        ],
        [
            'name' => 'Losartan',
            'dosages' => ['25mg', '50mg', '100mg'],
            'types' => ['tablet'],
            'instructions' => [
                'May be taken with or without food',
                'Avoid salt substitutes containing potassium',
                'Rise slowly from sitting or lying position',
                'Continue regular blood pressure monitoring'
            ],
        ],
        [
            'name' => 'Levothyroxine',
            'dosages' => ['25mcg', '50mcg', '75mcg', '100mcg'],
            'types' => ['tablet'],
            'instructions' => [
                'Take on empty stomach, 30-60 minutes before breakfast',
                'Take at least 4 hours apart from calcium or iron supplements',
                'Same time each day for consistent absorption',
                'Do not switch brands without consulting doctor'
            ],
        ],
        [
            'name' => 'Albuterol',
            'dosages' => ['90mcg/actuation'],
            'types' => ['inhaler'],
            'instructions' => [
                'Use as needed for shortness of breath',
                'Wait 1 minute between inhalations if second dose needed',
                'Rinse mouth after use to prevent thrush',
                'Seek medical attention if not effective quickly'
            ],
        ],
        [
            'name' => 'Furosemide',
            'dosages' => ['20mg', '40mg', '80mg'],
            'types' => ['tablet'],
            'instructions' => [
                'Take early in the day to prevent nighttime urination',
                'Monitor for excessive thirst or dizziness',
                'Weigh daily to track fluid loss',
                'Increase potassium-rich foods unless instructed otherwise'
            ],
        ],
        [
            'name' => 'Warfarin',
            'dosages' => ['1mg', '2mg', '3mg', '5mg'],
            'types' => ['tablet'],
            'instructions' => [
                'Take at the same time each day',
                'Maintain consistent vitamin K intake in diet',
                'Avoid significant changes in diet without consulting doctor',
                'Regular INR testing required as scheduled'
            ],
        ],
        [
            'name' => 'Pantoprazole',
            'dosages' => ['20mg', '40mg'],
            'types' => ['tablet', 'capsule'],
            'instructions' => [
                'Take before breakfast',
                'Swallow whole, do not crush or chew',
                'Complete full course of treatment',
                'Report black stools or persistent stomach pain'
            ],
        ],
        [
            'name' => 'Metoprolol',
            'dosages' => ['25mg', '50mg', '100mg'],
            'types' => ['tablet'],
            'instructions' => [
                'Take with or immediately after meals',
                'Do not stop suddenly without doctor approval',
                'May cause drowsiness or dizziness',
                'Check pulse regularly and report significant changes'
            ],
        ],
        [
            'name' => 'Aspirin',
            'dosages' => ['81mg', '325mg'],
            'types' => ['tablet'],
            'instructions' => [
                'Take with food to reduce stomach irritation',
                'Do not crush or chew enteric-coated tablets',
                'Report unusual bleeding or bruising',
                'Not recommended during fever in children'
            ],
        ],
        [
            'name' => 'Insulin',
            'dosages' => ['Variable units as prescribed'],
            'types' => ['injection'],
            'instructions' => [
                'Rotate injection sites',
                'Store current pen/vial at room temperature, extras in refrigerator',
                'Check blood sugar before administration',
                'Adjust dose only as instructed by doctor'
            ],
        ],
        [
            'name' => 'Paracetamol',
            'dosages' => ['500mg', '650mg'],
            'types' => ['tablet', 'capsule'],
            'instructions' => [
                'Do not exceed recommended dose',
                'Allow at least 4 hours between doses',
                'Do not take with other paracetamol-containing products',
                'Avoid alcohol while taking this medication'
            ],
        ],
        [
            'name' => 'Cetirizine',
            'dosages' => ['5mg', '10mg'],
            'types' => ['tablet'],
            'instructions' => [
                'May cause drowsiness',
                'Take at bedtime if daytime drowsiness occurs',
                'May be taken with or without food',
                'Drink plenty of fluids'
            ],
        ],
        [
            'name' => 'Ferrous Sulfate',
            'dosages' => ['325mg'],
            'types' => ['tablet'],
            'instructions' => [
                'Best absorbed on empty stomach, but may take with food if stomach upset occurs',
                'Take with vitamin C-rich foods or juice to enhance absorption',
                'May cause dark stools',
                'Avoid taking with milk, calcium supplements, or antacids'
            ],
        ],
    ];

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
        
        // Select a random medication from our realistic list
        $medication = $this->faker->randomElement($this->medications);
        $medicationName = $medication['name'];
        $dosage = $this->faker->randomElement($medication['dosages']);
        $medicationType = $this->faker->randomElement($medication['types'] ?? ['tablet']);
        $specialInstructions = $this->faker->randomElement($medication['instructions']);
        
        // Generate realistic morning, noon, evening, and night times
        $morningTime = $morningEnabled ? $this->faker->dateTimeBetween('06:00', '09:00')->format('H:i:00') : null;
        $noonTime = $noonEnabled ? $this->faker->dateTimeBetween('11:30', '13:30')->format('H:i:00') : null;
        $eveningTime = $eveningEnabled ? $this->faker->dateTimeBetween('17:00', '19:00')->format('H:i:00') : null;
        $nightTime = $nightEnabled ? $this->faker->dateTimeBetween('20:00', '23:00')->format('H:i:00') : null;
        
        // Generate reasonable start and end dates
        $startDate = $this->faker->dateTimeBetween('-6 months', '-1 week')->format('Y-m-d');
        
        // 70% chance of having an end date
        $endDate = null;
        if ($this->faker->boolean(70)) {
            // End date could be in the past (completed) or future (planned duration)
            if ($this->faker->boolean(30)) {
                // 30% chance it's a completed medication (end date in past)
                $endDate = $this->faker->dateTimeBetween($startDate, 'now')->format('Y-m-d');
            } else {
                // 70% chance it's ongoing with a planned end date
                $endDate = $this->faker->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d');
            }
        }
        
        // Set status based on dates
        $status = 'active';
        $today = Carbon::today();
        $startDateCarbon = Carbon::parse($startDate);
        $endDateCarbon = $endDate ? Carbon::parse($endDate) : null;
        
        if ($startDateCarbon->isAfter($today)) {
            $status = 'scheduled'; // Future medication
        } elseif ($endDateCarbon && $endDateCarbon->isBefore($today)) {
            $status = 'completed'; // Past medication
        } elseif ($this->faker->boolean(10)) {
            $status = 'paused'; // 10% chance of being paused
        }
        
        return [
            'beneficiary_id' => Beneficiary::inRandomOrder()->first()->beneficiary_id ?? 
                              Beneficiary::factory()->create()->beneficiary_id,
            'medication_name' => $medicationName,
            'dosage' => $dosage,
            'medication_type' => $medicationType,
            'morning_time' => $morningTime,
            'noon_time' => $noonTime,
            'evening_time' => $eveningTime,
            'night_time' => $nightTime,
            'as_needed' => $asNeeded,
            'with_food_morning' => $morningEnabled ? $this->faker->boolean(70) : false,
            'with_food_noon' => $noonEnabled ? $this->faker->boolean(90) : false,
            'with_food_evening' => $eveningEnabled ? $this->faker->boolean(80) : false,
            'with_food_night' => $nightEnabled ? $this->faker->boolean(50) : false,
            'special_instructions' => $specialInstructions,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
            'created_by' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id ?? 1,
        ];
    }
}