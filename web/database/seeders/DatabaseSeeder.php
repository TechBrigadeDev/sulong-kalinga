<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\FamilyMember;
use App\Models\PortalAccount;
use App\Models\GeneralCarePlan;
use App\Models\HealthHistory;
use App\Models\EmotionalWellbeing;
use App\Models\CognitiveFunction;
use App\Models\Mobility;
use App\Models\CareNeed;
use App\Models\Medication;
use App\Models\VitalSigns;
use App\Models\WeeklyCarePlan;
use App\Models\WeeklyCarePlanInterventions;
use App\Models\Intervention;
use App\Models\CareCategory;
use App\Models\CareWorkerResponsibility;
use App\Models\Notification;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\MessageReadStatus;
use Carbon\Carbon;
use App\Models\AppointmentType;
use App\Models\Appointment;
use App\Models\AppointmentOccurrence; 
use App\Models\RecurringPattern;
use App\Models\AppointmentParticipant;
use App\Models\Visitation;
use App\Models\VisitationOccurrence;

class DatabaseSeeder extends Seeder
{
    /**
     * The Faker instance for generating random data.
     *
     * @var \Faker\Generator
     */
    protected $faker;
    
    /**
     * Create a new seeder instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->faker = Factory::create('en_PH'); // Use Philippines locale for more relevant data
    }
    
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Generate portal accounts for beneficiaries (100 accounts for 100 beneficiaries)
        PortalAccount::factory()->count(100)->create();

        // 2. Generate users with different roles based on the requirements
        // Create 3 admins
        User::factory()->count(3)->create(['role_id' => 1]); 
        
        // Create 2 care managers
        $careManagers = [];
        for ($i = 0; $i < 2; $i++) {
            $careManagers[] = User::factory()->create([
                'role_id' => 2,
            ]);
        }
        
        // Create 10 care workers with assigned care managers - 5 per care manager
        $careWorkers = [];
        for ($i = 0; $i < 10; $i++) {
            // Assign care worker to appropriate care manager based on location
            $careManagerIndex = ($i < 5) ? 0 : 1; // First 5 to first manager, next 5 to second manager
            $location = ($careManagerIndex === 0) ? 'San Roque' : 'Mondragon';
            
            $careWorkers[] = User::factory()->create([
                'role_id' => 3,
                'assigned_care_manager_id' => $careManagers[$careManagerIndex]->id,
            ]);
        }

        // 3. Create general care plans for all beneficiaries
        $generalCarePlans = [];
        for ($i = 1; $i <= 100; $i++) {
            // Determine which area this care plan belongs to (San Roque or Mondragon)
            $location = ($i <= 50) ? 'San Roque' : 'Mondragon';
            
            // Assign care worker based on location (more realistic distribution)
            $locationCareWorkers = ($location === 'San Roque') 
                ? array_slice($careWorkers, 0, 5) 
                : array_slice($careWorkers, 5, 5);
                
            $careWorkerId = $locationCareWorkers[array_rand($locationCareWorkers)]->id;
            
            // Create the general care plan with a specific ID
            $generalCarePlan = GeneralCarePlan::create([
                'general_care_plan_id' => $i,
                'review_date' => Carbon::now()->addMonths(6),
                'emergency_plan' => 'Standard emergency procedures for ' . $location . ' residents',
                'care_worker_id' => $careWorkerId,
                'created_at' => now()->subDays(rand(30, 180)), // More varied creation dates
                'updated_at' => now()->subDays(rand(1, 30))
            ]);
            
            // Create emotional wellbeing for this general care plan
            EmotionalWellbeing::factory()->create([
                'general_care_plan_id' => $i,
            ]);
            
            // Create health history for this general care plan
            HealthHistory::factory()->create([
                'general_care_plan_id' => $i,
            ]);
            
            // Create cognitive function for this general care plan
            CognitiveFunction::factory()->create([
                'general_care_plan_id' => $i,
            ]);
            
            // Create mobility for this general care plan
            Mobility::factory()->create([
                'general_care_plan_id' => $i,
            ]);
            
            // Create medications for this general care plan (2-4 medications per beneficiary)
            foreach (range(1, rand(2, 4)) as $medicationIndex) {
                Medication::factory()->create([
                    'general_care_plan_id' => $i,
                ]);
            }
            
            // Create care needs for this general care plan
            foreach (range(1, 7) as $careCategoryId) {
                CareNeed::factory()->create([
                    'general_care_plan_id' => $i,
                    'care_category_id' => $careCategoryId,
                ]);
            }
            
            // Create 3-5 care worker responsibilities for this general care plan
            foreach (range(1, rand(3, 5)) as $index) {
                CareWorkerResponsibility::factory()->create([
                    'general_care_plan_id' => $i,
                    'care_worker_id' => $careWorkerId,
                ]);
            }
            
            $generalCarePlans[] = $generalCarePlan;
        }

        // 4. Create 50 beneficiaries from San Roque
        $beneficiaries = [];
        for ($i = 0; $i < 50; $i++) {
            // Get the barangay ID instead of the name
            $barangayName = $this->getRandomBarangay('San Roque');
            $barangayId = $this->getBarangayIdByName($barangayName, 2); // 2 is San Roque municipality ID
            
            $beneficiary = Beneficiary::factory()->create([
                'general_care_plan_id' => $i + 1,
                'street_address' => $this->generateAddress('San Roque'),
                'barangay_id' => $barangayId,
                'municipality_id' => 2 // San Roque municipality ID
            ]);
            
            // Create family members for each beneficiary (1-3 members)
            $familyMemberCount = rand(1, 3);
            FamilyMember::factory($familyMemberCount)
                ->forBeneficiary($beneficiary->beneficiary_id)
                ->create();
                
            $beneficiaries[] = $beneficiary;
        }
        
        // 5. Create 50 beneficiaries from Mondragon
        $mondragronBeneficiaries = [];
        for ($i = 0; $i < 50; $i++) {
            // Get the barangay ID instead of the name
            $barangayName = $this->getRandomBarangay('Mondragon');
            $barangayId = $this->getBarangayIdByName($barangayName, 1); // 1 is Mondragon municipality ID
            
            $beneficiary = Beneficiary::factory()->create([
                'general_care_plan_id' => $i + 51, // Starting from 51
                'street_address' => $this->generateAddress('Mondragon'),
                'barangay_id' => $barangayId,
                'municipality_id' => 1 // Mondragon municipality ID
            ]);
            
            $familyMemberCount = rand(1, 3);
            FamilyMember::factory($familyMemberCount)
                ->forBeneficiary($beneficiary->beneficiary_id)
                ->create();
                
            $mondragronBeneficiaries[] = $beneficiary;
        }
        
        // Combine all beneficiaries
        $allBeneficiaries = array_merge($beneficiaries, $mondragronBeneficiaries);
        
        // 6. Generate weekly care plans with realistic intervention data
        // Reduce frequency to keep DB size manageable but data still meaningful
        $this->generateRealisticWeeklyCarePlans($careWorkers, $allBeneficiaries);

        // 7. Generate notifications - adjusted for new user counts
        $this->generateNotifications();

        // 8. Generate conversations and messages - adjusted for new user structure
        $this->generateConversations();

        // 9. Generate scheduling data (appointments, visitations, medication schedules)
        $this->generateSchedulingData($careWorkers, $allBeneficiaries);

        // 10. Generate emergency notices and service requests
        $this->generateEmergencyAndServiceRequests();
    }

    /**
     * Generate a realistic address in the specified municipality
     */
    private function generateAddress($municipality)
    {
        $streetPatterns = [
            'Purok %d, %s',
            'Sitio %s, %s',
            'Phase %d, %s',
            'Block %d Lot %d, %s',
            'Zone %d, %s',
            '%d %s Street, %s'
        ];
        
        $streetNames = [
            'Rizal', 'Mabini', 'Bonifacio', 'Kalayaan', 'Maharlika', 
            'Sampaguita', 'Ilang-ilang', 'Magnofia', 'Orchid', 'Jasmine',
            'Lapu-Lapu', 'Del Pilar', 'Bayanihan', 'Kamias', 'Rosal'
        ];
        
        $sitioNames = [
            'Malunggay', 'Santol', 'Kaimito', 'Sampaloc', 'Kamagong',
            'Bayabas', 'Mangga', 'Niyog', 'Camachile', 'Balimbing'
        ];
        
        $pattern = $streetPatterns[array_rand($streetPatterns)];
        
        if (strpos($pattern, 'Sitio') !== false) {
            return sprintf($pattern, $sitioNames[array_rand($sitioNames)], $municipality);
        } elseif (strpos($pattern, 'Street') !== false) {
            return sprintf($pattern, rand(1, 99), $streetNames[array_rand($streetNames)], $municipality);
        } elseif (strpos($pattern, 'Block') !== false) {
            return sprintf($pattern, rand(1, 20), rand(1, 50), $municipality);
        } else {
            return sprintf($pattern, rand(1, 10), $municipality);
        }
    }
    
    /**
     * Get a random barangay from the specified municipality
     */
    private function getRandomBarangay($municipality) 
    {
        $barangays = [
            'San Roque' => [
                'Gata', 'Jamoog', 'Lao', 'Magsaysay', 'Bantayan',
                'Poblacion Norte', 'Poblacion Sur', 'San Antonio', 
                'San Isidro', 'San Juan', 'San Miguel'
            ],
            'Mondragon' => [
                'A. Bonifacio', 'Bagasbas', 'Bugko', 'Cahicsan', 'Chitongco',
                'De Maria', 'E. Duran', 'Eco', 'Fadriqueña', 'Flamboyant',
                'Hinabangan', 'Imelda', 'La Trinidad', 'Makiwalo', 'Maragat'
            ]
        ];
        
        return $barangays[$municipality][array_rand($barangays[$municipality])];
    }

