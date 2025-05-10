<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\MobileUser;
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
        $this->faker = Factory::create();
    }
    
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Generate portal accounts first (these will be linked to beneficiaries)
        PortalAccount::factory()->count(20)->create();

        // 2. Generate users with different roles
        User::factory()->count(5)->create(['role_id' => 1]); // Admins
        
        // Generate care managers first so we can assign care workers to them
        $careManagers = [];
        for ($i = 0; $i < 5; $i++) {
            $careManagers[] = User::factory()->create(['role_id' => 2]);
        }
        
        // Create care workers with assigned care managers
        $careWorkers = [];
        for ($i = 0; $i < 5; $i++) {
            // Assign each care worker to a random care manager
            $randomCareManager = $careManagers[array_rand($careManagers)];
            
            $careWorkers[] = User::factory()->create([
                'role_id' => 3,
                'assigned_care_manager_id' => $randomCareManager->id
            ]);
        }

        // 3. Create general care plans with all related data
        $generalCarePlans = [];
        for ($i = 1; $i <= 15; $i++) {  // Use condition +1 for testing. Reset back when done with testing with the portal account and beneficiary seeding way below
            // Get a random care worker
            $careWorkerId = $careWorkers[array_rand($careWorkers)]->id;
            
            // Create the general care plan with a specific ID
            $generalCarePlan = GeneralCarePlan::create([
                'general_care_plan_id' => $i,
                'review_date' => Carbon::now()->addMonths(6),
                'emergency_plan' => 'Standard emergency procedures',
                'care_worker_id' => $careWorkerId,
                'created_at' => now(),
                'updated_at' => now()
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
            
            // Create medications for this general care plan
            foreach (range(1, 3) as $medicationIndex) {
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
            
            // Create care worker responsibilities for this general care plan
            foreach (range(1, 5) as $index) {
                CareWorkerResponsibility::factory()->create([
                    'general_care_plan_id' => $i,
                    'care_worker_id' => $careWorkerId,
                ]);
            }
            
            $generalCarePlans[] = $generalCarePlan;
        }

        // Rest of your seeder remains the same...
        // 4. Now create beneficiaries with references to existing general care plans
        $beneficiaries = [];
        for ($i = 0; $i < 10; $i++) {
            $beneficiary = Beneficiary::factory()->create([
                'general_care_plan_id' => $i + 1  // Reference the existing GCP
            ]);
            
            // Create family members for each beneficiary (1-3 members)
            $familyMemberCount = rand(1, 3);
            FamilyMember::factory($familyMemberCount)
                ->forBeneficiary($beneficiary->beneficiary_id)
                ->create();
                
            $beneficiaries[] = $beneficiary;
        }
        
        // 5. Create additional beneficiaries with fully established care plans
        $additionalBeneficiaries = [];
        for ($i = 0; $i < 5; $i++) {
            $beneficiary = Beneficiary::factory()->create([
                'general_care_plan_id' => $i + 11  // Start from 11 since we used 1-10 above
            ]);
            
            $familyMemberCount = rand(1, 3);
            FamilyMember::factory($familyMemberCount)
                ->forBeneficiary($beneficiary->beneficiary_id)
                ->create();
                
            $additionalBeneficiaries[] = $beneficiary;
        }
        
         // 6. Generate weekly care plans with realistic intervention data
         $this->generateRealisticWeeklyCarePlans($careWorkers, $beneficiaries);

        // 7. Generate notifications
        $this->generateNotifications();

        // 8. Generate conversations and messages
        $this->generateConversations();

         // 9. Generate scheduling data (appointments, visitations, medication schedules)
        $this->generateSchedulingData();

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
        
        // Define the date range - from January 1, 2024 to May 7, 2025
        $startDate = Carbon::createFromDate(2024, 1, 1);
        $endDate = Carbon::createFromDate(2025, 5, 7);
        
        \Log::info("Generating weekly care plans from {$startDate->toDateString()} to {$endDate->toDateString()}");
        
        $wcpCount = 0;
        
        // Create a WCP for each beneficiary for each week in the date range
        foreach ($beneficiaries as $beneficiary) {
            $currentDate = $startDate->copy();
            
            // For each week in the range
            while ($currentDate->lte($endDate)) {
                // Get a random care worker for this WCP
                $careWorker = $careWorkers[array_rand($careWorkers)];
                
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
                
                // Pick a random day during the current week (0-6 days from the start of the week)
                $randomDayOffset = rand(0, 6);
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
                
                // Move to next week
                $currentDate->addWeek();
            }
            
            \Log::info("Generated weekly care plans for beneficiary ID: {$beneficiary->beneficiary_id}");
        }
        
        \Log::info("Created a total of {$wcpCount} weekly care plans");
        
        // Add some overlapping plans for testing purposes (same date, different care workers)
        $this->createOverlappingCarePlans($careWorkers, $beneficiaries);
    }

    /**
     * Create some overlapping care plans for testing purposes
     */
    private function createOverlappingCarePlans($careWorkers, $beneficiaries)
    {
        // Use specific dates for overlapping plans
        $overlappingDates = [
            '2024-03-15',
            '2024-04-15',
            '2024-05-15',
            '2025-01-10',
            '2025-02-20',
        ];
        
        foreach ($overlappingDates as $date) {
            // For each date, create 2 additional plans for the same beneficiary
            if (count($careWorkers) >= 3 && count($beneficiaries) > 0) {
                $beneficiary = $beneficiaries[array_rand($beneficiaries)];
                
                // Create 2 additional plans with different care workers
                for ($i = 0; $i < 2; $i++) {
                    $careWorker = $careWorkers[$i];
                    
                    // Create vital signs
                    $vitalSigns = VitalSigns::factory()->create([
                        'created_by' => $careWorker->id,
                    ]);
                    
                    // Select 0-2 illnesses randomly
                    $selectedIllnesses = $this->faker->randomElements(
                        $commonIllnesses ?? ['Cold', 'Fever', 'UTI'],
                        $this->faker->numberBetween(0, 2)
                    );
                    
                    // Create the Weekly Care Plan WITH photo_path
                    $weeklyCarePlan = WeeklyCarePlan::create([
                        'beneficiary_id' => $beneficiary->beneficiary_id,
                        'care_worker_id' => $careWorker->id,
                        'vital_signs_id' => $vitalSigns->vital_signs_id,
                        'date' => $date,
                        'assessment' => $this->getRealisticAssessment(),
                        'illnesses' => !empty($selectedIllnesses) ? json_encode($selectedIllnesses) : null,
                        'photo_path' => 'weekly_care_plans/photos/seed_photo_' . rand(1, 10) . '.jpg',
                        'evaluation_recommendations' => $this->getRealisticRecommendation(),
                        'created_by' => $careWorker->id,
                        'updated_by' => $careWorker->id,
                        'created_at' => $date,
                        'updated_at' => $date
                    ]);
                    
                    \Log::info("Created overlapping care plan for date {$date}, beneficiary {$beneficiary->beneficiary_id}, care worker {$careWorker->id}");
                }
            }
        }
    }

    private function getRealisticAssessment()
    {
        $assessments = [
            "Beneficiary appears alert and oriented to time, place, and person. Vital signs are within normal limits. Reports mild joint pain in knees, rating 3/10 on pain scale. Medication compliance is good. No signs of illness or infection noted.",
            
            "Beneficiary is experiencing some shortness of breath upon minimal exertion. Blood pressure is slightly elevated at 145/90. Reports difficulty sleeping due to back discomfort. Needs assistance with bathing and dressing.",
            
            "Assessment shows mild cognitive decline, with some short-term memory issues. Beneficiary can still perform most ADLs independently. Mood appears stable. Appetite is good but reports occasional difficulty chewing harder foods.",
            
            "Beneficiary reports increased fatigue and dizziness when standing. Blood pressure drops by 15mmHg upon standing, indicating possible orthostatic hypotension. No falls reported, but increased risk noted.",
            
            "Beneficiary shows signs of depression with decreased appetite and social withdrawal. Reports feeling 'worthless' and having little energy. Sleep disturbances noted with early morning awakening.",
            
            "Physical assessment shows good mobility using walker. Skin is intact with no pressure areas. Edema noted in both ankles, +2. Breathing is unlabored with clear lung sounds."
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
            
            "Continue weekly blood pressure monitoring. Recommend compression stockings for lower extremity edema. Evaluate effectiveness of pain management strategies at next visit. Encourage socialization through day program participation."
        ];
        
        return $recommendations[array_rand($recommendations)];
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


    private function generateNotifications()
    {
        // Generate notifications for beneficiaries - keeping this part
        $beneficiaries = Beneficiary::take(5)->get();
        foreach ($beneficiaries as $beneficiary) {
            Notification::factory()
                ->count(rand(2, 5))
                ->forBeneficiary($beneficiary->beneficiary_id)
                ->create();
        }
        
        // Generate notifications for family members - keeping this part
        $familyMembers = FamilyMember::take(5)->get();
        foreach ($familyMembers as $familyMember) {
            Notification::factory()
                ->count(rand(2, 4))
                ->forFamilyMember($familyMember->family_member_id)
                ->create();
        }
        
        // UPDATED: Generate notifications for ALL COSE staff instead of just taking 5
        $staffMembers = User::where('role_id', '<=', 3)->get(); // Get ALL COSE staff
        
        $notificationTypes = [
            // Admin notifications (role_id = 1)
            1 => [
                'System Update' => 'The system has been updated with new features.',
                'Security Alert' => 'A new security patch has been applied.',
                'New User Registration' => 'A new user has registered in the system.',
                'Data Backup Complete' => 'Automatic data backup has completed successfully.',
                'Performance Report' => 'Monthly performance report is now available.'
            ],
            // Care Manager notifications (role_id = 2)
            2 => [
                'New Case Assigned' => 'You have been assigned a new case to manage.',
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
            }
        }
    }

    /**
     * Generate conversations and messages between users following role hierarchy rules
     */
    private function generateConversations()
    {
        // Get users by role
        $admins = User::where('role_id', 1)->get();
        $careManagers = User::where('role_id', 2)->get();
        $careWorkers = User::where('role_id', 3)->get();
        
        // Get some beneficiaries and family members for conversations
        $beneficiaries = Beneficiary::take(5)->get();
        $familyMembers = FamilyMember::take(5)->get();
        
        // ================ PRIVATE CONVERSATIONS ================
        
        // 1. Create conversations for Admins (can only talk to Care Managers)
        foreach ($admins as $admin) {
            // Create 2 private conversations with random Care Managers
            for ($i = 0; $i < 2; $i++) {
                if ($careManagers->count() > 0) {
                    $randomCareManager = $careManagers->random();
                    $this->createPrivateConversation($admin, $randomCareManager);
                }
            }
        }
        
        // 2. Create conversations for Care Managers (can talk to Admins, other Care Managers, and Care Workers)
        foreach ($careManagers as $careManager) {
            // Create 3 private conversations - with Admin, other Care Manager, and Care Worker
            
            // With Admin
            if ($admins->count() > 0) {
                $randomAdmin = $admins->random();
                // Skip if conversation already exists from admin's loop
                if (!$this->conversationExistsBetween($careManager->id, 'cose_staff', $randomAdmin->id, 'cose_staff')) {
                    $this->createPrivateConversation($careManager, $randomAdmin);
                }
            }
            
            // With another Care Manager
            $otherCareManagers = $careManagers->where('id', '!=', $careManager->id);
            if ($otherCareManagers->count() > 0) {
                $randomOtherCareManager = $otherCareManagers->random();
                $this->createPrivateConversation($careManager, $randomOtherCareManager);
            }
            
            // With Care Worker
            if ($careWorkers->count() > 0) {
                $randomCareWorker = $careWorkers->random();
                $this->createPrivateConversation($careManager, $randomCareWorker);
            }
        }
        
        // 3. Create conversations for Care Workers (can only talk to Care Managers)
        foreach ($careWorkers as $careWorker) {
            // Create 1 conversation with a Care Manager if not already created
            if ($careManagers->count() > 0) {
                $randomCareManager = $careManagers->random();
                // Skip if conversation already exists from care manager's loop
                if (!$this->conversationExistsBetween($careWorker->id, 'cose_staff', $randomCareManager->id, 'cose_staff')) {
                    $this->createPrivateConversation($careWorker, $randomCareManager);
                }
            }
            
            // Create 1-2 conversations with beneficiaries and family members
            if ($beneficiaries->count() > 0) {
                $randomBeneficiary = $beneficiaries->random();
                $this->createPrivateConversation($careWorker, $randomBeneficiary, 'beneficiary');
            }
            
            if ($familyMembers->count() > 0) {
                $randomFamilyMember = $familyMembers->random();
                $this->createPrivateConversation($careWorker, $randomFamilyMember, 'family_member');
            }
        }
        
        // ================ GROUP CONVERSATIONS ================
        
        // 1. Create group chats for Admins (with only Care Managers)
        foreach ($admins as $admin) {
            if ($careManagers->count() >= 2) {
                $this->createGroupChat($admin, $careManagers->random(rand(2, min(4, $careManagers->count())))->all());
            }
        }
        
        // 2. Create group chats for Care Managers:
        // a) With Admins only
        // b) With other Care Managers only
        // c) With Care Workers only (to avoid mixing admins and care workers)
        foreach ($careManagers as $careManager) {
            // Group with Admins (if enough admins exist)
            if ($admins->count() >= 1) {
                $groupParticipants = $admins->random(min(2, $admins->count()))->all();
                $otherCareManagers = $careManagers->where('id', '!=', $careManager->id)->random(min(2, $careManagers->count() - 1))->all();
                $this->createGroupChat($careManager, array_merge($groupParticipants, $otherCareManagers));
            }
            
            // Group with Care Workers only
            if ($careWorkers->count() >= 2) {
                $this->createGroupChat($careManager, $careWorkers->random(rand(2, min(4, $careWorkers->count())))->all());
            }
        }
        
        // 3. Create group chats for Care Workers (with Care Managers and clients)
        foreach ($careWorkers as $careWorker) {
            // One group with Care Manager, beneficiary and family member
            if ($careManagers->count() > 0 && $beneficiaries->count() > 0 && $familyMembers->count() > 0) {
                $participants = [
                    ['object' => $careManagers->random(), 'type' => 'cose_staff'],
                    ['object' => $beneficiaries->random(), 'type' => 'beneficiary'],
                    ['object' => $familyMembers->random(), 'type' => 'family_member']
                ];
                
                $this->createGroupChatWithMixedParticipants($careWorker, $participants);
            }
        }
        
        // Log how many conversations were created
        $totalConversations = Conversation::count();
        $totalMessages = Message::count();
        $totalAttachments = MessageAttachment::count();
        
        \Log::info("Generated {$totalConversations} conversations with {$totalMessages} messages and {$totalAttachments} attachments.");
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
        
        // Create messages in this conversation from both participants
        $messageCount = rand(3, 10);
        
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
        // Create a group chat
        $groupChat = Conversation::factory()->groupChat()->create([
            'name' => 'Team ' . \Faker\Factory::create()->word . ' Chat',
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
     * Create a group chat with mixed participant types
     */
    private function createGroupChatWithMixedParticipants($creator, $participants)
    {
        // Create a group chat
        $groupChat = Conversation::factory()->groupChat()->create([
            'name' => 'Team ' . \Faker\Factory::create()->word . ' Support',
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
        
        // Generate 5-15 messages in the group chat from various participants
        $messageCount = rand(5, 15);
        
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
        if (rand(1, 5) == 1) { // 20% chance
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

    private function getRandomAssessment()
    {
        $assessments = [
            "Beneficiary shows improved mobility compared to last week. Maintains good spirits and is engaging well with care activities.",
            "Cognitive function stable; some memory issues persist but responds well to memory exercises. Appetite has improved.",
            "Sleep patterns remain disrupted. Requires additional assistance with ADLs. Pain levels manageable with current medication.",
            "Mood fluctuations noted this week. Physical strength improving gradually with exercise regimen. Social engagement increased.",
            "Beneficiary experienced mild respiratory difficulties but recovered well. Hydration and nutrition intake adequate.",
            "Notable progress in self-care abilities. Beneficiary participated actively in all therapy sessions. Family reports satisfaction with care.",
            "Some anxiety observed when discussing medical appointments. Mobility has improved with the new assistive device.",
            "Beneficiary appears more energetic this week. Completed all recommended exercises. Medication compliance has improved.",
            "Blood pressure readings slightly elevated. Will monitor closely. Otherwise, beneficiary is engaging well in daily activities.",
            "Beneficiary expressed interest in community activities. Physical condition stable. Requires ongoing support with meal preparation."
        ];
        
        return $assessments[array_rand($assessments)];
    }

    private function getRandomRecommendation()
    {
        $recommendations = [
            "Continue current mobility exercises and gradually increase intensity. Follow up on referral to physical therapy.",
            "Maintain memory exercises daily. Consider adding new cognitive activities to prevent boredom. Review medication schedule with doctor.",
            "Implement suggested sleep hygiene practices. Consider adjusting evening routine to improve sleep quality. Follow up on pain management.",
            "Encourage participation in social group activities twice weekly. Continue monitoring mood and report significant changes.",
            "Monitor respiratory function closely. Ensure proper hydration and nutrition intake. Follow up with pulmonary specialist as scheduled.",
            "Continue current self-care regimen. Celebrate progress with beneficiary. Schedule follow-up with family to discuss ongoing support.",
            "Provide additional emotional support before medical appointments. Continue with current mobility assistance devices.",
            "Maintain current exercise regimen. Provide positive reinforcement for medication compliance. Consider adding new activities.",
            "Schedule follow-up to monitor blood pressure. Review dietary recommendations. Continue with current social activities.",
            "Support interest in community activities by providing transportation options. Continue meal preparation support while encouraging participation."
        ];
        
        return $recommendations[array_rand($recommendations)];
    }

    /**
     * Generate scheduling system data (appointments, visitations, medication schedules)
     */
    private function generateSchedulingData()
    {
        \Log::info("Generating scheduling system data...");
        
        // Create appointment types
        $this->createAppointmentTypes();
        
        // Generate internal appointments with participants
        $this->generateInternalAppointments();
        
        // Generate care worker visitations
        $this->generateCareWorkerVisitations();
        
        // Generate medication schedules
        $this->generateMedicationSchedules();
        
        \Log::info("Finished generating scheduling system data");
    }

    /**
     * Create appointment types
     */
    private function createAppointmentTypes()
    {
        $appointmentTypes = [
            ['type_name' => 'Skills Enhancement Training', 'color_code' => '#4e73df', 'description' => 'Training sessions to enhance caregiver skills'],
            ['type_name' => 'Quarterly Feedback Sessions', 'color_code' => '#1cc88a', 'description' => 'Regular feedback sessions held quarterly'],
            ['type_name' => 'Municipal Development Council (MDC) Participation', 'color_code' => '#36b9cc', 'description' => 'Participation in MDC meetings'],
            ['type_name' => 'Municipal Local Health Board Meeting', 'color_code' => '#f6c23e', 'description' => 'Meetings with the Municipal Local Health Board'],
            ['type_name' => 'LIGA Meeting', 'color_code' => '#e74a3b', 'description' => 'LIGA organization meetings'],
            ['type_name' => 'Referrals to MHO', 'color_code' => '#6f42c1', 'description' => 'Referral appointments with Municipal Health Officer'],
            ['type_name' => 'Assessment and Review of Care Needs', 'color_code' => '#fd7e14', 'description' => 'Assessment and review of beneficiary care needs'],
            ['type_name' => 'General Care Plan Finalization', 'color_code' => '#20c997', 'description' => 'Meetings to finalize general care plans'],
            ['type_name' => 'Project Team Meeting', 'color_code' => '#5a5c69', 'description' => 'Internal project team meetings'],
            ['type_name' => 'Mentoring Session', 'color_code' => '#858796', 'description' => 'Mentoring sessions for care workers'],
            ['type_name' => 'Other Appointment', 'color_code' => '#a435f0', 'description' => 'Other appointment types not covered above'],
        ];
        
        foreach ($appointmentTypes as $type) {
            \App\Models\AppointmentType::create($type);
        }
        
        \Log::info("Created appointment types");
    }

    /**
     * Generate internal appointments and participants
     */
    private function generateInternalAppointments()
    {
        // Get staff users
        $admins = \App\Models\User::where('role_id', 1)->get();
        $careManagers = \App\Models\User::where('role_id', 2)->get();
        $careWorkers = \App\Models\User::where('role_id', 3)->take(10)->get();
        $beneficiaries = \App\Models\Beneficiary::take(10)->get();
        
        // Get appointment types
        $appointmentTypes = \App\Models\AppointmentType::all();
        
        // Generate 30 appointments spread across past, present and future
        $startDate = Carbon::now()->subMonths(1);
        $endDate = Carbon::now()->addMonths(2);
        
        for ($i = 0; $i < 30; $i++) {
            // Random date between start and end
            $appointmentDate = $this->faker->dateTimeBetween($startDate, $endDate)->format('Y-m-d');
            
            // Random appointment type
            $appointmentType = $appointmentTypes->random();
            
            // Determine if this is a flexible time appointment (20% chance)
            $isFlexibleTime = $this->faker->boolean(20);
            $startTime = $isFlexibleTime ? null : $this->faker->dateTimeBetween('08:00', '15:00')->format('H:i:s');
            $endTime = $isFlexibleTime ? null : Carbon::createFromFormat('H:i:s', $startTime)->addHours($this->faker->numberBetween(1, 3))->format('H:i:s');
            
            // Create appointment title
            $title = $appointmentType->type_name;
            if ($appointmentType->type_name === 'Other Appointment') {
                $otherTypes = ['Staff Training', 'Program Evaluation', 'Budget Meeting', 'Community Outreach', 'Stakeholder Meeting'];
                $otherTypeDetails = $otherTypes[array_rand($otherTypes)];
                $title .= ": " . $otherTypeDetails;
            } else {
                $otherTypeDetails = null;
            }
            
            // Create the appointment
            $appointment = \App\Models\Appointment::create([
                'appointment_type_id' => $appointmentType->appointment_type_id,
                'title' => $title,
                'description' => $this->faker->paragraph(2),
                'other_type_details' => $otherTypeDetails,
                'date' => $appointmentDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'is_flexible_time' => $isFlexibleTime,
                'meeting_location' => $this->faker->boolean(70) ? $this->faker->address : 'COSE Office',
                'status' => $appointmentDate < Carbon::now()->format('Y-m-d') ? 
                    $this->faker->randomElement(['completed', 'canceled']) : 
                    'scheduled',
                'notes' => $this->faker->boolean(60) ? $this->faker->paragraph : null,
                'created_by' => $admins->merge($careManagers)->random()->id,
                'updated_by' => $this->faker->boolean(30) ? $admins->merge($careManagers)->random()->id : null,
                'created_at' => Carbon::parse($appointmentDate)->subDays($this->faker->numberBetween(1, 14)),
                'updated_at' => Carbon::now(),
            ]);
            
            // Add 3-8 participants to each appointment
            $participantCount = $this->faker->numberBetween(3, 8);
            $usedParticipantIds = [];
            
            // First participant is always the organizer (from staff)
            $organizer = $admins->merge($careManagers)->random();
            \App\Models\AppointmentParticipant::create([
                'appointment_id' => $appointment->appointment_id,
                'participant_type' => 'cose_user',
                'participant_id' => $organizer->id,
                'is_organizer' => true,
                'created_at' => $appointment->created_at,
                'updated_at' => $appointment->created_at,
            ]);
            $usedParticipantIds[] = 'cose_user_' . $organizer->id;
            
            // Add other participants
            for ($p = 0; $p < $participantCount - 1; $p++) {
                // Determine participant type - weighted distribution
                $rand = $this->faker->numberBetween(1, 100);
                
                if ($rand <= 70) {
                    // Staff participant
                    $staffPool = $admins->merge($careManagers)->merge($careWorkers);
                    $participant = $staffPool->random();
                    $participantType = 'cose_user';
                    $participantId = $participant->id;
                    $participantKey = $participantType . '_' . $participantId;
                    
                    // Skip if this participant already added
                    if (in_array($participantKey, $usedParticipantIds)) {
                        continue;
                    }
                    
                    $usedParticipantIds[] = $participantKey;
                }
                elseif ($rand <= 90) {
                    // Beneficiary participant
                    $participant = $beneficiaries->random();
                    $participantType = 'beneficiary';
                    $participantId = $participant->beneficiary_id;
                    $participantKey = $participantType . '_' . $participantId;
                    
                    // Skip if this participant already added
                    if (in_array($participantKey, $usedParticipantIds)) {
                        continue;
                    }
                    
                    $usedParticipantIds[] = $participantKey;
                }
                else {
                    // Family member participant
                    $beneficiary = $beneficiaries->random();
                    $familyMember = \App\Models\FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)->first();
                    
                    if (!$familyMember) {
                        continue;
                    }
                    
                    $participantType = 'family_member';
                    $participantId = $familyMember->family_member_id;
                    $participantKey = $participantType . '_' . $participantId;
                    
                    // Skip if this participant already added
                    if (in_array($participantKey, $usedParticipantIds)) {
                        continue;
                    }
                    
                    $usedParticipantIds[] = $participantKey;
                }
                
                \App\Models\AppointmentParticipant::create([
                    'appointment_id' => $appointment->appointment_id,
                    'participant_type' => $participantType,
                    'participant_id' => $participantId,
                    'is_organizer' => false,
                    'created_at' => $appointment->created_at,
                    'updated_at' => $appointment->created_at,
                ]);
            }
            
            // Add recurring pattern to 40% of appointments
            if ($this->faker->boolean(40)) {
                \App\Models\RecurringPattern::create([
                    'appointment_id' => $appointment->appointment_id,
                    'pattern_type' => $this->faker->randomElement(['weekly', 'monthly']),
                    'day_of_week' => $this->faker->randomElement(['weekly', 'monthly']) ? $this->faker->numberBetween(0, 6) : null,
                    'recurrence_end' => Carbon::parse($appointmentDate)->addMonths($this->faker->numberBetween(1, 6)),
                    'created_at' => $appointment->created_at,
                    'updated_at' => $appointment->created_at,
                ]);
            }
        }
        
        \Log::info("Created internal appointments with participants");
    }

    /**
     * Generate care worker visitations
     */
    private function generateCareWorkerVisitations()
    {
        // Get care workers and beneficiaries
        $careWorkers = \App\Models\User::where('role_id', 3)->get();
        $beneficiaries = \App\Models\Beneficiary::all();
        $admins = \App\Models\User::where('role_id', 1)->get();
        $careManagers = \App\Models\User::where('role_id', 2)->get();
        
        if ($careWorkers->isEmpty() || $beneficiaries->isEmpty()) {
            \Log::warning("No care workers or beneficiaries found for visitation schedules");
            return;
        }
        
        // Generate 50 visitations
        for ($i = 0; $i < 50; $i++) {
            $careWorker = $careWorkers->random();
            $beneficiary = $beneficiaries->random();
            $assignedBy = $admins->merge($careManagers)->random();
            
            // Random date between -1 month and +2 months
            $visitationDate = $this->faker->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d');
            $dateAssigned = Carbon::parse($visitationDate)->subDays($this->faker->numberBetween(1, 14))->format('Y-m-d');
            
            // Determine if this is a flexible time visitation (30% chance)
            $isFlexibleTime = $this->faker->boolean(30);
            
            $startTime = $isFlexibleTime ? null : $this->faker->dateTimeBetween('08:00', '16:00')->format('H:i:s');
            $endTime = $isFlexibleTime ? null : Carbon::createFromFormat('H:i:s', $startTime)->addHours($this->faker->numberBetween(1, 2))->format('H:i:s');
            
            // Status based on date
            $status = Carbon::parse($visitationDate)->isPast() ?
                $this->faker->randomElement(['completed', 'canceled']) : 'scheduled';
            
            // Confirmation details for completed visitations
            $confirmedOn = ($status === 'completed') ? Carbon::parse($visitationDate)->addHours($this->faker->numberBetween(1, 8)) : null;
            
            $confirmedByBeneficiary = null;
            $confirmedByFamily = null;
            
            if ($status === 'completed') {
                // 50% chance of beneficiary confirmation, 30% chance of family confirmation, 20% chance of neither
                $confirmationType = $this->faker->randomElement(['beneficiary', 'family', 'none', 'beneficiary', 'family', 'beneficiary']);
                
                if ($confirmationType === 'beneficiary') {
                    $confirmedByBeneficiary = $beneficiary->beneficiary_id;
                } elseif ($confirmationType === 'family') {
                    $familyMember = \App\Models\FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)->first();
                    if ($familyMember) {
                        $confirmedByFamily = $familyMember->family_member_id;
                    }
                }
            }
            
            // Create the visitation
            $visitation = \App\Models\Visitation::create([
                'care_worker_id' => $careWorker->id,
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'visit_type' => $this->faker->randomElement(['routine_care_visit', 'service_request', 'emergency_visit']),
                'visitation_date' => $visitationDate,
                'is_flexible_time' => $isFlexibleTime,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'notes' => $this->faker->boolean(70) ? $this->faker->paragraph : null,
                'date_assigned' => $dateAssigned,
                'assigned_by' => $assignedBy->id,
                'status' => $status,
                'confirmed_by_beneficiary' => $confirmedByBeneficiary,
                'confirmed_by_family' => $confirmedByFamily,
                'confirmed_on' => $confirmedOn,
                'created_at' => Carbon::parse($dateAssigned),
                'updated_at' => $confirmedOn ?? Carbon::parse($dateAssigned),
            ]);
            
            // Add recurring pattern to 60% of visitations
            if ($this->faker->boolean(60)) {
                \App\Models\RecurringPattern::create([
                    'visitation_id' => $visitation->visitation_id,
                    'pattern_type' => $this->faker->randomElement(['weekly', 'monthly']),
                    'day_of_week' => $this->faker->randomElement(['weekly', 'monthly']) ? $this->faker->numberBetween(0, 6) : null,
                    'recurrence_end' => Carbon::parse($visitationDate)->addMonths($this->faker->numberBetween(1, 6)),
                    'created_at' => $visitation->created_at,
                    'updated_at' => $visitation->created_at,
                ]);
            }
        }
        
        \Log::info("Created care worker visitations");
    }

    /**
     * Generate medication schedules
     */
    private function generateMedicationSchedules()
    {
        // Get beneficiaries and care staff for created_by field
        $beneficiaries = \App\Models\Beneficiary::all();
        $careStaff = \App\Models\User::whereIn('role_id', [1, 2])->get();
        
        if ($beneficiaries->isEmpty()) {
            \Log::warning("No beneficiaries found for medication schedules");
            return;
        }
        
        $medicationTypes = [
            'tablet', 'capsule', 'liquid', 'injection', 'inhaler', 'topical', 'drops', 'other'
        ];
        
        $medications = [
            'Metformin' => ['dosages' => ['500mg', '850mg', '1000mg'], 'for' => 'Diabetes'],
            'Lisinopril' => ['dosages' => ['5mg', '10mg', '20mg'], 'for' => 'Hypertension'],
            'Atorvastatin' => ['dosages' => ['10mg', '20mg', '40mg'], 'for' => 'High Cholesterol'],
            'Levothyroxine' => ['dosages' => ['25mcg', '50mcg', '75mcg', '100mcg'], 'for' => 'Hypothyroidism'],
            'Albuterol' => ['dosages' => ['2 puffs', '1-2 puffs'], 'for' => 'Asthma/COPD'],
            'Warfarin' => ['dosages' => ['2mg', '5mg', '7.5mg'], 'for' => 'Blood Thinning'],
            'Furosemide' => ['dosages' => ['20mg', '40mg', '80mg'], 'for' => 'Edema/Heart Failure'],
            'Omeprazole' => ['dosages' => ['10mg', '20mg', '40mg'], 'for' => 'Acid Reflux'],
            'Amlodipine' => ['dosages' => ['2.5mg', '5mg', '10mg'], 'for' => 'Hypertension'],
            'Metoprolol' => ['dosages' => ['25mg', '50mg', '100mg'], 'for' => 'Hypertension/Heart Failure'],
            'Sertraline' => ['dosages' => ['25mg', '50mg', '100mg'], 'for' => 'Depression/Anxiety'],
            'Hydrochlorothiazide' => ['dosages' => ['12.5mg', '25mg'], 'for' => 'Hypertension'],
        ];
        
        // For each beneficiary, create 2-5 medications
        foreach ($beneficiaries as $beneficiary) {
            $medicationCount = $this->faker->numberBetween(2, 5);
            
            for ($i = 0; $i < $medicationCount; $i++) {
                // Select a random medication
                $medicationName = array_keys($medications)[$this->faker->numberBetween(0, count($medications) - 1)];
                $medicationInfo = $medications[$medicationName];
                $dosage = $medicationInfo['dosages'][$this->faker->numberBetween(0, count($medicationInfo['dosages']) - 1)];
                
                // Medication type
                $medicationType = $medicationTypes[$this->faker->numberBetween(0, count($medicationTypes) - 1)];
                
                // As needed flag
                $asNeeded = $this->faker->boolean(10); // 10% chance of being as-needed
                
                // Schedule times
                $morningTime = (!$asNeeded && $this->faker->boolean(70)) ? $this->faker->dateTimeBetween('06:00', '09:00')->format('H:i:s') : null;
                $noonTime = (!$asNeeded && $this->faker->boolean(40)) ? $this->faker->dateTimeBetween('11:00', '13:00')->format('H:i:s') : null;
                $eveningTime = (!$asNeeded && $this->faker->boolean(60)) ? $this->faker->dateTimeBetween('16:00', '19:00')->format('H:i:s') : null;
                $nightTime = (!$asNeeded && $this->faker->boolean(30)) ? $this->faker->dateTimeBetween('20:00', '23:00')->format('H:i:s') : null;
                
                // Make sure at least one time is set for non-as-needed medications
                if (!$asNeeded && !$morningTime && !$noonTime && !$eveningTime && !$nightTime) {
                    $morningTime = '08:00:00';
                }
                
                // Food requirements
                $withFoodMorning = $morningTime ? $this->faker->boolean(70) : false;
                $withFoodNoon = $noonTime ? $this->faker->boolean(90) : false;
                $withFoodEvening = $eveningTime ? $this->faker->boolean(80) : false;
                $withFoodNight = $nightTime ? $this->faker->boolean(50) : false;
                
                // Start and end dates
                $startDate = $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d');
                $hasEndDate = $this->faker->boolean(70);
                $endDate = $hasEndDate ? $this->faker->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d') : null;
                
                // Status
                $status = $hasEndDate && $endDate < now() ? 'completed' : 
                        ($this->faker->boolean(90) ? 'active' : 'paused');
                
                // Create the medication schedule
                \App\Models\MedicationSchedule::create([
                    'beneficiary_id' => $beneficiary->beneficiary_id,
                    'medication_name' => $medicationName,
                    'dosage' => $dosage,
                    'medication_type' => $medicationType,
                    'morning_time' => $morningTime,
                    'noon_time' => $noonTime,
                    'evening_time' => $eveningTime,
                    'night_time' => $nightTime,
                    'as_needed' => $asNeeded,
                    'with_food_morning' => $withFoodMorning,
                    'with_food_noon' => $withFoodNoon,
                    'with_food_evening' => $withFoodEvening,
                    'with_food_night' => $withFoodNight,
                    'special_instructions' => $this->faker->boolean(70) ? $this->faker->sentence : null,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $status,
                    'created_by' => $careStaff->random()->id,
                    'created_at' => Carbon::parse($startDate),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
        
        \Log::info("Created medication schedules for beneficiaries");
    }

}