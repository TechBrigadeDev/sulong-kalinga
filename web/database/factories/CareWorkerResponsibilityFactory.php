<?php

namespace Database\Factories;

use App\Models\CareWorkerResponsibility;
use App\Models\GeneralCarePlan;
use App\Models\User;
use App\Models\CareCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class CareWorkerResponsibilityFactory extends Factory
{
    protected $model = CareWorkerResponsibility::class;

    // Specific responsibilities by care category
    protected $tasksByCategory = [
        // Mobility (category_id 1)
        1 => [
            'Assist with daily morning exercises focusing on joint mobility',
            'Provide standby assistance during walking practice with appropriate assistive device',
            'Monitor gait pattern and report any deterioration in walking ability',
            'Ensure living space remains free of fall hazards and clutter',
            'Apply assistive devices properly and check for wear and tear',
            'Demonstrate proper transfer techniques to family members',
            'Assist with position changes every 2 hours when bedbound',
            'Document mobility progress and challenges in weekly reports'
        ],
        // Cognitive/Communication (category_id 2)
        2 => [
            'Implement memory exercises using family photos and familiar items',
            'Provide consistent orientation cues including calendar and clock checks',
            'Use clear, simple communication with adequate time for response',
            'Create and maintain routine schedule to reduce confusion',
            'Facilitate regular communication with family members',
            'Engage in reminiscence activities using culturally relevant themes',
            'Report changes in cognitive status to care manager promptly',
            'Support decision-making while respecting autonomy'
        ],
        // Self-sustainability (category_id 3)
        3 => [
            'Assist with personal hygiene while maintaining dignity and privacy',
            'Provide setup assistance for meals with appropriate adaptive equipment',
            'Encourage independence in self-care activities when safe',
            'Monitor skin condition during care activities and report changes',
            'Assist with selecting and laying out appropriate clothing',
            'Provide oral care assistance using preferred products',
            'Ensure adequate hydration throughout the day',
            'Support toileting needs with attention to comfort and dignity'
        ],
        // Disease/Therapy Handling (category_id 4)
        4 => [
            'Administer medications according to prescribed schedule',
            'Monitor vital signs and document according to care plan',
            'Recognize and report adverse medication effects promptly',
            'Provide diabetic foot care following established protocol',
            'Assist with prescribed therapeutic exercises',
            'Maintain accurate intake and output records when required',
            'Prepare for and accompany to medical appointments',
            'Follow specific protocols for chronic disease management'
        ],
        // Daily life/Social contact (category_id 5)
        5 => [
            'Facilitate video calls with family members living abroad',
            'Read aloud preferred materials including religious texts',
            'Engage in conversation about topics of interest during care activities',
            'Coordinate visits from neighbors and community members',
            'Support participation in cultural and religious observances',
            'Facilitate safe community interaction when appropriate',
            'Create opportunities for intergenerational engagement',
            'Support spiritual practices according to personal beliefs'
        ],
        // Outdoor Activities (category_id 6)
        6 => [
            'Accompany on short neighborhood walks with attention to weather conditions',
            'Prepare and assist with transportation to community activities',
            'Support safe gardening activities adapted to abilities',
            'Facilitate attendance at church or religious services',
            'Assist with shopping trips using mobility aids as needed',
            'Ensure appropriate sun protection during outdoor activities',
            'Support participation in barangay senior citizen events',
            'Plan and implement outdoor leisure activities appropriate to condition'
        ],
        // Household Keeping (category_id 7)
        7 => [
            'Maintain clean, safe living environment with particular attention to fall prevention',
            'Prepare meals according to dietary requirements and cultural preferences',
            'Assist with laundry and proper clothing storage',
            'Ensure adequate supplies of medications and personal care items',
            'Manage household waste appropriately',
            'Assist with simple home maintenance for safety',
            'Support budget management and bill payment as needed',
            'Organize personal belongings for easy access'
        ],
        // General responsibilities
        8 => [
            'Document care provided and beneficiary status after each visit',
            'Report changes in condition promptly to care manager',
            'Maintain respectful communication with beneficiary and family members',
            'Attend required training and care plan review meetings',
            'Adhere to safety protocols and infection control practices',
            'Ensure emergency contact information is current and accessible',
            'Coordinate care schedule with family caregivers',
            'Participate in care plan reviews with accurate observations'
        ]
    ];

    /**
     * Define the model's default state with realistic care worker responsibilities.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Either select a specific care category or general responsibilities
        $categorySelection = $this->faker->numberBetween(1, 8);
        
        return [
            'general_care_plan_id' => GeneralCarePlan::inRandomOrder()->first()->general_care_plan_id,
            'care_worker_id' => User::where('role_id', 3)->inRandomOrder()->first()->id,
            'task_description' => $this->faker->randomElement($this->tasksByCategory[$categorySelection]),
        ];
    }
}