    /**
     * Generate realistic weekly care plans with diverse interventions
     * Using existing interventions from the database
     */
    private function generateRealisticWeeklyCarePlans($careWorkers, $beneficiaries)
    {
        // Realistic illnesses list
        $commonIllnesses = [
            'Common Cold',
            'Influenza',
            'Urinary Tract Infection',
            'Pneumonia',
            'Bronchitis',
            'Gastroenteritis',
            'Shingles',
            'Pressure Ulcers',
            'Dehydration',
            'Acute Confusion',
            'Constipation',
            'Cellulitis',
            'Lower Respiratory Tract Infection',
            'Conjunctivitis'
        ];

        // Fetch all care categories
        $careCategories = CareCategory::all();
        $interventionsByCategoryId = [];

        // Get all interventions by category
        foreach ($careCategories as $category) {
            $interventions = Intervention::where('care_category_id', $category->care_category_id)->get();
            if ($interventions->count() > 0) {
                $interventionsByCategoryId[$category->care_category_id] = $interventions->pluck('intervention_id')->toArray();
            }
        }
        
        // Define the date range - from beginning of 2024 to present (May 2025)
        $startDate = Carbon::createFromDate(2024, 1, 1); // Start from January 1, 2024
        $endDate = Carbon::now(); // Current date (May 19, 2025)
        
        \Log::info("Generating weekly care plans from {$startDate->toDateString()} to {$endDate->toDateString()}");
        
        $wcpCount = 0;
        $careWorkerCollection = collect($careWorkers);
        
        // Create a WCP for each beneficiary weekly in the date range
        foreach ($beneficiaries as $beneficiary) {
            // Skip if no general care plan exists
            if (!$beneficiary->generalCarePlan) {
                \Log::warning("Beneficiary ID {$beneficiary->beneficiary_id} has no general care plan");
                continue;
            }
            
            $currentDate = $startDate->copy();
            
            // Get care_worker_id directly from the GeneralCarePlan model
            $careWorkerId = $beneficiary->generalCarePlan->care_worker_id;
            
            // Determine care worker for this beneficiary
            $careWorker = null;
            
            // First, try to find the exact care worker by ID
            if ($careWorkerId) {
                $careWorker = $careWorkerCollection->firstWhere('id', $careWorkerId);
            }
            
            // If no care worker found, assign one based on location
            if (!$careWorker) {
                // Determine which area the beneficiary is from based on municipality
                $isSanRoque = ($beneficiary->municipality_id == 2); // Assuming municipality_id 2 is San Roque
                
                // Filter care workers for this location
                $areaCareWorkers = $careWorkerCollection->filter(function($worker) use ($isSanRoque) {
                    // For San Roque workers, use even IDs (0,2,4,6,8)
                    // For Mondragon workers, use odd IDs (1,3,5,7,9)
                    return $isSanRoque ? ($worker->id % 2 == 0) : ($worker->id % 2 == 1);
                });
                
                if ($areaCareWorkers->count() > 0) {
                    $careWorker = $areaCareWorkers->random();
                    \Log::info("Assigned area-matched care worker ID {$careWorker->id} to beneficiary ID {$beneficiary->beneficiary_id}");
                } else {
                    // Last resort: just take any care worker
                    $careWorker = $careWorkerCollection->random();
                    \Log::warning("Assigned random care worker ID {$careWorker->id} to beneficiary ID {$beneficiary->beneficiary_id}");
                }
            }
            
            // For each WEEKLY interval in the date range (every 7 days)
            while ($currentDate->lte($endDate)) {
                // Create vital signs with realistic values
                $systolic = $this->faker->numberBetween(110, 160);
                $diastolic = $this->faker->numberBetween(70, 95);
                $vitalSigns = VitalSigns::create([
                    'blood_pressure' => "{$systolic}/{$diastolic}",
                    'body_temperature' => $this->faker->randomFloat(1, 36.1, 37.2),
                    'pulse_rate' => $this->faker->numberBetween(60, 100),
                    'respiratory_rate' => $this->faker->numberBetween(12, 20),
                    'created_by' => $careWorker->id,
                    'created_at' => $currentDate->copy(),
                    'updated_at' => $currentDate->copy()
                ]);
                
                // Select 0-2 illnesses randomly
                $selectedIllnesses = $this->faker->randomElements(
                    $commonIllnesses,
                    $this->faker->numberBetween(0, 2)
                );
                
                // Pick a random day during the current week (0-3 days from the start of the week)
                $randomDayOffset = rand(0, 3);
                $wcpDate = $currentDate->copy()->addDays($randomDayOffset);
                
                // Create weekly care plan with realistic assessment and illnesses
                $weeklyCarePlan = WeeklyCarePlan::create([
                    'beneficiary_id' => $beneficiary->beneficiary_id,
                    'care_worker_id' => $careWorker->id,
                    'vital_signs_id' => $vitalSigns->vital_signs_id,
                    'date' => $wcpDate,
                    'assessment' => $this->getRealisticAssessment(),
                    'illnesses' => !empty($selectedIllnesses) ? json_encode($selectedIllnesses) : null,
                    'photo_path' => 'weekly_care_plans/photos/seed_photo_' . rand(1, 10) . '.jpg',
                    'evaluation_recommendations' => $this->getRealisticRecommendation(),
                    'created_by' => $careWorker->id,
                    'updated_by' => $careWorker->id,
                    'created_at' => $wcpDate,
                    'updated_at' => $wcpDate
                ]);
                
                $wcpCount++;
                
                // Add 3-8 interventions from different categories
                $numInterventions = rand(3, 8);
                $usedCategoryIds = [];
                
                for ($j = 0; $j < $numInterventions; $j++) {
                    // Pick a category (prioritize unused ones)
                    $availableCategoryIds = array_diff(array_keys($interventionsByCategoryId), $usedCategoryIds);
                    
                    if (empty($availableCategoryIds)) {
                        // If we've used all categories, reset and pick randomly
                        $categoryId = array_rand($interventionsByCategoryId);
                    } else {
                        // Pick from unused categories
                        $categoryId = $availableCategoryIds[array_rand($availableCategoryIds)];
                        $usedCategoryIds[] = $categoryId;
                    }
                    
                    // Get interventions for this category
                    $categoryInterventions = $interventionsByCategoryId[$categoryId];
                    
                    if (!empty($categoryInterventions)) {
                        // Pick a random intervention from this category
                        $interventionId = $categoryInterventions[array_rand($categoryInterventions)];
                        
                        // Determine if this should be a custom intervention (20% chance)
                        $isCustom = (rand(1, 5) === 1);
                        
                        if ($isCustom) {
                            // Custom intervention
                            WeeklyCarePlanInterventions::create([
                                'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                                'care_category_id' => $categoryId,
                                'intervention_description' => 'Custom: ' . $this->getRandomCustomIntervention($categoryId),
                                'duration_minutes' => rand(15, 120),
                                'implemented' => (rand(1, 10) > 2) // 80% chance of being implemented
                            ]);
                        } else {
                            // Standard intervention
                            WeeklyCarePlanInterventions::create([
                                'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                                'intervention_id' => $interventionId,
                                'duration_minutes' => rand(15, 120),
                                'implemented' => (rand(1, 10) > 2) // 80% chance of being implemented
                            ]);
                        }
                    }
                }
                
                // Move forward by 1 week for weekly plans
                $currentDate->addWeek();
            }
            
            // Only log every 10 beneficiaries to reduce log spam
            if ($beneficiary->beneficiary_id % 10 == 0) {
                \Log::info("Generated weekly care plans for beneficiaries through ID: {$beneficiary->beneficiary_id}");
            }
        }
        
        \Log::info("Created a total of {$wcpCount} weekly care plans");
    }

    /**
     * Get a random custom intervention description based on category
     */
    private function getRandomCustomIntervention($categoryId)
    {
        $customInterventions = [
            1 => [ // Mobility
                'Specialized wheelchair transfer technique',
                'Custom mobility exercise program',
                'Beach walk assistance',
                'Garden pathway navigation',
                'Stair climbing with modified technique'
            ],
            2 => [ // Cognitive/Communication
                'Personalized memory card games',
                'Digital communication device training',
                'Native language practice sessions',
                'Custom flash card exercises',
                'Family photo recognition practice'
            ],
            3 => [ // Self-Sustainability
                'Modified clothing fastener technique',
                'Customized eating utensil training',
                'Specialized shower chair instruction',
                'Personal hygiene adapted routine',
                'Medication organization system training'
            ],
            4 => [ // Daily life/Social contact
                'Virtual family reunion setup',
                'Religious service accompaniment',
                'Community garden participation',
                'Senior center special event attendance',
                'Neighborhood walking group participation'
            ],
            5 => [ // Disease/Therapy Handling
                'Specialized diabetic foot care',
                'Custom cardiac rehabilitation exercises',
                'Modified stroke recovery techniques',
                'Personalized pain management approach',
                'Adaptive arthritis management'
            ],
            6 => [ // Outdoor Activities
                'Modified outdoor exercise routine',
                'Nature observation activity',
                'Community garden participation',
                'Outdoor social interaction support',
                'Supervised neighborhood walking'
            ],
            7 => [ // Household Keeping
                'Modified kitchen organization system',
                'Adaptive cooking technique instruction',
                'Energy-conserving housework approach',
                'Specialized laundry management',
                'Safety-focused home organization'
            ]
        ];
        
        // Default to first category if the requested one doesn't exist
        if (!isset($customInterventions[$categoryId])) {
            $categoryId = 1;
        }
        
        return $customInterventions[$categoryId][array_rand($customInterventions[$categoryId])];
    }

