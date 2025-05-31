<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\FamilyMember;
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
use App\Models\EmergencyType;
use App\Models\ServiceRequestType;
use App\Models\EmergencyNotice;
use App\Models\ServiceRequest;
use App\Models\EmergencyUpdate;
use App\Models\ServiceRequestUpdate;

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

        // Create exactly 2 admin users with specific organization roles
        try {
            $this->command->info('Seeding admin users...');
            
            // First admin with organization_role_id = 2
            $admin1 = User::factory()->create([
                'role_id' => 1,
                'organization_role_id' => 2,
                'password' => bcrypt('12312312')
            ]);
            
            // Second admin with organization_role_id = 3
            $admin2 = User::factory()->create([
                'role_id' => 1,
                'organization_role_id' => 3,
                'password' => bcrypt('12312312')
            ]);
            
            $this->command->info('Admin users seeded successfully.');
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed admin users: ' . $e->getMessage());
            \Log::error('Failed to seed admin users', ['exception' => $e]);
        }

        // Create exactly 2 care managers
        try {
            $this->command->info('Seeding care managers...');
            
            // Create 2 care managers, one for each municipality
            $careManagers = [];
            
            // First care manager for municipality 1 (Mondragon)
            $careManagers[] = User::factory()->create([
                'role_id' => 2, // Care Manager
                'assigned_municipality_id' => 1,
                'password' => bcrypt('12312312')
            ]);
            
            // Second care manager for municipality 2 (San Roque)
            $careManagers[] = User::factory()->create([
                'role_id' => 2, // Care Manager
                'assigned_municipality_id' => 2,
                'password' => bcrypt('12312312')
            ]);
            
            $careManagers = collect($careManagers);
            $this->command->info('Care managers seeded successfully.');
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed care managers: ' . $e->getMessage());
            \Log::error('Failed to seed care managers', ['exception' => $e]);
        }

        // Create exactly 10 care workers
        try {
            $this->command->info('Seeding care workers...');
            $careWorkers = [];
            
            // Create 5 care workers for municipality 1 (Mondragon)
            for ($i = 1; $i <= 5; $i++) {
                $careWorker = User::factory()->create([
                    'role_id' => 3, // Care Worker
                    'assigned_care_manager_id' => $careManagers[0]->id,
                    'assigned_municipality_id' => 1,
                    'password' => bcrypt('12312312')
                ]);
                
                $careWorkers[] = $careWorker;
            }
            
            // Create 5 care workers for municipality 2 (San Roque)
            for ($i = 1; $i <= 5; $i++) {
                $careWorker = User::factory()->create([
                    'role_id' => 3, // Care Worker
                    'assigned_care_manager_id' => $careManagers[1]->id,
                    'assigned_municipality_id' => 2,
                    'password' => bcrypt('12312312')
                ]);
                
                $careWorkers[] = $careWorker;
            }
            
            $careWorkers = collect($careWorkers);
            $this->command->info('Care workers seeded successfully.');
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed care workers: ' . $e->getMessage());
            \Log::error('Failed to seed care workers', ['exception' => $e]);
        }

        try {
            $this->command->info('Seeding general care plans...');
            $generalCarePlans = [];
            
            for ($i = 1; $i <= 100; $i++) {
                // Determine which area this care plan belongs to
                $municipalityId = ($i <= 50) ? 1 : 2;
                
                // Assign care worker based on municipality
                $municipalityWorkers = $careWorkers->where('assigned_municipality_id', $municipalityId);
                $careWorker = $municipalityWorkers->random();
                
                // Create the general care plan
                $generalCarePlan = GeneralCarePlan::create([
                    'general_care_plan_id' => $i,
                    'review_date' => Carbon::now()->addMonths(6),
                    'emergency_plan' => 'Standard emergency procedures for ' . 
                        ($municipalityId == 1 ? 'Mondragon' : 'San Roque') . ' residents. ' .
                        'Contact primary caregiver immediately, then local emergency services if needed.',
                    'care_worker_id' => $careWorker->id,
                    'created_at' => now()->subDays(rand(30, 180)),
                    'updated_at' => now()->subDays(rand(1, 30))
                ]);
                
                // Create emotional wellbeing with realistic content
                EmotionalWellbeing::factory()->create([
                    'general_care_plan_id' => $i,
                ]);
                
                // Create health history with realistic content
                HealthHistory::factory()->create([
                    'general_care_plan_id' => $i,
                ]);
                
                // Create cognitive function with realistic content
                CognitiveFunction::factory()->create([
                    'general_care_plan_id' => $i,
                ]);
                
                // Create mobility with realistic content
                Mobility::factory()->create([
                    'general_care_plan_id' => $i,
                ]);
                
                // Create medications for this general care plan (2-4 medications per beneficiary)
                // Ensure they're unique and appropriate for the beneficiary
                $medicationCount = rand(2, 4);
                $usedMedications = [];
                
                for ($j = 0; $j < $medicationCount; $j++) {
                    $medication = Medication::factory()->make([
                        'general_care_plan_id' => $i,
                    ]);
                    
                    // Avoid duplicate medications for same beneficiary
                    if (!in_array($medication->medication, $usedMedications)) {
                        $usedMedications[] = $medication->medication;
                        $medication->save();
                    } else {
                        // Try again with a different medication
                        $j--;
                    }
                }
                
                // Create care needs for this general care plan - one for each care category
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
                        'care_worker_id' => $careWorker->id,
                    ]);
                }
                
                $generalCarePlans[] = $generalCarePlan;
            }
            $this->command->info('General care plans seeded successfully.');
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed general care plans: ' . $e->getMessage());
            \Log::error('Failed to seed general care plans', ['exception' => $e]);
        }

        // Create beneficiaries with balanced distribution
        try {
            $this->command->info('Seeding beneficiaries...');
            
            // Create 100 beneficiaries with Filipino data
            $beneficiaries = [];
            
            // Track the count of beneficiaries per municipality to maintain balance
            $municipalityCounts = [1 => 0, 2 => 0];
            $maxPerMunicipality = 50;
            
            // Track the count of males and females to maintain balance
            $genderCounts = ['Male' => 0, 'Female' => 0];
            $maxPerGender = 50;
            
            for ($i = 0; $i < 100; $i++) {
                // Determine which municipality to use next to maintain balance
                if ($municipalityCounts[1] >= $maxPerMunicipality) {
                    $municipalityId = 2;
                } elseif ($municipalityCounts[2] >= $maxPerMunicipality) {
                    $municipalityId = 1;
                } else {
                    $municipalityId = $this->faker->randomElement([1, 2]);
                }
                
                // Determine gender to maintain balance
                if ($genderCounts['Male'] >= $maxPerGender) {
                    $gender = 'Female';
                } elseif ($genderCounts['Female'] >= $maxPerGender) {
                    $gender = 'Male';
                } else {
                    $gender = $this->faker->randomElement(['Male', 'Female']);
                }
                
                // Create the beneficiary
                $beneficiary = Beneficiary::factory()->create([
                    'municipality_id' => $municipalityId,
                    'gender' => $gender,
                    'general_care_plan_id' => $generalCarePlans[$i]->general_care_plan_id,
                    'beneficiary_status_id' => 1, // Active status
                    'category_id' => $this->faker->numberBetween(1, 8)
                ]);
                
                // Update the counts
                $municipalityCounts[$municipalityId]++;
                $genderCounts[$gender]++;
                
                $beneficiaries[] = $beneficiary;
            }
            $beneficiaries = collect($beneficiaries);
            $this->command->info('Beneficiaries seeded successfully.');
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed beneficiaries: ' . $e->getMessage());
            \Log::error('Failed to seed beneficiaries', ['exception' => $e]);
        }

        // Create family members for beneficiaries
        try {
            $this->command->info('Seeding family members...');
            // Create 1-3 family members for each beneficiary
            foreach ($beneficiaries as $beneficiary) {
                $familyMemberCount = $this->faker->numberBetween(1, 3);
                FamilyMember::factory()
                    ->count($familyMemberCount)
                    ->forBeneficiary($beneficiary->beneficiary_id)
                    ->create();
            }
            $this->command->info('Family members seeded successfully.');
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed family members: ' . $e->getMessage());
            \Log::error('Failed to seed family members', ['exception' => $e]);
        }

        // Create weekly care plans using the factory
        try {
            $this->command->info('Seeding weekly care plans...');
            
            // Generate initial vital signs
            $vitalSigns = VitalSigns::factory()->count(200)->create();
            
            $wcpCount = 0;
            $interventionCount = 0;
            $this->command->getOutput()->progressStart(count($beneficiaries));
            
            // Define the date range for care plans
            $startDate = Carbon::createFromDate(2024, 1, 1); // Start from January 2024
            $endDate = Carbon::now(); // Until current date
            
            // For each beneficiary, create 3-4 weekly care plans per month from Jan 2024 until now
            foreach ($beneficiaries as $beneficiary) {
                // Get care worker from beneficiary's municipality
                $careWorker = $careWorkers->where('assigned_municipality_id', $beneficiary->municipality_id)->random();
                $careManager = User::where('role_id', 2)
                    ->where('assigned_municipality_id', $beneficiary->municipality_id)
                    ->first();
                
                // Clone the startDate to avoid modifying the original
                $currentMonth = $startDate->copy();
                
                // Loop through each month
                while ($currentMonth->lte($endDate)) {
                    // Determine how many care plans to create this month (3-4)
                    $plansThisMonth = $this->faker->numberBetween(3, 4);
                    
                    // Calculate days in current month
                    $daysInMonth = $currentMonth->daysInMonth;
                    
                    // Create care plans evenly distributed throughout the month
                    for ($i = 0; $i < $plansThisMonth; $i++) {
                        // Calculate the target day for this care plan
                        // This spaces them out evenly across the month
                        $targetDay = ceil(($i + 1) * $daysInMonth / ($plansThisMonth + 1));
                        
                        // Create a date for this care plan
                        $planDate = $currentMonth->copy()->day($targetDay);
                        
                        // If this date is in the future, stop generating care plans
                        if ($planDate->gt($endDate)) {
                            break;
                        }
                        
                        // Get a random vital sign
                        $vitalSign = $vitalSigns->random();
                        
                        // Create the weekly care plan
                        $weeklyCarePlanFactory = WeeklyCarePlan::factory();
                        $weeklyCarePlan = $weeklyCarePlanFactory->make([
                            'beneficiary_id' => $beneficiary->beneficiary_id,
                            'care_worker_id' => $careWorker->id,
                            'care_manager_id' => $careManager->id,
                            'vital_signs_id' => $vitalSign->vital_signs_id,
                            'date' => $planDate->format('Y-m-d'),
                            'created_by' => $careWorker->id,
                            'updated_by' => $careManager->id,
                            'created_at' => $planDate,
                            'updated_at' => $planDate->copy()->addDay(),
                            'photo_path' => "uploads/weekly_care_plans/{$planDate->format('Y')}/{$planDate->format('m')}/beneficiary_{$beneficiary->beneficiary_id}_assessment_{$this->faker->randomNumber(8)}.jpg"
                        ]);
                        
                        // Save the weekly care plan to get an ID
                        $weeklyCarePlan->save();
                        
                        // Now create interventions based on the assessment
                        // Get suggested interventions based on assessment text
                        $interventionSuggestions = $weeklyCarePlanFactory->getInterventionSuggestions($weeklyCarePlan->assessment);
                        
                        // Create 2-5 intervention records for this weekly care plan
                        $interventionsToCreate = min(count($interventionSuggestions), $this->faker->numberBetween(2, 5));
                        
                        for ($j = 0; $j < $interventionsToCreate; $j++) {
                            $interventionDescription = $interventionSuggestions[$j] ?? $this->faker->sentence();
                            $careCategory = CareCategory::inRandomOrder()->first();
                            $intervention = Intervention::where('care_category_id', $careCategory->care_category_id)
                                ->inRandomOrder()->first();
                            
                            WeeklyCarePlanInterventions::create([
                                'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                                'intervention_id' => $intervention ? $intervention->intervention_id : null,
                                'care_category_id' => $careCategory->care_category_id,
                                'intervention_description' => $interventionDescription,
                                'duration_minutes' => $this->faker->randomFloat(2, 10, 60),
                                'implemented' => $planDate->lt(Carbon::now()) ? $this->faker->boolean(80) : false // 80% chance of being implemented for past care plans
                            ]);
                            
                            $interventionCount++;
                        }
                        
                        // Add beneficiary acknowledgement to some past care plans
                        if ($planDate->lt(Carbon::now()->subWeek()) && $this->faker->boolean(40)) {
                            if ($this->faker->boolean(70)) {
                                $weeklyCarePlan->acknowledged_by_beneficiary = $beneficiary->beneficiary_id;
                                $weeklyCarePlan->save();
                            } else {
                                // Try to get a family member for this beneficiary
                                $familyMember = FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)
                                    ->inRandomOrder()
                                    ->first();
                                    
                                if ($familyMember) {
                                    $weeklyCarePlan->acknowledged_by_family = $familyMember->family_member_id;
                                    $weeklyCarePlan->save();
                                }
                            }
                        }
                        
                        $wcpCount++;
                    }
                    
                    // Move to the next month
                    $currentMonth->addMonth();
                }
                
                $this->command->getOutput()->progressAdvance();
            }
            
            $this->command->getOutput()->progressFinish();
            $this->command->info("Created {$wcpCount} weekly care plans with {$interventionCount} interventions.");
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed weekly care plans: ' . $e->getMessage());
            \Log::error('Failed to seed weekly care plans', ['exception' => $e]);
        }
    
        try {
            $this->command->info('Seeding notifications...');
            // 7. Generate notifications - adjusted for new user counts
            $this->generateNotifications();
            $this->command->info('Notifications seeded successfully.');
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed notifications: ' . $e->getMessage());
            \Log::error('Failed to seed notifications', ['exception' => $e]);
        }

        try {
            $this->command->info('Seeding conversations...');
            // 8. Generate conversations and messages - adjusted for new user structure
            $this->generateConversations();
            $this->command->info('Conversations seeded successfully.');
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed conversations: ' . $e->getMessage());
            \Log::error('Failed to seed conversations', ['exception' => $e]);
        }

        try {
            $this->command->info('Seeding scheduling data...');
            // 9. Generate scheduling data (appointments, visitations, medication schedules)
            $this->generateSchedulingData($careWorkers, $beneficiaries);
            $this->command->info('Scheduling data seeded successfully.');
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed scheduling data: ' . $e->getMessage());
            \Log::error('Failed to seed scheduling data', ['exception' => $e]);
        }

        try {
            $this->command->info('Seeding emergency notices and service requests...');
            // 10. Generate emergency notices and service requests
            $this->generateEmergencyAndServiceRequests();
            $this->command->info('Emergency notices and service requests seeded successfully.');
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed emergency notices and service requests: ' . $e->getMessage());
            \Log::error('Failed to seed emergency notices and service requests', ['exception' => $e]);
        }

        try {
            $this->command->info('Seeding expense tracker data...');
            // 11. Generate expenses tracker data
            $this->generateExpenseTrackerData();
            $this->command->info('Expense tracker data seeded successfully.');
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed expense tracker data: ' . $e->getMessage());
            \Log::error('Failed to seed expense tracker data', ['exception' => $e]);
        }

        $this->command->info('Database seeding complete!');
    }
    

    /**
     * Generate realistic weekly care plans with diverse interventions
     * Using existing interventions from the database
     */
    private function generateRealisticWeeklyCarePlans($careWorkers, $beneficiaries)
    {
        \Log::info('Starting generateRealisticWeeklyCarePlans');
        $careWorkerCollection = collect($careWorkers);
        
        // Define date range parameters
        $startDate = Carbon::now()->subMonths(12);
        $endDate = Carbon::now();
        
        // Progress tracking
        $total = count($beneficiaries);
        $this->command->getOutput()->progressStart($total);
        $wcpCount = 0;
        
        // Loop through each beneficiary
        foreach ($beneficiaries as $beneficiary) {
            // Determine how many care plans to create for this beneficiary (2-12)
            $plansToCreate = $this->faker->numberBetween(2, 12);
            
            // Calculate dates evenly across the period
            $dateStep = $endDate->diffInDays($startDate) / ($plansToCreate + 1);
            $currentDate = clone $startDate;
            
            for ($i = 0; $i < $plansToCreate; $i++) {
                // Move date forward for each plan
                $currentDate = $currentDate->addDays($dateStep + $this->faker->numberBetween(-3, 3)); // Add some randomness
                
                // Assign a random care worker who matches the municipality of the beneficiary
                $matchedCareWorkers = $careWorkerCollection->where('assigned_municipality_id', $beneficiary->municipality_id);
                
                // If no matching care worker, use any care worker (fallback)
                if ($matchedCareWorkers->isEmpty()) {
                    $careWorker = $careWorkerCollection->random();
                } else {
                    $careWorker = $matchedCareWorkers->random();
                }
                
                // First, create vital signs record
                $vitalSigns = VitalSigns::factory()->create([
                    'created_by' => $careWorker->id,
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate
                ]);
                
                // Create weekly care plan with gender-appropriate assessments
                $weeklyCarePlan = WeeklyCarePlan::factory()->create([
                    'beneficiary_id' => $beneficiary->beneficiary_id,
                    'care_worker_id' => $careWorker->id,
                    'care_manager_id' => $careWorker->assigned_care_manager_id ?? User::where('role_id', 2)->inRandomOrder()->first()->id,
                    'vital_signs_id' => $vitalSigns->vital_signs_id,
                    'date' => $currentDate->format('Y-m-d'),
                    'created_by' => $careWorker->id,
                    'updated_by' => $careWorker->assigned_care_manager_id ?? User::where('role_id', 2)->inRandomOrder()->first()->id,
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate->addDays(1) // Care manager reviews a day later
                ]);
                
                // Get interventions from the factory to ensure they match the assessment
                $interventionsJson = $weeklyCarePlan->interventions;
                $interventions = $interventionsJson ? json_decode($interventionsJson, true) : [];
                
                // Now create actual intervention records for the weekly care plan
                // Either use the interventions from JSON or create new ones from DB
                if (!empty($interventions)) {
                    foreach ($interventions as $interventionName) {
                        // Find a matching intervention in the database or use a general one
                        $intervention = Intervention::where('intervention_description', 'like', "%$interventionName%")
                            ->inRandomOrder()
                            ->first();
                        
                        // If no exact match found, get any intervention from the care category
                        if (!$intervention) {
                            $intervention = Intervention::inRandomOrder()->first();
                        }
                        
                        // Create the intervention record
                        WeeklyCarePlanInterventions::create([
                            'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                            'intervention_id' => $intervention->intervention_id,
                            'care_category_id' => $intervention->care_category_id,
                            'duration_minutes' => $this->faker->numberBetween(15, 60),
                            'implemented' => $this->faker->boolean(80) // 80% chance of being implemented
                        ]);
                    }
                } else {
                    // Fallback: create 2-4 random interventions 
                    $interventionCount = $this->faker->numberBetween(2, 4);
                    $interventions = Intervention::inRandomOrder()->take($interventionCount)->get();
                    
                    foreach ($interventions as $intervention) {
                        WeeklyCarePlanInterventions::create([
                            'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                            'intervention_id' => $intervention->intervention_id,
                            'care_category_id' => $intervention->care_category_id,
                            'duration_minutes' => $this->faker->numberBetween(15, 60),
                            'implemented' => $this->faker->boolean(80)
                        ]);
                    }
                }
                
                // Randomly set acknowledgements for plans older than 2 weeks
                if ($currentDate->diffInDays(Carbon::now()) > 14 && $this->faker->boolean(40)) {
                    if ($this->faker->boolean()) {
                        $weeklyCarePlan->acknowledged_by_beneficiary = $beneficiary->beneficiary_id;
                    } else {
                        // Try to get a family member for this beneficiary
                        $familyMember = FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)
                            ->inRandomOrder()
                            ->first();
                            
                        if ($familyMember) {
                            $weeklyCarePlan->acknowledged_by_family = $familyMember->family_member_id;
                        }
                    }
                    $weeklyCarePlan->save();
                }
                
                $wcpCount++;
            }
            
            $this->command->getOutput()->progressAdvance();
        }
        
        $this->command->getOutput()->progressFinish();
        \Log::info("Created {$wcpCount} weekly care plans");
        $this->command->info("Created {$wcpCount} weekly care plans");
    }

    /**
     * Get a random custom intervention description based on category
     */
    private function getRandomCustomIntervention($categoryId)
    {
        $customInterventions = [
            1 => [ // Mobility category
                'Personalized transfer assistance with extra support for weak side',
                'Supervised walking with arm support technique',
                'Modified stair climbing with two-point contact',
                'Assisted bathroom mobility with specialized equipment',
                'Customized bed mobility exercises with enhanced lumbar support'
            ],
            2 => [ // Cognitive assistance category
                'Memory enhancement through personalized photo albums',
                'Specialized orientation techniques using familiar objects',
                'Custom cognitive stimulation through familiar music and conversation',
                'Simplified multi-step task instructions with visual aids',
                'Personalized reality orientation through daily calendar review'
            ],
            3 => [ // Personal care category
                'Specialized bathing technique with minimal water exposure',
                'Adaptive dressing method using modified clothing',
                'Custom grooming routine with specialized tools',
                'Modified oral hygiene protocol for sensitive gums',
                'Personalized skincare regimen for pressure ulcer prevention'
            ],
            4 => [ // Medical care category
                'Enhanced medication organization system with color coding',
                'Specialized wound care technique with minimal discomfort',
                'Custom vital signs monitoring with simplified recording method',
                'Adaptive range of motion exercises tailored to specific limitations',
                'Personalized pain management techniques combining physical and cognitive approaches'
            ],
            5 => [ // Social activities category
                'Customized reminiscence therapy using local cultural references',
                'Adapted social engagement activities with limited verbal requirements',
                'Modified group activities with enhanced sensory components',
                'Personalized community connection through virtual platforms',
                'Specialized family engagement activities focusing on non-verbal communication'
            ],
            6 => [ // Nutrition category
                'Customized feeding technique for swallowing difficulties',
                'Specialized food preparation for enhanced nutrient absorption',
                'Modified hydration schedule with thickened liquids',
                'Personalized meal planning focused on local ingredients and preferences',
                'Custom portion control system with visual guides'
            ],
            7 => [ // Household category
                'Specialized laundry management with simplified sorting system',
                'Customized home organization focused on fall prevention',
                'Modified meal preparation techniques with adaptive equipment',
                'Personalized cleaning routine with ergonomic considerations',
                'Adaptive home maintenance schedule with prioritized safety tasks'
            ]
        ];
        
        // Default to first category if the requested one doesn't exist
        if (!isset($customInterventions[$categoryId])) {
            $categoryId = 1;
        }
        
        return $this->faker->randomElement($customInterventions[$categoryId]);
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
        $this->command->info('Generating internal appointments...');
        
        try {
            $appointmentTypes = AppointmentType::all();
            $staffUsers = User::where('role_id', '<=', 3)->get();
            
            if ($appointmentTypes->isEmpty()) {
                $this->command->error('No appointment types found. Please run migrations first.');
                return;
            }
            
            if ($staffUsers->isEmpty()) {
                $this->command->error('No staff users found. Please seed users first.');
                return;
            }
            
            // Track appointment counts for reporting
            $pastCount = 0;
            $futureCount = 0;
            $recurringCount = 0;
            $occurrenceCount = 0;
            
            // Create past appointments (completed or canceled)
            for ($i = 0; $i < 20; $i++) {
                $appointmentType = $appointmentTypes->random();
                $pastDate = $this->faker->dateTimeBetween('-6 months', '-1 day');
                $status = $this->faker->randomElement(['completed', 'completed', 'completed', 'canceled']); // Weight towards completed
                
                // Create the appointment
                $appointment = Appointment::factory()->create([
                    'appointment_type_id' => $appointmentType->appointment_type_id,
                    'date' => $pastDate->format('Y-m-d'),
                    'status' => $status,
                    'created_by' => $staffUsers->where('role_id', '<=', 2)->random()->id // Created by admin or care manager
                ]);
                
                // Add organizer
                $organizer = $staffUsers->where('role_id', '<=', 2)->random(); // Admin or care manager
                AppointmentParticipant::create([
                    'appointment_id' => $appointment->appointment_id,
                    'participant_id' => $organizer->id,
                    'participant_type' => 'cose_user',
                    'is_organizer' => true
                ]);
                
                // Add other participants (3-8 additional staff)
                $participantCount = $this->faker->numberBetween(3, 8);
                $availableParticipants = $staffUsers->reject(function($user) use ($organizer) {
                    return $user->id === $organizer->id;
                })->shuffle();
                
                for ($j = 0; $j < min($participantCount, count($availableParticipants)); $j++) {
                    AppointmentParticipant::create([
                        'appointment_id' => $appointment->appointment_id,
                        'participant_id' => $availableParticipants[$j]->id,
                        'participant_type' => 'cose_user',
                        'is_organizer' => false
                    ]);
                }
                
                // Create a single occurrence for this appointment
                AppointmentOccurrence::create([
                    'appointment_id' => $appointment->appointment_id,
                    'occurrence_date' => $pastDate->format('Y-m-d'),
                    'start_time' => $appointment->start_time,
                    'end_time' => $appointment->end_time,
                    'status' => $status,
                    'notes' => $status === 'completed' ? 
                        $this->faker->optional(0.7)->paragraph() : 
                        ($status === 'canceled' ? $this->faker->optional(0.9)->sentence() : null)
                ]);
                
                $pastCount++;
            }
            
            // Create future appointments (scheduled)
            for ($i = 0; $i < 15; $i++) {
                $appointmentType = $appointmentTypes->random();
                $futureDate = $this->faker->dateTimeBetween('+1 day', '+2 months');
                
                // Create the appointment
                $appointment = Appointment::factory()->create([
                    'appointment_type_id' => $appointmentType->appointment_type_id,
                    'date' => $futureDate->format('Y-m-d'),
                    'status' => 'scheduled',
                    'created_by' => $staffUsers->where('role_id', '<=', 2)->random()->id // Created by admin or care manager
                ]);
                
                // Add organizer
                $organizer = $staffUsers->where('role_id', '<=', 2)->random(); // Admin or care manager
                AppointmentParticipant::create([
                    'appointment_id' => $appointment->appointment_id,
                    'participant_id' => $organizer->id,
                    'participant_type' => 'cose_user',
                    'is_organizer' => true
                ]);
                
                // Add other participants (3-8 additional staff)
                $participantCount = $this->faker->numberBetween(3, 8);
                $availableParticipants = $staffUsers->reject(function($user) use ($organizer) {
                    return $user->id === $organizer->id;
                })->shuffle();
                
                for ($j = 0; $j < min($participantCount, count($availableParticipants)); $j++) {
                    AppointmentParticipant::create([
                        'appointment_id' => $appointment->appointment_id,
                        'participant_id' => $availableParticipants[$j]->id,
                        'participant_type' => 'cose_user',
                        'is_organizer' => false
                    ]);
                }
                
                // Create a single occurrence for this appointment
                AppointmentOccurrence::create([
                    'appointment_id' => $appointment->appointment_id,
                    'occurrence_date' => $futureDate->format('Y-m-d'),
                    'start_time' => $appointment->start_time,
                    'end_time' => $appointment->end_time,
                    'status' => 'scheduled'
                ]);
                
                $futureCount++;
            }
            
            // Create recurring appointments
            for ($i = 0; $i < 8; $i++) {
                $appointmentType = $appointmentTypes->random();
                $startDate = $this->faker->dateTimeBetween('-1 month', '+2 weeks');
                
                // 80% weekly, 20% monthly
                $patternType = $this->faker->randomElement(['weekly', 'weekly', 'weekly', 'weekly', 'monthly']); 
                $dayOfWeek = null;
                
                if ($patternType === 'weekly') {
                    // 70% single day, 30% multiple days
                    if ($this->faker->boolean(70)) {
                        $dayOfWeek = $this->faker->numberBetween(1, 5); // Monday to Friday
                    } else {
                        $days = $this->faker->randomElements(range(1, 5), 2); // 2 days between Monday and Friday
                        sort($days); // Keep them in order
                        $dayOfWeek = implode(',', $days);
                    }
                } else {
                    $dayOfWeek = $startDate->format('j'); // Day of month without leading zeros
                }
                
                // Create the recurring appointment
                $appointment = Appointment::factory()->create([
                    'appointment_type_id' => $appointmentType->appointment_type_id,
                    'date' => $startDate->format('Y-m-d'),
                    'status' => 'scheduled',
                    'created_by' => $staffUsers->where('role_id', '<=', 2)->random()->id
                ]);
                
                // Add organizer
                $organizer = $staffUsers->where('role_id', '<=', 2)->random();
                AppointmentParticipant::create([
                    'appointment_id' => $appointment->appointment_id,
                    'participant_id' => $organizer->id,
                    'participant_type' => 'cose_user',
                    'is_organizer' => true
                ]);
                
                // Add other participants (3-6 additional staff)
                $participantCount = $this->faker->numberBetween(3, 6);
                $availableParticipants = $staffUsers->reject(function($user) use ($organizer) {
                    return $user->id === $organizer->id;
                })->shuffle();
                
                for ($j = 0; $j < min($participantCount, count($availableParticipants)); $j++) {
                    AppointmentParticipant::create([
                        'appointment_id' => $appointment->appointment_id,
                        'participant_id' => $availableParticipants[$j]->id,
                        'participant_type' => 'cose_user',
                        'is_organizer' => false
                    ]);
                }
                
                // Create the recurring pattern
                $recurrenceEnd = $this->faker->dateTimeBetween('+2 months', '+6 months')->format('Y-m-d');
                
                RecurringPattern::create([
                    'appointment_id' => $appointment->appointment_id,
                    'pattern_type' => $patternType,
                    'day_of_week' => $dayOfWeek,
                    'recurrence_end' => $recurrenceEnd
                ]);
                
                // Generate occurrences
                $newOccurrences = $this->generateAppointmentOccurrences($appointment, 3);
                $occurrenceCount += count($newOccurrences);
                $recurringCount++;
            }
            
            $this->command->info("Generated appointments: {$pastCount} past, {$futureCount} future, {$recurringCount} recurring with {$occurrenceCount} occurrences");
            
        } catch (\Throwable $e) {
            $this->command->error('Failed to generate internal appointments: ' . $e->getMessage());
            \Log::error('Failed to generate internal appointments', ['exception' => $e]);
        }
    }

    /**
     * Generate occurrences for a recurring appointment
     * 
     * @param Appointment $appointment The appointment to generate occurrences for
     * @param int $months Number of months to generate occurrences for
     * @return array Array of generated occurrence IDs
     */
    private function generateAppointmentOccurrences($appointment, $months = 3)
    {
        // Check if this is a recurring appointment
        $pattern = RecurringPattern::where('appointment_id', $appointment->appointment_id)->first();
        if (!$pattern) {
            return [];
        }
        
        $occurrences = [];
        $startDate = Carbon::parse($appointment->date);
        $endDate = $pattern->recurrence_end ? 
                Carbon::parse($pattern->recurrence_end) : 
                $startDate->copy()->addMonths($months);
        
        // Generate based on pattern type
        if ($pattern->pattern_type === 'weekly') {
            // Parse day_of_week string into array of integers
            $daysOfWeek = [];
            if (strpos($pattern->day_of_week, ',') !== false) {
                $daysOfWeek = array_map('intval', explode(',', $pattern->day_of_week));
            } else {
                $daysOfWeek = [intval($pattern->day_of_week)];
            }
            
            $currentDate = $startDate->copy();
            
            // First, set the current date to the first occurrence of each day of week
            foreach ($daysOfWeek as $day) {
                $firstDate = $currentDate->copy();
                // If the current day is not the specified day of week, move to the next occurrence
                if ($firstDate->dayOfWeek !== $day) {
                    $daysToAdd = ($day - $firstDate->dayOfWeek + 7) % 7;
                    if ($daysToAdd === 0) {
                        $daysToAdd = 7; // Move to next week if we're already on this day
                    }
                    $firstDate->addDays($daysToAdd);
                }
                
                // Create occurrences for this day of week until the end date
                $dateIterator = $firstDate->copy();
                while ($dateIterator <= $endDate) {
                    // Create the occurrence
                    $occurrence = AppointmentOccurrence::create([
                        'appointment_id' => $appointment->appointment_id,
                        'occurrence_date' => $dateIterator->format('Y-m-d'),
                        'start_time' => $appointment->start_time,
                        'end_time' => $appointment->end_time,
                        'status' => $dateIterator->isPast() ? 
                            $this->faker->randomElement(['completed', 'completed', 'canceled']) : 'scheduled',
                        'notes' => $dateIterator->isPast() ? $this->faker->optional(0.7)->paragraph() : null
                    ]);
                    
                    $occurrences[] = $occurrence->occurrence_id;
                    
                    // Move to the next week
                    $dateIterator->addWeek();
                }
            }
            
        } elseif ($pattern->pattern_type === 'monthly') {
            $dayOfMonth = intval($pattern->day_of_week); // For monthly, this is the day of month
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                // Ensure the day exists in the current month (handle cases like 31st in Feb)
                $daysInMonth = $currentDate->daysInMonth;
                $validDay = min($dayOfMonth, $daysInMonth);
                
                // Set to the correct day of the month
                $currentDate->day = $validDay;
                
                // Only create an occurrence if we're still in range
                if ($currentDate <= $endDate) {
                    // Create the occurrence
                    $occurrence = AppointmentOccurrence::create([
                        'appointment_id' => $appointment->appointment_id,
                        'occurrence_date' => $currentDate->format('Y-m-d'),
                        'start_time' => $appointment->start_time,
                        'end_time' => $appointment->end_time,
                        'status' => $currentDate->isPast() ? 
                            $this->faker->randomElement(['completed', 'completed', 'canceled']) : 'scheduled',
                        'notes' => $currentDate->isPast() ? $this->faker->optional(0.7)->paragraph() : null
                    ]);
                    
                    $occurrences[] = $occurrence->occurrence_id;
                }
                
                // Move to the next month
                $currentDate->addMonth();
            }
        }
        
        return $occurrences;
    }

    /**
     * Generate care worker visitations
     */
    private function generateCareWorkerVisitations($careWorkers, $beneficiaries)
    {
        $this->command->info('Generating care worker visitations...');
        
        try {
            // Track visitation counts for reporting
            $regularCount = 0;
            $recurringCount = 0;
            $occurrenceCount = 0;
            
            // Create 40 regular (non-recurring) visitations
            for ($i = 0; $i < 40; $i++) {
                // Randomly select a care worker and an appropriate beneficiary (same municipality)
                $careWorker = $careWorkers->random();
                $municipalityId = $careWorker->assigned_municipality_id;
                
                // Find beneficiaries in the same municipality
                $municipalityBeneficiaries = $beneficiaries->where('municipality_id', $municipalityId);
                
                // If no beneficiaries in this municipality, use any beneficiary
                if ($municipalityBeneficiaries->isEmpty()) {
                    $beneficiary = $beneficiaries->random();
                } else {
                    $beneficiary = $municipalityBeneficiaries->random();
                }
                
                // Create the visitation with appropriate care worker and beneficiary
                $visitation = Visitation::factory()->create([
                    'care_worker_id' => $careWorker->id,
                    'beneficiary_id' => $beneficiary->beneficiary_id,
                    'visit_type' => $this->faker->randomElement(['routine_care_visit', 'service_request']),
                    'visitation_date' => $this->faker->dateTimeBetween('-2 weeks', '+2 weeks')->format('Y-m-d'),
                    'assigned_by' => User::where('role_id', 2)
                        ->where('assigned_municipality_id', $municipalityId)
                        ->first()->id ?? User::where('role_id', 1)->first()->id
                ]);
                
                // Create a single occurrence for this visitation
                VisitationOccurrence::factory()->create([
                    'visitation_id' => $visitation->visitation_id,
                    'occurrence_date' => $visitation->visitation_date,
                    'start_time' => $visitation->start_time,
                    'end_time' => $visitation->end_time,
                    'status' => Carbon::parse($visitation->visitation_date)->isPast() ? 
                        $this->faker->randomElement(['completed', 'canceled']) : 'scheduled'
                ]);
                
                $regularCount++;
            }
            
            // Create 15 recurring visitations (weekly pattern)
            for ($i = 0; $i < 15; $i++) {
                // Randomly select a care worker and beneficiary from the same area
                $careWorker = $careWorkers->random();
                $municipalityId = $careWorker->assigned_municipality_id;
                $municipalityBeneficiaries = $beneficiaries->where('municipality_id', $municipalityId);
                $beneficiary = $municipalityBeneficiaries->isEmpty() ? 
                            $beneficiaries->random() : 
                            $municipalityBeneficiaries->random();
                
                // Create the visitation with weekly recurring pattern
                $startDate = $this->faker->dateTimeBetween('-1 month', '+1 week');
                $visitation = $this->createRecurringVisitation(
                    $careWorker, 
                    $beneficiary, 
                    'routine_care_visit', 
                    'weekly',
                    $startDate
                );
                
                // Generate occurrences for this recurring visitation
                $generatedOccurrences = $this->generateVisitationOccurrences($visitation, 3);
                $occurrenceCount += count($generatedOccurrences);
                $recurringCount++;
            }
            
            // Create 5 recurring visitations (monthly pattern)
            for ($i = 0; $i < 5; $i++) {
                // Randomly select a care worker and beneficiary from the same area
                $careWorker = $careWorkers->random();
                $municipalityId = $careWorker->assigned_municipality_id;
                $municipalityBeneficiaries = $beneficiaries->where('municipality_id', $municipalityId);
                $beneficiary = $municipalityBeneficiaries->isEmpty() ? 
                            $beneficiaries->random() : 
                            $municipalityBeneficiaries->random();
                
                // Create the visitation with monthly recurring pattern
                $startDate = $this->faker->dateTimeBetween('-1 month', '+1 week');
                $visitation = $this->createRecurringVisitation(
                    $careWorker, 
                    $beneficiary, 
                    'service_request', 
                    'monthly',
                    $startDate
                );
                
                // Generate occurrences for this recurring visitation
                $generatedOccurrences = $this->generateVisitationOccurrences($visitation, 6);
                $occurrenceCount += count($generatedOccurrences);
                $recurringCount++;
            }
            
            $this->command->info("Generated visitations: {$regularCount} regular, {$recurringCount} recurring with {$occurrenceCount} occurrences");
            
        } catch (\Throwable $e) {
            $this->command->error('Failed to generate care worker visitations: ' . $e->getMessage());
            \Log::error('Failed to generate care worker visitations', ['exception' => $e]);
        }
    }

    /**
     * Create a recurring visitation
     */
    private function createRecurringVisitation($careWorker, $beneficiary, $type, $patternType = 'weekly', $startDate = null)
    {
        if (!$startDate) {
            $startDate = $this->faker->dateTimeBetween('-2 weeks', '+2 weeks');
        }
        
        // Create the visitation
        $visitation = Visitation::factory()->create([
            'care_worker_id' => $careWorker->id,
            'beneficiary_id' => $beneficiary->beneficiary_id,
            'visit_type' => $type,
            'visitation_date' => $startDate->format('Y-m-d'),
            'assigned_by' => User::where('role_id', 2)
                ->where('assigned_municipality_id', $careWorker->assigned_municipality_id)
                ->first()->id ?? User::where('role_id', 1)->first()->id
        ]);
        
        // Create a recurring pattern for this visitation
        // For weekly pattern, choose 1-2 days of the week
        $dayOfWeek = null;
        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        if ($patternType === 'weekly') {
            // Generate 1-2 random days of the week
            $dayCount = $this->faker->randomElement([1, 2]);
            if ($dayCount === 1) {
                // Just use a single day of the week (0=Sunday to 6=Saturday)
                $dayOfWeek = $this->faker->numberBetween(1, 5); // Monday to Friday
            } else {
                // Use multiple days, comma-separated
                $days = $this->faker->randomElements(range(1, 5), 2); // 2 days between Monday and Friday
                sort($days); // Keep them in order
                $dayOfWeek = implode(',', $days);
            }
        } else {
            // For monthly, use the day of month from the start date
            $dayOfWeek = $startDate->format('j'); // Day of month without leading zeros
        }
        
        // Create the recurring pattern
        $recurrenceEnd = $this->faker->dateTimeBetween('+2 months', '+6 months')->format('Y-m-d');
        
        RecurringPattern::create([
            'visitation_id' => $visitation->visitation_id,
            'pattern_type' => $patternType,
            'day_of_week' => $dayOfWeek, // Used for both weekly and monthly patterns
            'recurrence_end' => $recurrenceEnd
        ]);
        
        return $visitation;
    }

    /**
     * Generate occurrences for a recurring visitation
     */
    private function generateVisitationOccurrences($visitation, $months = 3)
    {
        // Check if this is a recurring visitation
        $pattern = RecurringPattern::where('visitation_id', $visitation->visitation_id)->first();
        if (!$pattern) {
            return [];
        }
        
        $occurrences = [];
        $startDate = Carbon::parse($visitation->visitation_date);
        $endDate = $pattern->recurrence_end ? 
                Carbon::parse($pattern->recurrence_end) : 
                $startDate->copy()->addMonths($months);
        
        // Generate based on pattern type
        if ($pattern->pattern_type === 'weekly') {
            // Parse day_of_week string into array of integers
            $daysOfWeek = [];
            if (strpos($pattern->day_of_week, ',') !== false) {
                $daysOfWeek = array_map('intval', explode(',', $pattern->day_of_week));
            } else {
                $daysOfWeek = [intval($pattern->day_of_week)];
            }
            
            $currentDate = $startDate->copy();
            
            // First, set the current date to the first occurrence of each day of week
            foreach ($daysOfWeek as $day) {
                $firstDate = $currentDate->copy();
                // If the current day is not the specified day of week, move to the next occurrence
                if ($firstDate->dayOfWeek !== $day) {
                    $daysToAdd = ($day - $firstDate->dayOfWeek + 7) % 7;
                    if ($daysToAdd === 0) {
                        $daysToAdd = 7; // Move to next week if we're already on this day
                    }
                    $firstDate->addDays($daysToAdd);
                }
                
                // Create occurrences for this day of week until the end date
                $dateIterator = $firstDate->copy();
                while ($dateIterator <= $endDate) {
                    // Create the occurrence
                    $occurrence = VisitationOccurrence::create([
                        'visitation_id' => $visitation->visitation_id,
                        'occurrence_date' => $dateIterator->format('Y-m-d'),
                        'start_time' => $visitation->start_time,
                        'end_time' => $visitation->end_time,
                        'status' => $dateIterator->isPast() ? 
                            $this->faker->randomElement(['completed', 'completed', 'canceled']) : 'scheduled',
                        'notes' => $dateIterator->isPast() ? $this->faker->optional(0.7)->sentence() : null
                    ]);
                    
                    $occurrences[] = $occurrence->occurrence_id;
                    
                    // Move to the next week
                    $dateIterator->addWeek();
                }
            }
            
        } elseif ($pattern->pattern_type === 'monthly') {
            $dayOfMonth = intval($pattern->day_of_week); // For monthly, this is the day of month
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                // Ensure the day exists in the current month (handle cases like 31st in Feb)
                $daysInMonth = $currentDate->daysInMonth;
                $validDay = min($dayOfMonth, $daysInMonth);
                
                // Set to the correct day of the month
                $currentDate->day = $validDay;
                
                // Only create an occurrence if we're still in range
                if ($currentDate <= $endDate) {
                    // Create the occurrence
                    $occurrence = VisitationOccurrence::create([
                        'visitation_id' => $visitation->visitation_id,
                        'occurrence_date' => $currentDate->format('Y-m-d'),
                        'start_time' => $visitation->start_time,
                        'end_time' => $visitation->end_time,
                        'status' => $currentDate->isPast() ? 
                            $this->faker->randomElement(['completed', 'completed', 'canceled']) : 'scheduled',
                        'notes' => $currentDate->isPast() ? $this->faker->optional(0.7)->sentence() : null
                    ]);
                    
                    $occurrences[] = $occurrence->occurrence_id;
                }
                
                // Move to the next month
                $currentDate->addMonth();
            }
        }
        
        return $occurrences;
    }




    /**
     * Generate medication schedules for beneficiaries
     */
    private function generateMedicationSchedules()
    {
        $this->command->info('Seeding medication schedules...');
        
        try {
            // Get all beneficiaries
            $beneficiaries = Beneficiary::all();
            
            if ($beneficiaries->isEmpty()) {
                $this->command->error("No beneficiaries found for medication schedules");
                return;
            }
            
            $medicationCount = 0;
            
            // For each beneficiary, create 2-5 medications
            foreach ($beneficiaries as $beneficiary) {
                // Determine how many medications this beneficiary takes (age-weighted distribution)
                $age = Carbon::parse($beneficiary->birthdate)->age;
                $schedulesToCreate = $this->getScheduleCountByAge($age);
                
                // Track used medications to prevent duplicates
                $usedMedications = [];
                
                for ($i = 0; $i < $schedulesToCreate; $i++) {
                    // Create medication using the factory
                    $medicationSchedule = \App\Models\MedicationSchedule::factory()
                        ->make([
                            'beneficiary_id' => $beneficiary->beneficiary_id
                        ]);
                    
                    // Skip if we've already assigned this medication to this beneficiary
                    if (in_array($medicationSchedule->medication_name, $usedMedications)) {
                        // Try to create a different medication up to 3 times
                        $attempts = 0;
                        $uniqueFound = false;
                        
                        while ($attempts < 3 && !$uniqueFound) {
                            $newMedicationSchedule = \App\Models\MedicationSchedule::factory()->make([
                                'beneficiary_id' => $beneficiary->beneficiary_id
                            ]);
                            
                            if (!in_array($newMedicationSchedule->medication_name, $usedMedications)) {
                                $medicationSchedule = $newMedicationSchedule;
                                $uniqueFound = true;
                            }
                            $attempts++;
                        }
                        
                        // If we still couldn't find a unique medication, skip this iteration
                        if (!$uniqueFound) {
                            continue;
                        }
                    }
                    
                    // Track this medication as used
                    $usedMedications[] = $medicationSchedule->medication_name;
                    
                    // Save the medication schedule
                    $medicationSchedule->save();
                    
                    $medicationCount++;
                }
            }
            
            $this->command->info("Created {$medicationCount} medication schedules for beneficiaries");
            
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed medication schedules: ' . $e->getMessage());
            \Log::error('Failed to seed medication schedules', ['exception' => $e]);
        }
    }

    /**
     * Get appropriate medication schedule count based on beneficiary age
     */
    private function getScheduleCountByAge($age)
    {
        if ($age < 60) {
            return $this->faker->numberBetween(1, 3); // Younger elderly take fewer medications
        } elseif ($age < 70) {
            return $this->faker->numberBetween(2, 4); // Middle elderly
        } else {
            return $this->faker->numberBetween(3, 5); // Older elderly typically take more medications
        }
    }

    /**
     * Generate emergency notices and service requests
     */
    private function generateEmergencyAndServiceRequests()
    {
        $this->command->info('Creating emergency notices and service requests...');
        
        try {
            // Create emergency types if they don't exist yet (should be created by migrations)
            if (EmergencyType::count() == 0) {
                $this->command->info('Creating emergency types...');
                DB::table('emergency_types')->insert([
                    ['name' => 'Medical Emergency', 'color_code' => '#dc3545', 'description' => 'Urgent medical situations requiring immediate attention', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Fall Incident', 'color_code' => '#fd7e14', 'description' => 'Falls resulting in injury or requiring assistance', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Medication Issue', 'color_code' => '#6f42c1', 'description' => 'Problems with medication administration or adverse reactions', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Mental Health Crisis', 'color_code' => '#20c997', 'description' => 'Acute mental health episodes requiring intervention', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Other Emergency', 'color_code' => '#6c757d', 'description' => 'Other emergency situations not categorized above', 'created_at' => now(), 'updated_at' => now()],
                ]);
            }
            
            // Create service request types if they don't exist yet (should be created by migrations)
            if (ServiceRequestType::count() == 0) {
                $this->command->info('Creating service request types...');
                DB::table('service_request_types')->insert([
                    ['name' => 'Home Care Visit', 'color_code' => '#0d6efd', 'description' => 'Additional home care services', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Transportation', 'color_code' => '#198754', 'description' => 'Transportation assistance', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Medical Appointments', 'color_code' => '#6610f2', 'description' => 'Assistance with medical appointment visits', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Meal Delivery', 'color_code' => '#fd7e14', 'description' => 'Delivery of prepared meals', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Other Service', 'color_code' => '#6c757d', 'description' => 'Other service requests not categorized above', 'created_at' => now(), 'updated_at' => now()],
                ]);
            }
            
            // Create emergency notices (15 total - 5 of each status)
            $this->command->info('Creating emergency notices...');
            
            // Create 5 new emergency notices
            EmergencyNotice::factory()->count(5)->asNew()->create();
            
            // Create 5 in-progress emergency notices
            EmergencyNotice::factory()->count(5)->inProgress()->create();
            
            // Create 5 resolved emergency notices
            EmergencyNotice::factory()->count(5)->state(function (array $attributes) {
                $created_at = $this->faker->dateTimeBetween('-3 months', '-1 week');
                $read_at = $this->faker->dateTimeBetween($created_at, $created_at->copy()->addDays(2));
                $action_taken_at = $this->faker->dateTimeBetween($read_at, $read_at->copy()->addDays(3));
                
                return [
                    'status' => 'resolved',
                    'read_status' => true,
                    'read_at' => $read_at,
                    'assigned_to' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id,
                    'action_type' => 'resolved',
                    'action_taken_by' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id,
                    'action_taken_at' => $action_taken_at,
                    'created_at' => $created_at,
                    'updated_at' => $action_taken_at,
                ];
            })->create();
            
            // Create service requests (15 total - distributed by status)
            $this->command->info('Creating service requests...');
            
            // Create 5 new service requests
            ServiceRequest::factory()->count(5)->asNew()->create();
            
            // Create 3 approved service requests
            ServiceRequest::factory()->count(3)->state(function (array $attributes) {
                $created_at = $this->faker->dateTimeBetween('-3 months', '-1 week');
                $read_at = $this->faker->dateTimeBetween($created_at, $created_at->copy()->addDays(2));
                $action_taken_at = $this->faker->dateTimeBetween($read_at, $read_at->copy()->addDays(1));
                
                return [
                    'status' => 'approved',
                    'read_status' => true,
                    'read_at' => $read_at,
                    'action_type' => 'approved',
                    'care_worker_id' => User::where('role_id', 3)->inRandomOrder()->first()->id,
                    'action_taken_by' => User::whereIn('role_id', [1, 2])->inRandomOrder()->first()->id,
                    'action_taken_at' => $action_taken_at,
                    'created_at' => $created_at,
                    'updated_at' => $action_taken_at,
                    'service_date' => $this->faker->dateTimeBetween('+1 day', '+2 weeks')->format('Y-m-d'),
                ];
            })->create();
            
            // Create 3 rejected service requests
            ServiceRequest::factory()->count(3)->state(function (array $attributes) {
                $created_at = $this->faker->dateTimeBetween('-3 months', '-1 week');
                $read_at = $this->faker->dateTimeBetween($created_at, $created_at->copy()->addDays(2));
                $action_taken_at = $this->faker->dateTimeBetween($read_at, $read_at->copy()->addDays(1));
                
                return [
                    'status' => 'rejected',
                    'read_status' => true,
                    'read_at' => $read_at,
                    'action_type' => 'rejected',
                    'care_worker_id' => null,
                    'action_taken_by' => User::whereIn('role_id', [1, 2])->inRandomOrder()->first()->id,
                    'action_taken_at' => $action_taken_at,
                    'created_at' => $created_at,
                    'updated_at' => $action_taken_at,
                ];
            })->create();
            
            // Create 4 completed service requests
            ServiceRequest::factory()->count(4)->state(function (array $attributes) {
                $created_at = $this->faker->dateTimeBetween('-3 months', '-2 weeks');
                $read_at = $this->faker->dateTimeBetween($created_at, $created_at->copy()->addDays(2));
                $action_taken_at = $this->faker->dateTimeBetween($read_at, $read_at->copy()->addDays(1));
                $service_date = $this->faker->dateTimeBetween('-2 weeks', '-2 days')->format('Y-m-d');
                
                return [
                    'status' => 'completed',
                    'read_status' => true,
                    'read_at' => $read_at,
                    'action_type' => 'completed',
                    'care_worker_id' => User::where('role_id', 3)->inRandomOrder()->first()->id,
                    'action_taken_by' => User::whereIn('role_id', [1, 2])->inRandomOrder()->first()->id,
                    'action_taken_at' => $action_taken_at,
                    'created_at' => $created_at,
                    'updated_at' => $this->faker->dateTimeBetween($action_taken_at, 'now'),
                    'service_date' => $service_date,
                ];
            })->create();
            
            // Create updates for emergency notices - several for each notice
            $emergencyNotices = EmergencyNotice::all();
            foreach ($emergencyNotices as $notice) {
                // Create 1-3 updates per notice
                $updateCount = $this->faker->numberBetween(1, 3);
                for ($i = 0; $i < $updateCount; $i++) {
                    EmergencyUpdate::factory()->create([
                        'notice_id' => $notice->notice_id,
                        'created_at' => $this->faker->dateTimeBetween($notice->created_at, now()),
                    ]);
                }
            }
            
            // Create updates for service requests - several for each request
            $serviceRequests = ServiceRequest::all();
            foreach ($serviceRequests as $request) {
                // Create 1-3 updates per request
                $updateCount = $this->faker->numberBetween(1, 3);
                for ($i = 0; $i < $updateCount; $i++) {
                    ServiceRequestUpdate::factory()->create([
                        'service_request_id' => $request->service_request_id,
                        'created_at' => $this->faker->dateTimeBetween($request->created_at, now()),
                    ]);
                }
            }
            
            $this->command->info('Emergency notices and service requests created successfully!');
            
        } catch (\Throwable $e) {
            $this->command->error('Failed to generate emergency notices and service requests: ' . $e->getMessage());
            \Log::error('Failed to generate emergency notices and service requests', ['exception' => $e]);
        }
    }

    private function generateExpenseTrackerData()
    {
        \Log::info('Generating expense tracker data...');
        
        // Generate expenses - create a reasonable number for a small organization
        \App\Models\Expense::factory()->count(40)->create();
        
        // Generate budget allocations - create 12 months of history plus a few future months
        \App\Models\BudgetAllocation::factory()->count(15)->create();
        
        \Log::info('Expense tracker data generation complete');
    }

}

