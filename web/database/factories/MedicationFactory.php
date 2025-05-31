<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Medication;

class MedicationFactory extends Factory
{
    protected $model = Medication::class;

    // List of common medications for Filipino elderly
    protected $commonMedications = [
        [
            'name' => 'Amlodipine',
            'dosages' => ['2.5mg', '5mg', '10mg'],
            'frequencies' => ['once daily', 'twice daily'],
            'instructions' => [
                'Take in the morning with or without food',
                'Take at the same time each day to maintain blood pressure control',
                'Avoid grapefruit juice while taking this medication',
                'Monitor for swelling in ankles or feet and report to healthcare provider'
            ]
        ],
        [
            'name' => 'Losartan',
            'dosages' => ['25mg', '50mg', '100mg'],
            'frequencies' => ['once daily', 'twice daily'],
            'instructions' => [
                'Take with or without food at the same time each day',
                'Do not take potassium supplements without consulting your doctor',
                'Stand up slowly from sitting or lying position to avoid dizziness',
                'Report persistent dizziness or fainting to your healthcare provider'
            ]
        ],
        [
            'name' => 'Metformin',
            'dosages' => ['500mg', '850mg', '1000mg'],
            'frequencies' => ['once daily', 'twice daily', 'three times daily with meals'],
            'instructions' => [
                'Take with meals to reduce stomach upset',
                'Do not crush or chew extended-release tablets',
                'Monitor blood sugar regularly as directed by your healthcare provider',
                'Temporarily stop during periods of dehydration or before X-ray procedures'
            ]
        ],
        [
            'name' => 'Atorvastatin',
            'dosages' => ['10mg', '20mg', '40mg'],
            'frequencies' => ['once daily at bedtime'],
            'instructions' => [
                'Take at bedtime as cholesterol production is highest at night',
                'Avoid excessive consumption of pomelo or pomelo juice',
                'Report any unexplained muscle pain or weakness to your doctor immediately',
                'Follow recommended low-fat diet for best results'
            ]
        ],
        [
            'name' => 'Clopidogrel',
            'dosages' => ['75mg'],
            'frequencies' => ['once daily'],
            'instructions' => [
                'Take with or without food at the same time each day',
                'Do not discontinue without consulting your doctor',
                'Inform all healthcare providers you are taking this medication before procedures',
                'Report any unusual bleeding or bruising to your healthcare provider'
            ]
        ],
        [
            'name' => 'Aspirin',
            'dosages' => ['80mg', '100mg'],
            'frequencies' => ['once daily'],
            'instructions' => [
                'Take with food to reduce stomach irritation',
                'Do not crush or chew enteric-coated tablets',
                'Report any signs of bleeding or black tarry stools to your doctor immediately',
                'Avoid taking with other NSAIDs unless directed by your healthcare provider'
            ]
        ],
        [
            'name' => 'Metoprolol',
            'dosages' => ['25mg', '50mg', '100mg'],
            'frequencies' => ['once daily', 'twice daily'],
            'instructions' => [
                'Take with or immediately after meals',
                'Do not suddenly stop taking this medication',
                'Check pulse regularly and report significant changes to your doctor',
                'May cause drowsiness; use caution when driving or operating machinery'
            ]
        ],
        [
            'name' => 'Furosemide',
            'dosages' => ['20mg', '40mg'],
            'frequencies' => ['once daily in the morning', 'twice daily (morning and early afternoon)'],
            'instructions' => [
                'Take early in the day to prevent nighttime urination',
                'Increase fluid and potassium intake unless otherwise directed',
                'Monitor for excessive thirst, weakness, or dizziness',
                'Weigh yourself daily at the same time to monitor fluid status'
            ]
        ],
        [
            'name' => 'Allopurinol',
            'dosages' => ['100mg', '300mg'],
            'frequencies' => ['once daily', 'twice daily'],
            'instructions' => [
                'Take after meals with plenty of water',
                'Drink 8-10 glasses of water daily while taking this medication',
                'Report any skin rash or fever to your doctor immediately',
                'Avoid alcohol and high-purine foods like organ meats and dried beans'
            ]
        ],
        [
            'name' => 'Levothyroxine',
            'dosages' => ['25mcg', '50mcg', '88mcg', '100mcg'],
            'frequencies' => ['once daily in the morning on empty stomach'],
            'instructions' => [
                'Take on an empty stomach, 30-60 minutes before breakfast',
                'Take at least 4 hours apart from calcium or iron supplements',
                'Avoid taking with grated ubi (purple yam) or other high-fiber foods',
                'Do not switch brands without consulting your doctor'
            ]
        ],
        [
            'name' => 'Memantine',
            'dosages' => ['5mg', '10mg'],
            'frequencies' => ['once daily', 'twice daily'],
            'instructions' => [
                'May be taken with or without food',
                'Take at the same time each day to maintain consistent levels',
                'Report any changes in behavior or alertness to your healthcare provider',
                'Do not stop taking suddenly without consulting your doctor'
            ]
        ],
        [
            'name' => 'Multivitamins for Seniors',
            'dosages' => ['1 tablet'],
            'frequencies' => ['once daily'],
            'instructions' => [
                'Take with food for best absorption',
                'Do not exceed recommended dose',
                'Store in a cool, dry place away from direct sunlight',
                'Report any unusual symptoms to your healthcare provider'
            ]
        ]
    ];

    /**
     * Define the model's default state with realistic medication data.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Select a random medication from our list
        $medication = $this->faker->randomElement($this->commonMedications);
        
        return [
            'general_care_plan_id' => 1, // This will be set in the seeder
            'medication' => $medication['name'],
            'dosage' => $this->faker->randomElement($medication['dosages']),
            'frequency' => $this->faker->randomElement($medication['frequencies']),
            'administration_instructions' => $this->faker->randomElement($medication['instructions'])
        ];
    }
}