    private function getRealisticAssessment()
    {
        $assessments = [
            "Beneficiary appears alert and oriented to time, place, and person. Vital signs are within normal limits. Reports mild joint pain in knees, rating 3/10 on pain scale. Medication compliance is good. No signs of illness or infection noted.",
            
            "Beneficiary is experiencing some shortness of breath upon minimal exertion. Blood pressure is slightly elevated at 145/90. Reports difficulty sleeping due to back discomfort. Needs assistance with bathing and dressing.",
            
            "Assessment shows mild cognitive decline, with some short-term memory issues. Beneficiary can still perform most ADLs independently. Mood appears stable. Appetite is good but reports occasional difficulty chewing harder foods.",
            
            "Beneficiary reports increased fatigue and dizziness when standing. Blood pressure drops by 15mmHg upon standing, indicating possible orthostatic hypotension. No falls reported, but increased risk noted.",
            
            "Beneficiary shows signs of depression with decreased appetite and social withdrawal. Reports feeling 'worthless' and having little energy. Sleep disturbances noted with early morning awakening.",
            
            "Physical assessment shows good mobility using walker. Skin is intact with no pressure areas. Edema noted in both ankles, +2. Breathing is unlabored with clear lung sounds.",
            
            "Beneficiary maintains independent ADLs but requires supervision for medication management. Cognitive status remains stable with good short-term memory. Social engagement has improved with regular family visits.",
            
            "Assessment reveals moderate pain in lower back, self-rated as 5/10. Pain increases with prolonged standing. Using prescribed pain medication with good effect. Mobility remains good with appropriate assistive devices.",
            
            "Beneficiary showing excellent progress with physical therapy exercises. Range of motion in affected shoulder improved by approximately 15 degrees. Self-reports satisfaction with progress and decreased pain levels.",
            
            "Nutritional assessment shows adequate intake of fluids and nutrients. Weight stable at 68kg. No difficulty swallowing noted. Enjoys meals and maintains good appetite. No dietary restrictions required at this time."
        ];
        
        return $assessments[array_rand($assessments)];
    }

    private function getRealisticRecommendation()
    {
        $recommendations = [
            "Continue current medication regimen. Increase fluid intake to 1.5-2L daily. Schedule follow-up blood pressure check in 2 weeks. Encourage daily short walks to maintain mobility.",
            
            "Refer to physical therapy for strengthening exercises. Monitor blood glucose levels twice daily. Review medication schedule with beneficiary to ensure proper timing with meals. Provide education on signs of hypoglycemia.",
            
            "Recommend home safety evaluation to prevent falls. Contact primary physician regarding increased pain medication. Schedule vision assessment. Encourage family to assist with meal preparation twice weekly.",
            
            "Implement cognitive stimulation activities daily. Consider podiatry referral for foot care. Schedule nutrition consultation to address weight loss. Recommend joining community senior center activities once weekly.",
            
            "Monitor for signs of urinary tract infection due to recent symptoms. Encourage use of bedroom commode at night to reduce fall risk. Review proper transfer techniques with caregiver. Schedule memory assessment.",
            
            "Continue weekly blood pressure monitoring. Recommend compression stockings for lower extremity edema. Evaluate effectiveness of pain management strategies at next visit. Encourage socialization through day program participation.",
            
            "Increase protein intake to promote wound healing. Consult with dietitian for personalized meal planning. Continue daily wound care per protocol. Monitor for signs of infection during dressing changes.",
            
            "Maintain current exercise regimen focusing on balance and strength. Consider group exercise sessions for social interaction. Review medication efficacy at next appointment. Keep detailed log of any dizziness episodes.",
            
            "Continue monitoring oxygen saturation levels daily. Ensure proper inhaler technique is maintained. Schedule pulmonary function tests within 30 days. Report any increased shortness of breath immediately.",
            
            "Implement fall prevention strategies including removing throw rugs and improving lighting. Consider grab bar installation in bathroom. Encourage consistent use of prescribed assistive devices. Schedule follow-up in 2 weeks."
        ];
        
        return $recommendations[array_rand($recommendations)];
    }

