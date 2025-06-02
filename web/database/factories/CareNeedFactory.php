<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CareNeed;

class CareNeedFactory extends Factory
{
    protected $model = CareNeed::class;

    // Assistance requirements by care category
    protected $assistanceByCategory = [
        // Mobility (category_id 1)
        1 => [
            'frequencies' => ['Daily', 'Twice daily', 'As needed', 'Every morning', 'Every evening', '3 times per week'],
            'assistance' => [
                'Assistance with transfers from bed to chair using gait belt',
                'Supervision during short walks around the home to prevent falls',
                'Contact guarding during stair navigation, holding onto handrail',
                'Complete assistance with transfers to and from bathroom',
                'Standby assistance during walking with quad cane',
                'Physical support when navigating uneven terrain outside the home',
                'Assistance with position changes in bed to prevent pressure sores',
                'Regular assistance with range of motion exercises for lower extremities',
                'Support during morning exercises to maintain mobility',
                'Help with proper positioning in chair to prevent discomfort'
            ]
        ],
        // Cognitive/Communication (category_id 2)
        2 => [
            'frequencies' => ['Daily', 'Multiple times daily', 'During meals', 'Morning and evening', 'As needed', 'Weekly'],
            'assistance' => [
                'Verbal cues for completing multi-step tasks in proper sequence',
                'Written reminders for appointments and medication times',
                'Assistance with phone calls to family members',
                'Simplification of complex instructions into single steps',
                'Redirection when confused or fixated on particular concerns',
                'Regular orientation to time, place, and person',
                'Support with reading mail and important documents',
                'Facilitation of communication with healthcare providers',
                'Use of simple yes/no questions when fatigue affects communication',
                'Reminiscence activities to stimulate long-term memory'
            ]
        ],
        // Self-sustainability (category_id 3)
        3 => [
            'frequencies' => ['Daily', 'Morning and evening', 'Weekly', 'Three times weekly', 'Every other day', 'As needed'],
            'assistance' => [
                'Setup assistance for meals with adaptive utensils',
                'Complete assistance with bathing, ensuring safety and dignity',
                'Supervision during grooming activities with verbal cues',
                'Assistance with upper body dressing, independent with lower body',
                'Complete support with nail care and hair washing',
                'Minimal assistance with feeding when fatigue is present',
                'Setup for tooth brushing with verbal cues through process',
                'Assistance with selecting weather-appropriate clothing',
                'Support with toileting, including transfer and hygiene',
                'Preparation of food items in easily manageable portions'
            ]
        ],
        // Disease/Therapy Handling (category_id 4)
        4 => [
            'frequencies' => ['Daily', 'Twice daily', 'Weekly', 'Monthly', 'Every 4 hours while awake', 'As needed'],
            'assistance' => [
                'Medication setup using weekly pill organizer with verification',
                'Blood glucose monitoring before breakfast and dinner',
                'Blood pressure monitoring each morning, recording results',
                'Assistance with insulin administration after glucose check',
                'Regular assessment for signs of edema in lower extremities',
                'Support with nebulizer treatments during respiratory difficulties',
                'Assistance applying compression stockings before morning ambulation',
                'Wound care for diabetic foot ulcer using sterile technique',
                'Monitoring for medication side effects and reporting to healthcare provider',
                'Transportation to medical appointments with pre-visit preparation'
            ]
        ],
        // Daily life/Social contact (category_id 5)
        5 => [
            'frequencies' => ['Daily', 'Weekly', 'Every other day', 'Twice weekly', 'Monthly', 'As opportunity arises'],
            'assistance' => [
                'Facilitation of video calls with distant family members',
                'Arranging visits from neighbors and community members',
                'Reading aloud religious materials and prayer support',
                'Accompaniment to senior citizen activities in barangay center',
                'Creating opportunities for interaction with young children',
                'Support in writing letters to friends and relatives',
                'Assistance with craft activities that connect to cultural traditions',
                'Facilitation of small gathering for important celebrations',
                'Regular conversation during care activities to provide social engagement',
                'Arrangement of preferred TV programs and radio stations'
            ]
        ],
        // Outdoor Activities (category_id 6)
        6 => [
            'frequencies' => ['Weekly', 'Twice weekly', 'Weather permitting', 'Morning only', 'Every other day', 'Monthly'],
            'assistance' => [
                'Accompanied walks around the neighborhood with rest periods',
                'Transportation and support during market visits',
                'Assistance to attend church services with appropriate mobility aids',
                'Support during short gardening activities in seated position',
                'Facilitation of safe sun exposure for Vitamin D (morning only)',
                'Assistance with community senior activities',
                'Transportation to visit nearby relatives with necessary comfort measures',
                'Support during outdoor family gatherings with attention to fatigue',
                'Accompanying to local plaza in early evening for socialization',
                'Help maintaining small container garden with Filipino vegetables'
            ]
        ],
        // Household Keeping (category_id 7)
        7 => [
            'frequencies' => ['Daily', 'Weekly', 'Twice weekly', 'Monthly', 'As needed', 'Every morning'],
            'assistance' => [
                'Complete assistance with laundry and proper folding of garments',
                'Light meal preparation following traditional dietary preferences',
                'Regular cleaning of living spaces with attention to fall hazards',
                'Assistance managing household waste and recyclables',
                'Support with simple home maintenance tasks',
                'Organization of personal belongings for easy access',
                'Changing bed linens and ensuring clean sleeping environment',
                'Assistance with grocery shopping following cultural food preferences',
                'Management of household budget and bill payments',
                'Ensuring adequate supplies of household necessities'
            ]
        ]
    ];

    /**
     * Define the model's default state with realistic care need data.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category_id = $this->faker->numberBetween(1, 7);
        
        return [
            'general_care_plan_id' => 1, // This will be set in the seeder
            'care_category_id' => $category_id,
            'frequency' => $this->faker->randomElement($this->assistanceByCategory[$category_id]['frequencies']),
            'assistance_required' => $this->faker->randomElement($this->assistanceByCategory[$category_id]['assistance'])
        ];
    }
}