    private function generateNotifications()
    {
        // Generate notifications for all beneficiaries (but limit to 1-3 per beneficiary)
        $beneficiaries = Beneficiary::all();
        $notificationCount = 0;
        
        foreach ($beneficiaries as $beneficiary) {
            if (rand(1, 4) > 1) { // 75% of beneficiaries get notifications
                $count = rand(1, 3);
                Notification::factory()
                    ->count($count)
                    ->forBeneficiary($beneficiary->beneficiary_id)
                    ->create();
                    
                $notificationCount += $count;
            }
        }
        
        \Log::info("Created {$notificationCount} notifications for beneficiaries");
        
        // Generate notifications for some family members
        $familyMembers = FamilyMember::inRandomOrder()->take(50)->get(); // Half of all family members
        $notificationCount = 0;
        
        foreach ($familyMembers as $familyMember) {
            $count = rand(1, 2);
            Notification::factory()
                ->count($count)
                ->forFamilyMember($familyMember->family_member_id)
                ->create();
                
            $notificationCount += $count;
        }
        
        \Log::info("Created {$notificationCount} notifications for family members");
        
        // Generate notifications for ALL COSE staff
        $staffMembers = User::where('role_id', '<=', 3)->get();
        $notificationCount = 0;
        
        $notificationTypes = [
            // Admin notifications (role_id = 1)
            1 => [
                'System Update' => 'The system has been updated with new features.',
                'Internal Appointment Created' => 'A new internal appointment has been created.',
                'Internal Appointment Updated' => 'An internal appointment has been updated.',
                'Internal Appointment Canceled' => 'An internal appointment has been canceled.',
                'Internal Appointment Reminder' => 'Reminder: You have an upcoming internal appointment.',
                'Security Alert' => 'A new security patch has been applied.',
                'New User Registration' => 'A new user has registered in the system.',
                'Data Backup Complete' => 'Automatic data backup has completed successfully.',
                'Performance Report' => 'Monthly performance report is now available.'
            ],
            // Care Manager notifications (role_id = 2)
            2 => [
                'New Case Assigned' => 'You have been assigned a new case to manage.',
                'Internal Appointment Created' => 'A new internal appointment has been created.',
                'Internal Appointment Updated' => 'An internal appointment has been updated.',
                'Internal Appointment Canceled' => 'An internal appointment has been canceled.',
                'Internal Appointment Reminder' => 'Reminder: You have an upcoming internal appointment.',
                'Care Plan Review' => 'A care plan is due for review this week.',
                'Staff Schedule Update' => 'There are changes to the staff schedule.',
                'Patient Status Alert' => 'A patient status has been updated.',
                'Weekly Report Due' => 'Your weekly report is due in 2 days.'
            ],
            // Care Worker notifications (role_id = 3)
            3 => [
                'Visit Reminder' => 'You have a scheduled visit tomorrow.',
                'Medication Update' => 'Medication schedule has been updated for a patient.',
                'Training Available' => 'New training modules are available for you.',
                'Shift Change Request' => 'A shift change has been requested.',
                'Internal Appointment Created' => 'A new internal appointment has been created.',
                'Internal Appointment Updated' => 'An internal appointment has been updated.',
                'Internal Appointment Canceled' => 'An internal appointment has been canceled.',
                'Internal Appointment Reminder' => 'Reminder: You have an upcoming internal appointment.',
                'Documentation Reminder' => 'Please complete your visit documentation.'
            ]
        ];
        
        // For each staff member, create role-specific notifications
        foreach ($staffMembers as $staff) {
            $roleSpecificMessages = $notificationTypes[$staff->role_id] ?? $notificationTypes[1];
            $count = rand(3, 7); // Create 3-7 notifications per user
            
            // Create some read and some unread notifications
            for ($i = 0; $i < $count; $i++) {
                $title = array_rand($roleSpecificMessages);
                $message = $roleSpecificMessages[$title];
                
                Notification::create([
                    'user_id' => $staff->id,
                    'user_type' => 'cose_staff',
                    'message_title' => $title,
                    'message' => $message,
                    'date_created' => now()->subHours(rand(1, 72)), // Random time within last 3 days
                    'is_read' => rand(0, 100) < 30, // 30% chance of being read
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $notificationCount++;
            }
        }
        
        \Log::info("Created {$notificationCount} notifications for staff members");
    }

    /**
     * Generate conversations and messages between users following role hierarchy rules
     */
    private function generateConversations()
    {
        // Get users by role - with our new reduced counts
        $admins = User::where('role_id', 1)->get();
        $careManagers = User::where('role_id', 2)->get();
        $careWorkers = User::where('role_id', 3)->get();
        
        // Get a subset of beneficiaries and family members for conversations
        // We don't need all 100 beneficiaries having conversations
        $beneficiaries = Beneficiary::inRandomOrder()->take(20)->get();
        $familyMembers = FamilyMember::inRandomOrder()->take(15)->get();
        
        // ================ PRIVATE CONVERSATIONS ================
        $conversationCount = 0;
        
        // 1. Create conversations for Admins (can only talk to Care Managers)
        foreach ($admins as $admin) {
            // Create conversations with all Care Managers (since we only have 2)
            foreach ($careManagers as $careManager) {
                $this->createPrivateConversation($admin, $careManager);
                $conversationCount++;
            }
        }
        
        // 2. Create conversations between Care Managers and Care Workers (distributed by area)
        foreach ($careManagers as $careManager) {
            // Get care workers assigned to this care manager
            $assignedWorkers = $careWorkers->where('assigned_care_manager_id', $careManager->id);
            
            // Create conversation with each assigned worker
            foreach ($assignedWorkers as $worker) {
                $this->createPrivateConversation($careManager, $worker);
                $conversationCount++;
            }
            
            // Also create conversations with some beneficiaries in their area
            $areaName = stripos($careManager->name, 'San Roque') !== false ? 'San Roque' : 'Mondragon';
            $areaBeneficiaries = $beneficiaries->filter(function ($beneficiary) use ($areaName) {
                return stripos($beneficiary->address, $areaName) !== false;
            })->take(5);
            
            foreach ($areaBeneficiaries as $beneficiary) {
                $this->createPrivateConversation($careManager, $beneficiary, 'beneficiary');
                $conversationCount++;
            }
        }
        
        // 3. Create conversations for Care Workers with their assigned beneficiaries
        $careWorkersCount = count($careWorkers);
        $beneficiariesPerWorker = ceil(count($beneficiaries) / $careWorkersCount);
        
        // Distribute beneficiaries among care workers for conversations
        for ($i = 0; $i < $careWorkersCount; $i++) {
            $start = $i * $beneficiariesPerWorker;
            $end = min(($i + 1) * $beneficiariesPerWorker, count($beneficiaries));
            
            for ($j = $start; $j < $end; $j++) {
                if (isset($beneficiaries[$j])) {
                    $this->createPrivateConversation($careWorkers[$i], $beneficiaries[$j], 'beneficiary');
                    $conversationCount++;
                }
            }
            
            // Also create 1-2 conversations with family members
            $workerFamilyMembers = $familyMembers->random(min(2, count($familyMembers)));
            foreach ($workerFamilyMembers as $familyMember) {
                $this->createPrivateConversation($careWorkers[$i], $familyMember, 'family_member');
                $conversationCount++;
            }
        }
        
        \Log::info("Created {$conversationCount} private conversations");
        
        // ================ GROUP CONVERSATIONS ================
        $groupCount = 0;
        
        // 1. Create group chats for entire admin team (with care managers)
        $allAdmins = $admins->all();
        $this->createGroupChat($allAdmins[0], array_merge(array_slice($allAdmins, 1), $careManagers->all()));
        $groupCount++;
        
        // 2. Create area-specific group chats for each care manager with their care workers
        foreach ($careManagers as $careManager) {
            $assignedWorkers = $careWorkers->where('assigned_care_manager_id', $careManager->id)->all();
            if (count($assignedWorkers) > 0) {
                $this->createGroupChat($careManager, $assignedWorkers);
                $groupCount++;
            }
        }
        
        // 3. Create a few mixed groups with care workers, beneficiaries and family members
        foreach ($careWorkers as $i => $careWorker) {
            // Only create for some care workers (40%)
            if (rand(1, 10) <= 4) {
                // Get a beneficiary and family member
                $beneficiary = $beneficiaries->random();
                $familyMember = $familyMembers->random();
                
                $participants = [
                    ['object' => $beneficiary, 'type' => 'beneficiary'],
                    ['object' => $familyMember, 'type' => 'family_member']
                ];
                
                // Optionally add care manager (50% chance)
                if (rand(1, 2) == 1) {
                    $careManager = User::find($careWorker->assigned_care_manager_id);
                    if ($careManager) {
                        $participants[] = ['object' => $careManager, 'type' => 'cose_staff'];
                    }
                }
                
                $this->createGroupChatWithMixedParticipants($careWorker, $participants);
                $groupCount++;
            }
        }
        
        \Log::info("Created {$groupCount} group conversations");
        
        // Log overall messaging stats
        $totalConversations = Conversation::count();
        $totalMessages = Message::count();
        $totalAttachments = MessageAttachment::count();
        
        \Log::info("Total: {$totalConversations} conversations with {$totalMessages} messages and {$totalAttachments} attachments");
    }

    /**
     * Check if a conversation already exists between two participants
     */
    private function conversationExistsBetween($userId1, $userType1, $userId2, $userType2)
    {
        // Get conversations where user1 is a participant
        $user1ConversationIds = ConversationParticipant::where('participant_id', $userId1)
            ->where('participant_type', $userType1)
            ->pluck('conversation_id');
        
        // Find if any of those conversations have user2 as participant
        return ConversationParticipant::whereIn('conversation_id', $user1ConversationIds)
            ->where('participant_id', $userId2)
            ->where('participant_type', $userType2)
            ->exists();
    }

    /**
     * Create a private conversation between two users with messages
     */
    private function createPrivateConversation($user1, $user2, $user2Type = 'cose_staff')
    {
        // Skip if conversation already exists
        if ($user2Type === 'cose_staff') {
            if ($this->conversationExistsBetween($user1->id, 'cose_staff', $user2->id, 'cose_staff')) {
                return null;
            }
        } else {
            $user2Id = $user2Type === 'beneficiary' ? $user2->beneficiary_id : $user2->family_member_id;
            if ($this->conversationExistsBetween($user1->id, 'cose_staff', $user2Id, $user2Type)) {
                return null;
            }
        }
        
        // Create a private conversation
        $conversation = Conversation::factory()->privateChat()->create();
        
        // Add the first user as a participant
        ConversationParticipant::create([
            'conversation_id' => $conversation->conversation_id,
            'participant_id' => $user1->id,
            'participant_type' => 'cose_staff',
            'joined_at' => now()->subDays(rand(1, 30)),
        ]);
        
        // Add the second user as a participant
        ConversationParticipant::create([
            'conversation_id' => $conversation->conversation_id,
            'participant_id' => ($user2Type === 'cose_staff') ? $user2->id : $user2->{$user2Type === 'beneficiary' ? 'beneficiary_id' : 'family_member_id'},
            'participant_type' => $user2Type,
            'joined_at' => now()->subDays(rand(1, 30)),
        ]);
        
        // Create messages in this conversation from both participants (2-8 messages)
        $messageCount = rand(2, 8);
        
        $lastMessage = null;
        for ($j = 0; $j < $messageCount; $j++) {
            // Alternate between the two participants
            if ($j % 2 == 0) {
                // First user sends message
                $senderId = $user1->id;
                $senderType = 'cose_staff';
            } else {
                // Second user sends message
                $senderId = ($user2Type === 'cose_staff') ? $user2->id : $user2->{$user2Type === 'beneficiary' ? 'beneficiary_id' : 'family_member_id'};
                $senderType = $user2Type;
            }
            
            $isUnsent = (rand(1, 20) === 1); // 5% chance of being unsent
            $message = Message::create([
                'conversation_id' => $conversation->conversation_id,
                'sender_id' => $senderId,
                'sender_type' => $senderType,
                'content' => \Faker\Factory::create()->sentence(rand(3, 15)),
                'is_unsent' => $isUnsent,
                'message_timestamp' => now()->subDays(5)->addMinutes($j * 30),
            ]);
            
            $lastMessage = $message;
            
            // Randomly add attachments and read statuses
            $this->addAttachmentAndReadStatuses($message, [
                ['id' => $user1->id, 'type' => 'cose_staff'],
                ['id' => ($user2Type === 'cose_staff') ? $user2->id : $user2->{$user2Type === 'beneficiary' ? 'beneficiary_id' : 'family_member_id'}, 'type' => $user2Type]
            ]);
        }
        
        // Update the conversation with the last message ID
        if ($lastMessage) {
            $conversation->last_message_id = $lastMessage->message_id;
            $conversation->save();
        }
        
        return $conversation;
    }

    /**
     * Create a group chat with staff users of the same type
     */
    private function createGroupChat($creator, $participants)
    {
        $areaName = '';
        if (is_object($creator) && property_exists($creator, 'name')) {
            if (stripos($creator->name, 'San Roque') !== false) {
                $areaName = 'San Roque';
            } elseif (stripos($creator->name, 'Mondragon') !== false) {
                $areaName = 'Mondragon';
            }
        }
        
        // Create a realistic group chat name
        if (!empty($areaName)) {
            $name = $areaName . ' ' . $this->getGroupChatName();
        } else {
            $name = 'COSE ' . $this->getGroupChatName();
        }
        
        // Create a group chat
        $groupChat = Conversation::factory()->groupChat()->create([
            'name' => $name,
        ]);
        
        // Add the creator as a participant
        ConversationParticipant::create([
            'conversation_id' => $groupChat->conversation_id,
            'participant_id' => $creator->id,
            'participant_type' => 'cose_staff',
            'joined_at' => now()->subDays(rand(1, 30)),
        ]);
        
        // Add other participants
        foreach ($participants as $participant) {
            ConversationParticipant::create([
                'conversation_id' => $groupChat->conversation_id,
                'participant_id' => $participant->id,
                'participant_type' => 'cose_staff',
                'joined_at' => now()->subDays(rand(1, 30)),
            ]);
        }
        
        // Convert collection to array and merge with creator for messages
        $allParticipants = [$creator];
        if ($participants instanceof \Illuminate\Database\Eloquent\Collection) {
            $participantsArray = $participants->all(); // Convert Collection to array
        } else {
            $participantsArray = $participants; // Already an array
        }
        
        // Generate messages
        $this->generateGroupMessages($groupChat, array_merge($allParticipants, $participantsArray), []);
        
        return $groupChat;
    }
    
    /**
     * Get a realistic group chat name
     */
    private function getGroupChatName()
    {
        $prefixes = ['Team', 'Staff', 'Care', 'Community', 'Support'];
        $purposes = ['Coordination', 'Updates', 'Team', 'Planning', 'Discussion', 'Communication'];
        
        return $prefixes[array_rand($prefixes)] . ' ' . $purposes[array_rand($purposes)];
    }

    /**
     * Create a group chat with mixed participant types
     */
    private function createGroupChatWithMixedParticipants($creator, $participants)
    {
        // Get the beneficiary name if available
        $beneficiaryName = '';
        foreach ($participants as $participant) {
            if ($participant['type'] === 'beneficiary' && property_exists($participant['object'], 'name')) {
                $beneficiaryName = $participant['object']->name;
                break;
            }
        }
        
        // Create a realistic group name
        if (!empty($beneficiaryName)) {
            $firstName = explode(' ', $beneficiaryName)[0];
            $name = $firstName . "'s Care Team";
        } else {
            $name = "Beneficiary Support Group";
        }
        
        // Create a group chat
        $groupChat = Conversation::factory()->groupChat()->create([
            'name' => $name,
        ]);
        
        // Add the creator as a participant
        ConversationParticipant::create([
            'conversation_id' => $groupChat->conversation_id,
            'participant_id' => $creator->id,
            'participant_type' => 'cose_staff',
            'joined_at' => now()->subDays(rand(1, 30)),
        ]);
        
        // Convert participants to a format we can use
        $allParticipants = [
            ['object' => $creator, 'type' => 'cose_staff']
        ];
        
        // Add other participants
        foreach ($participants as $participant) {
            $participantId = ($participant['type'] === 'cose_staff') 
                ? $participant['object']->id 
                : ($participant['type'] === 'beneficiary' 
                    ? $participant['object']->beneficiary_id 
                    : $participant['object']->family_member_id);
            
            ConversationParticipant::create([
                'conversation_id' => $groupChat->conversation_id,
                'participant_id' => $participantId,
                'participant_type' => $participant['type'],
                'joined_at' => now()->subDays(rand(1, 30)),
            ]);
            
            $allParticipants[] = $participant;
        }
        
        // Generate messages
        $this->generateGroupMessages($groupChat, [], $allParticipants);
        
        return $groupChat;
    }

    /**
     * Generate messages for a group chat
     */
    private function generateGroupMessages($groupChat, $staffParticipants, $mixedParticipants)
    {
        // Determine which participants array to use
        $useParticipants = !empty($mixedParticipants) ? $mixedParticipants : $staffParticipants;
        
        // Generate 3-10 messages in the group chat from various participants
        $messageCount = rand(3, 10);
        
        $lastMessage = null;
        for ($j = 0; $j < $messageCount; $j++) {
            // Choose a random participant to send the message
            $randomIndex = array_rand($useParticipants);
            $randomParticipant = $useParticipants[$randomIndex];
            
            // Get the sender ID and type
            if (!empty($mixedParticipants)) {
                $senderId = ($randomParticipant['type'] === 'cose_staff') 
                    ? $randomParticipant['object']->id 
                    : ($randomParticipant['type'] === 'beneficiary' 
                        ? $randomParticipant['object']->beneficiary_id 
                        : $randomParticipant['object']->family_member_id);
                $senderType = $randomParticipant['type'];
            } else {
                $senderId = $randomParticipant->id;
                $senderType = 'cose_staff';
            }
            
            $message = Message::create([
                'conversation_id' => $groupChat->conversation_id,
                'sender_id' => $senderId,
                'sender_type' => $senderType,
                'content' => \Faker\Factory::create()->sentence(rand(3, 15)),
                'message_timestamp' => now()->subDays(5)->addMinutes($j * 30),
            ]);
            
            $lastMessage = $message;
            
            // Create a list of all participants for read statuses
            $allParticipantIds = [];
            if (!empty($mixedParticipants)) {
                foreach ($mixedParticipants as $p) {
                    $pId = ($p['type'] === 'cose_staff') 
                        ? $p['object']->id 
                        : ($p['type'] === 'beneficiary' 
                            ? $p['object']->beneficiary_id 
                            : $p['object']->family_member_id);
                    
                    $allParticipantIds[] = ['id' => $pId, 'type' => $p['type']];
                }
            } else {
                foreach ($staffParticipants as $p) {
                    $allParticipantIds[] = ['id' => $p->id, 'type' => 'cose_staff'];
                }
            }
            
            // Add attachments and read statuses
            $this->addAttachmentAndReadStatuses($message, $allParticipantIds);
        }
        
        // Update the conversation with the last message ID
        if ($lastMessage) {
            $groupChat->last_message_id = $lastMessage->message_id;
            $groupChat->save();
        }
    }

    /**
     * Add attachment and read statuses to a message
     */
    private function addAttachmentAndReadStatuses($message, $participants)
    {
        // Randomly add attachments to some messages
        if (rand(1, 10) == 1) { // 10% chance - reduced from original
            $isImage = rand(0, 1) == 1;
            
            if ($isImage) {
                $fileName = \Faker\Factory::create()->word . '.jpg';
                $filePath = 'message_attachments/images/' . $fileName;
                $fileType = 'image/jpeg';
            } else {
                $fileExtension = ['pdf', 'doc', 'docx'][rand(0, 2)];
                $fileName = \Faker\Factory::create()->word . '.' . $fileExtension;
                $filePath = 'message_attachments/documents/' . $fileName;
                
                if ($fileExtension === 'pdf') {
                    $fileType = 'application/pdf';
                } elseif ($fileExtension === 'doc') {
                    $fileType = 'application/msword';
                } else {
                    $fileType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                }
            }
            
            MessageAttachment::create([
                'message_id' => $message->message_id,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $fileType,
                'file_size' => rand(10000, 5000000), // 10KB to 5MB
                'is_image' => $isImage,
            ]);
        }
        
        // Mark messages as read by recipients
        foreach ($participants as $participant) {
            // Skip the sender (they've already seen their own message)
            if ($participant['id'] == $message->sender_id && $participant['type'] == $message->sender_type) {
                continue;
            }
            
            // 70% chance this participant has read the message
            if (rand(1, 10) <= 7) {
                MessageReadStatus::create([
                    'message_id' => $message->message_id,
                    'reader_id' => $participant['id'],
                    'reader_type' => $participant['type'],
                    'read_at' => now()->subMinutes(rand(1, 60)),
                ]);
            }
        }
    }

    /**
     * Generate scheduling data for the system
     */
    private function generateSchedulingData($careWorkers, $allBeneficiaries)
    {
        \Log::info("Generating scheduling data...");
        
        // Create appointment types
        $this->createAppointmentTypes();
        
        // Generate internal appointments
        $this->generateInternalAppointments();
        
        // Generate care worker visitations
        $this->generateCareWorkerVisitations($careWorkers, $allBeneficiaries);
        
        // Generate medication schedules
        $this->generateMedicationSchedules();
    }

    private function createAppointmentTypes()
    {
        // Check if appointment types already exist (should exist after running migrations)
        if (AppointmentType::count() > 0) {
            \Log::info("Using " . AppointmentType::count() . " existing appointment types");
            return;
        }

        \Log::info("No appointment types found. Creating default types...");
        
        $types = [
            ['type_name' => 'Skills Training', 'color_code' => '#4e73df', 'description' => 'Staff skills development and training sessions'],
            ['type_name' => 'Feedback Session', 'color_code' => '#1cc88a', 'description' => 'Performance feedback and evaluation meetings'],
            ['type_name' => 'Council Meeting', 'color_code' => '#36b9cc', 'description' => 'Regular council and committee meetings'],
            ['type_name' => 'Health Protocols', 'color_code' => '#f6c23e', 'description' => 'Health and safety protocol discussions'],
            ['type_name' => 'Liga Meetings', 'color_code' => '#e74a3b', 'description' => 'Liga ng mga Barangay meetings and coordination'],
            ['type_name' => 'Referrals Discussion', 'color_code' => '#6f42c1', 'description' => 'Discussing beneficiary referrals and services'],
            ['type_name' => 'Assessment Review', 'color_code' => '#fd7e14', 'description' => 'Reviewing beneficiary assessments and reports'],
            ['type_name' => 'Care Plan Review', 'color_code' => '#20c997', 'description' => 'Reviewing and updating care plans'],
            ['type_name' => 'Team Building', 'color_code' => '#5a5c69', 'description' => 'Staff team-building activities'],
            ['type_name' => 'Mentoring Session', 'color_code' => '#858796', 'description' => 'One-on-one mentoring sessions'],
            ['type_name' => 'Other', 'color_code' => '#a435f0', 'description' => 'Other meeting types not categorized above'],
        ];

        foreach ($types as $type) {
            AppointmentType::firstOrCreate(
                ['type_name' => $type['type_name']],
                $type
            );
        }
        
        \Log::info("Created appointment types: " . AppointmentType::count());
    }

    /**
     * Generate internal appointments for staff
     */
    private function generateInternalAppointments()
    {
        \Log::info("Generating internal appointments...");
        
        $appointmentTypes = AppointmentType::all();
        $staffUsers = User::where('role_id', '<=', 3)->get();
        
        if ($appointmentTypes->isEmpty()) {
            \Log::warning("No appointment types found. Cannot generate internal appointments.");
            return;
        }
        
        // Reduced number of appointments compared to the original seeder
        // Create past appointments
        $this->createInternalAppointmentBatch(20, $appointmentTypes, $staffUsers, true);
        
        // Create future appointments
        $this->createInternalAppointmentBatch(30, $appointmentTypes, $staffUsers, false);
        
        // Create recurring appointments - limit daily recurring patterns
        $this->createRecurringInternalAppointments(8, $appointmentTypes, $staffUsers);
        
        \Log::info("Generated internal appointments: " . Appointment::count());
    }
    
    /**
     * Create a batch of internal appointments
     * 
     * @param int $count Number of appointments to create
     * @param Collection $appointmentTypes Available appointment types
     * @param Collection $staffUsers Available staff users
     * @param bool $isPast Whether to create past appointments
     */
    private function createInternalAppointmentBatch($count, $appointmentTypes, $staffUsers, $isPast = false)
    {
        // Track used dates to avoid too many appointments on the same day
        $usedDates = [];
        
        for ($i = 0; $i < $count; $i++) {
            $appointmentType = $appointmentTypes->random();
            $title = $this->getRealisticAppointmentTitle($appointmentType->type_name);
            
            // For past appointments, set date in the past and status as completed
            if ($isPast) {
                // Create a date that's not already heavily used
                do {
                    $date = $this->faker->dateTimeBetween('-3 months', '-1 day')->format('Y-m-d');
                } while (isset($usedDates[$date]) && $usedDates[$date] >= 2); // Max 2 appointments per day
                
                // Track usage
                $usedDates[$date] = isset($usedDates[$date]) ? $usedDates[$date] + 1 : 1;
                
                // 80% completed, 20% canceled
                $status = (rand(1, 100) <= 80) ? 'completed' : 'canceled';
            } else {
                // Create a date that's not already heavily used
                do {
                    $date = $this->faker->dateTimeBetween('+1 day', '+2 months')->format('Y-m-d');
                } while (isset($usedDates[$date]) && $usedDates[$date] >= 2); // Max 2 appointments per day
                
                // Track usage
                $usedDates[$date] = isset($usedDates[$date]) ? $usedDates[$date] + 1 : 1;
                
                $status = 'scheduled';
            }
            
            // Realistic office hours (8:30 AM to 4:30 PM)
            $startTime = $this->faker->dateTimeBetween('08:30', '16:00')->format('H:i:s');
            $endTime = Carbon::parse($startTime)->addMinutes(rand(30, 120))->format('H:i:s');
            $isFlexibleTime = $this->faker->boolean(10); // Only 10% chance of being flexible time
            
            // Choose organizer (admins and care managers organize appointments)
            $organizer = $staffUsers->where('role_id', '<=', 2)->random();
            
            // Create the appointment
            $appointment = Appointment::create([
                'appointment_type_id' => $appointmentType->appointment_type_id,
                'title' => $title,
                'description' => $this->faker->paragraph(),
                'date' => $date,
                'start_time' => $isFlexibleTime ? null : $startTime,
                'end_time' => $isFlexibleTime ? null : $endTime,
                'is_flexible_time' => $isFlexibleTime,
                'meeting_location' => $this->getRealisticMeetingLocation(),
                'status' => $status,
                'notes' => $this->faker->optional(70)->paragraph(),
                'created_by' => $organizer->id,
                'updated_by' => null
            ]);
            
            // Create a single occurrence for non-recurring appointment
            AppointmentOccurrence::create([
                'appointment_id' => $appointment->appointment_id,
                'occurrence_date' => $appointment->date,
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'status' => $appointment->status
            ]);
            
            // Determine the participant count based on appointment type
            $participantCount = $this->getParticipantCountForAppointmentType($appointmentType->type_name);
            
            // Ensure we don't try to add more participants than available staff
            $participantCount = min($participantCount, $staffUsers->count());
            
            // Add the organizer as the first participant
            AppointmentParticipant::create([
                'appointment_id' => $appointment->appointment_id,
                'participant_id' => $organizer->id,
                'participant_type' => 'cose_user',
                'is_organizer' => true
            ]);
            
            // Add other participants randomly
            $availableParticipants = $staffUsers->reject(function($user) use ($organizer) {
                return $user->id === $organizer->id;
            })->shuffle();
            
            // Add participants based on the calculated count, limiting to available participants
            for ($j = 0; $j < min($participantCount - 1, count($availableParticipants)); $j++) {
                AppointmentParticipant::create([
                    'appointment_id' => $appointment->appointment_id,
                    'participant_id' => $availableParticipants[$j]->id,
                    'participant_type' => 'cose_user',
                    'is_organizer' => false
                ]);
            }
        }
    }
    
    /**
     * Create recurring internal appointments
     */
    private function createRecurringInternalAppointments($count, $appointmentTypes, $staffUsers)
    {
        // Track used days to avoid too many recurring appointments on the same day
        $usedDays = [];
        
        for ($i = 0; $i < $count; $i++) {
            // Select a random appointment type
            $appointmentType = $appointmentTypes->random();
            
            // Generate a realistic title based on type
            $title = $this->getRealisticAppointmentTitle($appointmentType->type_name);
            
            // Choose pattern type - mostly weekly, some monthly
            $patternType = rand(1, 10) <= 8 ? 'weekly' : 'monthly';
            
            // Set start date in future
            $startDate = Carbon::now()->addDays(rand(1, 14))->format('Y-m-d');
            
            // Set an end date 3-6 months in the future
            $endDate = Carbon::parse($startDate)->addMonths(rand(3, 6))->format('Y-m-d');
            
            // For weekly patterns, select days of week as a string
            $dayOfWeek = null;
            if ($patternType === 'weekly') {
                // Avoid creating too many recurring appointments on the same day
                do {
                    $dayOfWeek = (string)rand(1, 5); // Monday to Friday (1-5) as a string
                } while (isset($usedDays[$dayOfWeek]) && $usedDays[$dayOfWeek] >= 2);
                
                // Track usage
                $usedDays[$dayOfWeek] = isset($usedDays[$dayOfWeek]) ? $usedDays[$dayOfWeek] + 1 : 1;
            } else {
                // For monthly patterns, set day of month as a string in the day_of_week field
                $dayOfWeek = (string)rand(1, 28); // Day of month (1-28) as a string
            }
            
            // Realistic office hours (9 AM to 3 PM for recurring meetings)
            $startTime = $this->faker->dateTimeBetween('09:00', '15:00')->format('H:i:s');
            $endTime = Carbon::parse($startTime)->addMinutes(rand(30, 90))->format('H:i:s');
            
            // Staff meetings are rarely flexible time
            $isFlexibleTime = $this->faker->boolean(5); // Only 5% chance of being flexible time
            
            // Choose organizer (admins and care managers organize appointments)
            $organizer = $staffUsers->where('role_id', '<=', 2)->random();
            
            // Create the recurring appointment
            $appointment = Appointment::create([
                'appointment_type_id' => $appointmentType->appointment_type_id,
                'title' => $title,
                'description' => $this->faker->paragraph(),
                'date' => $startDate,
                'start_time' => $isFlexibleTime ? null : $startTime,
                'end_time' => $isFlexibleTime ? null : $endTime,
                'is_flexible_time' => $isFlexibleTime,
                'meeting_location' => $this->getRealisticMeetingLocation(),
                'status' => 'scheduled',
                'notes' => $this->faker->optional(50)->paragraph(),
                'created_by' => $organizer->id,
                'updated_by' => null
            ]);
            
            // Create the recurring pattern
            $recurringPattern = RecurringPattern::create([
                'appointment_id' => $appointment->appointment_id,
                'pattern_type' => $patternType,
                'day_of_week' => $dayOfWeek, // Use day_of_week for both weekly and monthly patterns
                'recurrence_end' => $endDate
            ]);
            
            // Generate occurrences based on the pattern
            $currentDate = Carbon::parse($startDate);
            $endDateTime = Carbon::parse($endDate);
            
            while ($currentDate->lte($endDateTime)) {
                if ($patternType === 'weekly') {
                    // Skip to the next occurrence if the day of week doesn't match
                    if ($currentDate->dayOfWeek != intval($dayOfWeek)) {
                        $currentDate->addDay();
                        continue;
                    }
                    
                    // Create occurrence for this date
                    AppointmentOccurrence::create([
                        'appointment_id' => $appointment->appointment_id,
                        'occurrence_date' => $currentDate->format('Y-m-d'),
                        'start_time' => $appointment->start_time,
                        'end_time' => $appointment->end_time,
                        'status' => $currentDate->isPast() ? (rand(1, 10) <= 8 ? 'completed' : (rand(1, 10) <= 5 ? 'canceled' : 'scheduled')) : 'scheduled'
                    ]);
                    
                    // Move to the next week
                    $currentDate->addWeek();
                } else {
                    // Monthly pattern - check if day of month matches
                    if ($currentDate->day != intval($dayOfWeek)) {
                        // Move to the next day
                        $currentDate->addDay();
                        continue;
                    }
                    
                    // Create occurrence for this date
                    AppointmentOccurrence::create([
                        'appointment_id' => $appointment->appointment_id,
                        'occurrence_date' => $currentDate->format('Y-m-d'),
                        'start_time' => $appointment->start_time,
                        'end_time' => $appointment->end_time,
                        'status' => $currentDate->isPast() ? (rand(1, 10) <= 8 ? 'completed' : (rand(1, 10) <= 5 ? 'canceled' : 'scheduled')) : 'scheduled'
                    ]);
                    
                    // Move to the next month
                    $currentDate->addMonth();
                }
            }
            
            // Determine the participant count based on appointment type
            $participantCount = $this->getParticipantCountForAppointmentType($appointmentType->type_name);
            
            // Add the organizer as the first participant
            AppointmentParticipant::create([
                'appointment_id' => $appointment->appointment_id,
                'participant_id' => $organizer->id,
                'participant_type' => 'cose_user',
                'is_organizer' => true
            ]);
            
            // Add other participants randomly
            $availableParticipants = $staffUsers->reject(function($user) use ($organizer) {
                return $user->id === $organizer->id;
            })->shuffle();
            
            // Add participants based on the calculated count, limiting to available participants
            for ($j = 0; $j < min($participantCount - 1, count($availableParticipants)); $j++) {
                AppointmentParticipant::create([
                    'appointment_id' => $appointment->appointment_id,
                    'participant_id' => $availableParticipants[$j]->id,
                    'participant_type' => 'cose_user',
                    'is_organizer' => false
                ]);
            }
        }
    }

    /**
     * Generate care worker visitations for beneficiaries
     */
    private function generateCareWorkerVisitations($careWorkers, $beneficiaries)
    {
        \Log::info("Generating care worker visitations...");
        
        // Convert the beneficiaries array to a collection
        $beneficiariesCollection = collect($beneficiaries);
        
        // Keep track of how many visitations we create
        $totalVisitations = 0;
        $recurringCount = 0;
        
        // For each care worker, create visitations
        foreach ($careWorkers as $careWorker) {
            // Determine which beneficiaries this care worker can visit based on location
            $isSanRoqueWorker = stripos($careWorker->name, 'San Roque') !== false;
            $workerArea = $isSanRoqueWorker ? 'San Roque' : 'Mondragon';
            
            // Filter beneficiaries by the worker's area - NOW USING THE COLLECTION
            $assignedBeneficiaries = $beneficiariesCollection->filter(function($beneficiary) use ($workerArea) {
                return stripos($beneficiary->street_address, $workerArea) !== false;
            });
            
            // Select a subset of beneficiaries for this care worker (5-10)
            $selectedBeneficiaries = $assignedBeneficiaries->random(min(rand(5, 10), $assignedBeneficiaries->count()));
            
            // For each selected beneficiary, create a weekly recurring routine care visit
            foreach ($selectedBeneficiaries as $beneficiary) {
                // 1. Create a weekly recurring routine care visit
                $this->createRecurringVisitation($careWorker, $beneficiary, 'routine_care');
                $recurringCount++;
                
                // 2. Create 0-2 emergency visitations for this beneficiary (30% chance)
                if (rand(1, 10) <= 3) {
                    $emergencyCount = rand(1, 2);
                    for ($i = 0; $i < $emergencyCount; $i++) {
                        $this->createSingleVisitation($careWorker, $beneficiary, 'emergency');
                        $totalVisitations++;
                    }
                }
                
                // 3. Create 0-3 service request visitations (50% chance)
                if (rand(1, 10) <= 5) {
                    $serviceCount = rand(1, 3);
                    for ($i = 0; $i < $serviceCount; $i++) {
                        $this->createSingleVisitation($careWorker, $beneficiary, 'service_request');
                        $totalVisitations++;
                    }
                }
            }
        }
        
        \Log::info("Generated {$recurringCount} recurring routine care visitations and {$totalVisitations} individual visitations");
    }
    
    /**
     * Create a recurring visitation (for routine care)
     */
    private function createRecurringVisitation($careWorker, $beneficiary, $type)
    {
        // Fix the visit type to match the enum values in the database
        $visitType = 'routine_care_visit'; // Changed from routine_care
        
        // Pick a start date in the past (1-3 months ago)
        $startDate = Carbon::now()->subMonths(rand(1, 3))->format('Y-m-d');
        
        // Set an end date 4-8 months in the future
        $endDate = Carbon::now()->addMonths(rand(4, 8))->format('Y-m-d');
        
        // Choose a random day of week (1-7)
        $dayOfWeek = rand(1, 7);
        
        // Create the visitation with all required fields
        $visitation = Visitation::create([
            'care_worker_id' => $careWorker->id,
            'beneficiary_id' => $beneficiary->beneficiary_id,
            'visit_type' => $visitType,
            'visitation_date' => $startDate,
            'start_time' => null,
            'end_time' => null,
            'is_flexible_time' => true,
            'status' => 'scheduled',
            'notes' => $this->faker->optional(80)->sentence(5),
            'date_assigned' => Carbon::now()->subDays(rand(5, 30))->format('Y-m-d'), // Add required field
            'assigned_by' => $careWorker->assigned_care_manager_id ?? 2, // Care manager assigns (default to ID 2 if null)
        ]);
        
        // Create recurring pattern
        $recurringPattern = RecurringPattern::create([
            'visitation_id' => $visitation->visitation_id,
            'pattern_type' => 'weekly',
            'day_of_week' => $dayOfWeek,
            'recurrence_end' => $endDate
        ]);
        
        // Generate occurrences based on the pattern
        $currentDate = Carbon::parse($startDate);
        $endDateTime = Carbon::parse($endDate);
        
        while ($currentDate->lte($endDateTime)) {
            // Skip to the next occurrence if the day of week doesn't match
            if ($currentDate->dayOfWeek !== $dayOfWeek) {
                $currentDate->addDay();
                continue;
            }
            
            // For past occurrences, most are completed, some canceled
            $status = 'scheduled';
            if ($currentDate->isPast()) {
                $status = (rand(1, 10) <= 8) ? 'completed' : (rand(1, 10) <= 5 ? 'canceled' : 'scheduled');
            }
            
            // Create occurrence for this date
            VisitationOccurrence::create([
                'visitation_id' => $visitation->visitation_id,
                'occurrence_date' => $currentDate->format('Y-m-d'),
                'start_time' => null,
                'end_time' => null,
                'status' => $status,
                'notes' => $status == 'completed' ? $this->faker->optional(70)->paragraph() : null,
            ]);
            
            // Move to the next week
            $currentDate->addWeek();
        }
    }
    
    /**
     * Create a single non-recurring visitation
     */
    private function createSingleVisitation($careWorker, $beneficiary, $type)
    {
        // Fix visit types to match the enum in the database
        if ($type === 'emergency') {
            $visitType = 'emergency_visit';
        } else if ($type === 'service_request') {
            $visitType = 'service_request';
        } else {
            $visitType = 'routine_care_visit';
        }
        
        // Determine if this is a past or future visitation
        $isPast = (rand(1, 10) <= 7); // 70% chance of being in the past
        
        if ($isPast) {
            // Create a past visitation
            $date = Carbon::now()->subDays(rand(1, 60))->format('Y-m-d');
            $status = (rand(1, 10) <= 8) ? 'completed' : 'canceled'; // 80% completed, 20% canceled
        } else {
            // Create a future visitation
            $date = Carbon::now()->addDays(rand(1, 30))->format('Y-m-d');
            $status = 'scheduled';
        }
        
        // Set times based on type
        $isFlexibleTime = false;
        $startTime = null;
        $endTime = null;
        
        if ($visitType === 'emergency_visit') {
            // Emergency visits usually have specific times
            $startTime = $this->faker->dateTimeBetween('08:00', '17:00')->format('H:i:s');
            $endTime = Carbon::parse($startTime)->addMinutes(rand(30, 90))->format('H:i:s');
        } else if ($visitType === 'service_request') {
            // Service requests may have specific times or be flexible
            $isFlexibleTime = (rand(1, 10) <= 4); // 40% chance of flexible time
            if (!$isFlexibleTime) {
                $startTime = $this->faker->dateTimeBetween('08:00', '17:00')->format('H:i:s');
                $endTime = Carbon::parse($startTime)->addMinutes(rand(30, 120))->format('H:i:s');
            }
        }
        
        // Create the visitation with all required fields
        $visitation = Visitation::create([
            'care_worker_id' => $careWorker->id,
            'beneficiary_id' => $beneficiary->beneficiary_id,
            'visit_type' => $visitType,
            'visitation_date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_flexible_time' => $isFlexibleTime,
            'status' => $status,
            'notes' => $this->faker->optional(80)->sentence(5),
            'date_assigned' => Carbon::now()->subDays(rand(10, 60))->format('Y-m-d'), // Add required field
            'assigned_by' => $careWorker->assigned_care_manager_id ?? 2, // Care manager assigns (default to ID 2 if null)
        ]);
        
        // Create a single occurrence for this visitation
        VisitationOccurrence::create([
            'visitation_id' => $visitation->visitation_id,
            'occurrence_date' => $visitation->visitation_date,
            'start_time' => $visitation->start_time,
            'end_time' => $visitation->end_time,
            'status' => $visitation->status,
            'notes' => $status == 'completed' ? $this->faker->optional(70)->paragraph() : null,
        ]);
    }

    /**
     * Generate medication schedules for beneficiaries
     */
    private function generateMedicationSchedules()
    {
        \Log::info("Medication schedules would be generated here if implemented");
        // Note: This would involve creating scheduled medication reminders
        // based on the medications already created in the general care plans
    }

    /**
     * Get participant count for an appointment type
     */
    private function getParticipantCountForAppointmentType($typeName)
    {
        $participantCounts = [
            'Skills Training' => [5, 10], // [min, max]
            'Feedback Session' => [2, 4],
            'Council Meeting' => [4, 8],
            'Health Protocols' => [3, 6],
            'Liga Meetings' => [4, 7],
            'Referrals Discussion' => [2, 4],
            'Assessment Review' => [2, 4],
            'Care Plan Review' => [2, 3],
            'Team Building' => [5, 10],
            'Mentoring Session' => [2, 2],
            'Other' => [2, 5]
        ];
        
        $range = $participantCounts[$typeName] ?? [2, 5]; // Default to 2-5 participants
        return rand($range[0], $range[1]);
    }

    /**
     * Get a realistic appointment title based on type
     */
    private function getRealisticAppointmentTitle($typeName)
    {
        $titles = [
            'Skills Training' => [
                'Documentation System Training',
                'Effective Communication Workshop',
                'Elder Care Best Practices',
                'Specialized Care for Dementia Patients',
                'Emergency Response Protocol Training',
                'Nutritional Assessment Skills',
                'Mobility Assistance Techniques'
            ],
            'Feedback Session' => [
                'Quarterly Performance Review',
                'Care Plan Implementation Feedback',
                'Service Quality Assessment',
                'Client Satisfaction Discussion',
                'Field Work Evaluation',
                'Improvement Strategies Meeting'
            ],
            'Council Meeting' => [
                'Municipal Care Council Regular Meeting',
                'Barangay Health Workers Council',
                'Quarterly Planning Session',
                'Budget Allocation Committee',
                'Service Expansion Planning',
                'Community Resource Coordination'
            ],
            'Health Protocols' => [
                'COVID-19 Safety Measures Update',
                'Heat Illness Prevention Protocol Review',
                'Infectious Disease Control Procedures',
                'Emergency Medical Response Standards',
                'Fall Prevention Protocol Implementation'
            ],
            'Liga Meetings' => [
                'Liga ng mga Barangay General Assembly',
                'Municipal Health Coordination',
                'Inter-barangay Resource Sharing',
                'Community Health Program Alignment',
                'Liga Leadership Planning Session'
            ],
            'Referrals Discussion' => [
                'Medical Specialist Referral Process',
                'Mental Health Services Coordination',
                'Hospital Referral Protocol Review',
                'Community Resource Access Planning',
                'Service Network Expansion'
            ],
            'Assessment Review' => [
                'Quarterly Assessment Standards Review',
                'Evaluation Tools Enhancement',
                'Care Needs Classification Update',
                'Assessment Documentation Improvement',
                'New Client Intake Process Review'
            ],
            'Care Plan Review' => [
                'High-Need Clients Care Plan Review',
                'Monthly Plan Implementation Check',
                'Care Goals Achievement Assessment',
                'Family Involvement Strategy Review',
                'Service Delivery Optimization'
            ],
            'Team Building' => [
                'Annual Team Building Retreat',
                'Staff Cohesion Workshop',
                'Collaborative Problem-Solving Exercise',
                'Cross-Team Integration Activity',
                'Leadership Development Session'
            ],
            'Mentoring Session' => [
                'New Care Worker Orientation',
                'Career Development Planning',
                'Skills Enhancement Guidance',
                'Professional Growth Discussion',
                'Specialized Care Technique Coaching'
            ],
            'Other' => [
                'Program Sustainability Planning',
                'Community Outreach Coordination',
                'Annual Budget Review',
                'Municipal Partnership Discussion',
                'Policy Implementation Briefing',
                'Volunteer Program Development'
            ]
        ];
        
        $typeSpecificTitles = $titles[$typeName] ?? $titles['Other'];
        return $typeSpecificTitles[array_rand($typeSpecificTitles)];
    }

    /**
     * Get a realistic meeting location
     */
    private function getRealisticMeetingLocation()
    {
        $locations = [
            'Municipal Social Welfare Office',
            'Barangay Hall Conference Room',
            'COSE Main Office',
            'Health Center Meeting Room',
            'Municipal Library Conference Room',
            'Rural Health Unit',
            'Community Center',
            'San Roque Multi-Purpose Hall',
            'Mondragon Training Center',
            'Municipal Agriculture Office',
            'Senior Citizens Center'
        ];
        
        return $locations[array_rand($locations)];
    }

    /**
     * Get the barangay ID by name and municipality ID
     */
    private function getBarangayIdByName($barangayName, $municipalityId)
    {
        // Try to find the barangay by name and municipality ID
        $barangay = \DB::table('barangays')
            ->where('barangay_name', $barangayName)
            ->where('municipality_id', $municipalityId)
            ->first();
        
        // If found, return the ID, otherwise return a random barangay ID for that municipality
        if ($barangay) {
            return $barangay->barangay_id;
        } else {
            // Get any barangay ID from that municipality as fallback
            $randomBarangay = \DB::table('barangays')
                ->where('municipality_id', $municipalityId)
                ->inRandomOrder()
                ->first();
            
            return $randomBarangay ? $randomBarangay->barangay_id : 1; // Default to 1 if nothing is found
        }
    }

    // Generate emergency notices and service requests
    private function generateEmergencyAndServiceRequests()
    {
        // Get beneficiaries for emergency notices and service requests
        $beneficiaries = Beneficiary::inRandomOrder()->take(20)->get();
        $familyMembers = FamilyMember::inRandomOrder()->take(15)->get();
        
        // Create emergency types if they don't exist yet
        if (EmergencyType::count() == 0) {
            \Log::info('Creating emergency types...');
            DB::table('emergency_types')->insert([
                ['name' => 'Medical Emergency', 'color_code' => '#dc3545', 'description' => 'Urgent medical situations requiring immediate attention', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Fall Incident', 'color_code' => '#fd7e14', 'description' => 'Falls resulting in injury or requiring assistance', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Medication Issue', 'color_code' => '#6f42c1', 'description' => 'Problems with medication administration or adverse reactions', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Mental Health Crisis', 'color_code' => '#20c997', 'description' => 'Acute mental health episodes requiring intervention', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Other Emergency', 'color_code' => '#6c757d', 'description' => 'Other emergency situations not categorized above', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
        
        // Create service request types if they don't exist yet
        if (ServiceRequestType::count() == 0) {
            \Log::info('Creating service request types...');
            DB::table('service_request_types')->insert([
                ['name' => 'Home Care Visit', 'color_code' => '#0d6efd', 'description' => 'Additional home care services', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Transportation', 'color_code' => '#198754', 'description' => 'Transportation assistance', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Medical Appointments', 'color_code' => '#6610f2', 'description' => 'Assistance with medical appointment visits', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Meal Delivery', 'color_code' => '#fd7e14', 'description' => 'Delivery of prepared meals', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Other Service', 'color_code' => '#6c757d', 'description' => 'Other service requests not categorized above', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
        
        // Create some emergency notices (15 total)
        \Log::info('Creating emergency notices...');
        
        // Create 5 new emergency notices
        EmergencyNotice::factory()->count(5)->asNew()->create();
        
        // Create 5 in progress emergency notices 
        EmergencyNotice::factory()->count(5)->inProgress()->create();
        
        // Create 5 resolved emergency notices (for history)
        EmergencyNotice::factory()->count(5)->state(function () {
            return [
                'status' => 'resolved',
                'read_status' => true,
                'read_at' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
                'assigned_to' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id,
                'action_type' => 'resolved',
                'action_taken_by' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id,
                'action_taken_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            ];
        })->create();
        
        // Create service requests (15 total)
        \Log::info('Creating service requests...');
        
        // Create 5 new service requests
        ServiceRequest::factory()->count(5)->asNew()->create();
        
        // Create 5 approved service requests
        ServiceRequest::factory()->count(5)->state(function () {
            return [
                'status' => 'approved',
                'read_status' => true,
                'read_at' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
                'action_type' => 'approved',
                'action_taken_by' => User::where('role_id', '<=', 2)->inRandomOrder()->first()->id,
                'action_taken_at' => $this->faker->dateTimeBetween('-1 month', '-2 days'),
                'care_worker_id' => User::where('role_id', 3)->inRandomOrder()->first()->id,
            ];
        })->create();
        
        // Create 5 completed/rejected service requests (for history)
        ServiceRequest::factory()->count(5)->state(function () {
            $status = $this->faker->randomElement(['completed', 'rejected']);
            return [
                'status' => $status,
                'read_status' => true,
                'read_at' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
                'action_type' => $status,
                'action_taken_by' => User::where('role_id', '<=', 2)->inRandomOrder()->first()->id,
                'action_taken_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
                'care_worker_id' => $status === 'completed' ? User::where('role_id', 3)->inRandomOrder()->first()->id : null,
            ];
        })->create();
        
        // Create some emergency updates for existing notices
        \Log::info('Creating emergency updates...');
        $emergencies = EmergencyNotice::where('status', '!=', 'new')->get();
        foreach ($emergencies as $emergency) {
            // Generate 1-3 updates per emergency
            $updateCount = rand(1, 3);
            EmergencyUpdate::factory()->count($updateCount)->create([
                'notice_id' => $emergency->notice_id
            ]);
        }
        
        // Create some service request updates
        \Log::info('Creating service request updates...');
        $serviceRequests = ServiceRequest::where('status', '!=', 'new')->get();
        foreach ($serviceRequests as $request) {
            // Generate 1-2 updates per request
            $updateCount = rand(1, 2);
            ServiceRequestUpdate::factory()->count($updateCount)->create([
                'service_request_id' => $request->service_request_id
            ]);
        }
        
        \Log::info('Emergency notices and service requests generated successfully');
    }
}

