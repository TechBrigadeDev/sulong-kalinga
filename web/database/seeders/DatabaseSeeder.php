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
use Database\Factories\FamilyMemberFactory;

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
            
            // Reset the primary caregiver tracking
            FamilyMemberFactory::resetPrimaryCaregivers();
            
            // Create 1-3 family members for each beneficiary
            foreach ($beneficiaries as $beneficiary) {
                $familyMemberCount = mt_rand(1, 3);
                
                // Create family members for this beneficiary
                $familyMembers = [];
                for ($i = 0; $i < $familyMemberCount; $i++) {
                    $familyMembers[] = FamilyMember::factory()
                        ->forBeneficiary($beneficiary->beneficiary_id)
                        ->create();
                }
                
                // If this beneficiary doesn't yet have a primary caregiver, assign one
                if (!FamilyMemberFactory::hasPrimaryCaregiver($beneficiary->beneficiary_id) && count($familyMembers) > 0) {
                    // Choose the most likely family member to be primary caregiver
                    // Prefer spouse or child if available
                    $primaryIndex = 0;
                    
                    foreach ($familyMembers as $index => $familyMember) {
                        if (in_array($familyMember->relation_to_beneficiary, ['Spouse', 'Child'])) {
                            $primaryIndex = $index;
                            break;
                        }
                    }
                    
                    // Update the selected family member to be the primary caregiver
                    $familyMembers[$primaryIndex]->update(['is_primary_caregiver' => true]);
                    
                    // Mark this beneficiary as having a primary caregiver
                    FamilyMemberFactory::setPrimaryCaregiver($beneficiary->beneficiary_id);
                }
            }
            
            $this->command->info('Family members seeded successfully.');
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed family members: ' . $e->getMessage());
            \Log::error('Failed to seed family members', ['exception' => $e]);
        }

        // Create scheduling data first
        try {
            $this->command->info('Seeding scheduling data...');
            // Generate scheduling data (appointments, visitations) FIRST
            $this->generateSchedulingData($careWorkers, $beneficiaries);
            $this->command->info('Scheduling data seeded successfully.');
        } catch (\Throwable $e) {
            $this->command->error('Failed to seed scheduling data: ' . $e->getMessage());
            \Log::error('Failed to seed scheduling data', ['exception' => $e]);
        }

        // NOW create weekly care plans AFTER visitations exist
        try {
            $this->command->info('Seeding weekly care plans...');
            // Generate initial vital signs
            $vitalSigns = VitalSigns::factory()->count(200)->create();
            
            $wcpCount = 0;
            $interventionCount = 0;
            $this->command->getOutput()->progressStart(count($beneficiaries));
            
            // Define possible illnesses for weekly care plans
            $possibleIllnesses = [
                'Common Cold',
                'Influenza',
                'Bronchitis',
                'Gastroenteritis',
                'Skin Infection',
                'Urinary Tract Infection (UTI)',
                'Pneumonia',
                'Respiratory Infection',
                'Allergic Rhinitis',
                'Diarrhea',
                'Minor Injuries (e.g., falls, sprains)',
                'Headache',
                'Indigestion',
                'Toothache',
                'Sore Throat'
            ];
            
            // For each beneficiary, create weekly care plans that match visitation dates
            foreach ($beneficiaries as $beneficiary) {
                $this->command->getOutput()->progressAdvance();
                
                // Find care manager for this municipality first thing
                $careManager = User::where('role_id', 2)
                    ->where('assigned_municipality_id', $beneficiary->municipality_id)
                    ->first();
                
                // If no care manager for this municipality, use any care manager
                if (!$careManager) {
                    $careManager = User::where('role_id', 2)->first();
                    if (!$careManager) {
                        $this->command->warn("No care manager found for beneficiary {$beneficiary->beneficiary_id}, skipping");
                        continue;
                    }
                }
                
                // Find ALL visitation occurrences for this beneficiary (both completed and scheduled)
                $visitations = VisitationOccurrence::join('visitations', 'visitation_occurrences.visitation_id', '=', 'visitations.visitation_id')
                    ->where('visitations.beneficiary_id', $beneficiary->beneficiary_id)
                    ->where(function($query) {
                        // Include both completed visits and scheduled future visits
                        $query->where('visitation_occurrences.status', 'completed')
                            ->orWhere('visitation_occurrences.status', 'scheduled');
                    })
                    ->where('visitations.visit_type', 'routine_care_visit')  // Focus on routine visits
                    ->whereDate('visitation_occurrences.occurrence_date', '<=', '2025-07-31') // Up to end of July 2025
                    ->orderBy('visitation_occurrences.occurrence_date')
                    ->select('visitation_occurrences.*', 'visitations.care_worker_id')
                    ->get();
                
                // Define our date range from Sept 2024 to July 2025
                $startDate = Carbon::create(2024, 9, 1);
                $endDate = Carbon::create(2025, 7, 31);
                
                // Calculate number of months in the range
                $numMonths = $endDate->diffInMonths($startDate) + 1;
                
                // If there are no visitations or not enough for 3-4 per month, we'll create them
                if ($visitations->isEmpty()) {
                    // Find care worker for this beneficiary's municipality
                    $municipalityCareWorkers = $careWorkers->filter(function ($worker) use ($beneficiary) {
                        return $worker->assigned_municipality_id == $beneficiary->municipality_id;
                    });

                    // If no care worker for this municipality, get any care worker
                    if ($municipalityCareWorkers->isEmpty()) {
                        $this->command->info("No care worker found for municipality {$beneficiary->municipality_id}, using any available care worker");
                        $careWorker = $careWorkers->first();
                        
                        // If still no care workers at all, create one for this municipality
                        if (!$careWorker) {
                            $this->command->info("Creating a new care worker for municipality {$beneficiary->municipality_id}");
                            $careWorker = User::factory()->create([
                                'role_id' => 3,
                                'assigned_municipality_id' => $beneficiary->municipality_id,
                                'assigned_care_manager_id' => $careManager->id
                            ]);
                            // Add to our collection of care workers
                            $careWorkers->push($careWorker);
                        }
                    } else {
                        // Use first() if only one result or random() safely if multiple results
                        $careWorker = $municipalityCareWorkers->count() > 1 ? 
                            $municipalityCareWorkers->random() : 
                            $municipalityCareWorkers->first();
                    }
                    
                    // Create 3-4 WCPs per month across the entire date range
                    $currentDate = $startDate->copy();
                    
                    while ($currentDate->lte($endDate)) {
                        // Generate 3-4 dates for this month
                        $daysInMonth = $currentDate->daysInMonth;
                        $numPlansThisMonth = rand(3, 4); // 3-4 plans per month
                        
                        // Get random days in this month, spaced out roughly evenly
                        $weekDivisions = array_map(function($i) use ($numPlansThisMonth) {
                            return ceil($i * 28 / $numPlansThisMonth); // 28 days / number of plans
                        }, range(0, $numPlansThisMonth - 1));
                        
                        // Add some randomness to the exact day
                        $days = array_map(function($day) {
                            return $day + rand(-2, 2); // Add some variability
                        }, $weekDivisions);
                        
                        // Make sure days are within valid range (1 to days in month)
                        $days = array_map(function($day) use ($daysInMonth) {
                            return max(1, min($day, $daysInMonth));
                        }, $days);
                        
                        // Sort the days
                        sort($days);
                        
                        // Create care plans for each day
                        foreach ($days as $day) {
                            // Set the date to this specific day
                            $planDate = $currentDate->copy()->setDay($day);
                            
                            // Skip if date is past the end date
                            if ($planDate->gt($endDate)) {
                                continue;
                            }
                            
                            // Get a random vital sign
                            $vitalSign = $vitalSigns->random();
                            
                            // Create the weekly care plan
                            $weeklyCarePlan = new WeeklyCarePlan();
                            $weeklyCarePlan->beneficiary_id = $beneficiary->beneficiary_id;
                            $weeklyCarePlan->care_worker_id = $careWorker->id;
                            $weeklyCarePlan->vital_signs_id = $vitalSign->vital_signs_id;
                            $weeklyCarePlan->date = $planDate->format('Y-m-d');
                            $weeklyCarePlan->assessment = $this->getRandomAssessment();
                            $weeklyCarePlan->evaluation_recommendations = $this->getRandomEvaluation();
                            $weeklyCarePlan->created_by = $careWorker->id;
                            $weeklyCarePlan->updated_by = $careWorker->id;
                            $weeklyCarePlan->created_at = $planDate;
                            $weeklyCarePlan->updated_at = $planDate->copy()->addHours(2);
                            $weeklyCarePlan->photo_path = "uploads/weekly_care_plans/{$planDate->format('Y')}/{$planDate->format('m')}/beneficiary_{$beneficiary->beneficiary_id}_assessment_{$this->faker->randomNumber(8)}.jpg";
                            
                            // Generate random illnesses (0-3) for this weekly care plan
                            $weeklyCarePlan->illnesses = json_encode($this->faker->randomElements(
                                $possibleIllnesses, 
                                $this->faker->numberBetween(0, 3)
                            ));

                            // Save the weekly care plan to get an ID
                            $weeklyCarePlan->save();
                            
                            // Now create interventions
                            $this->createWeeklyCarePlanInterventions($weeklyCarePlan, $planDate, $interventionCount);
                            
                            // Add acknowledgements for past care plans
                            if ($planDate->lt(Carbon::now()) && $this->faker->boolean(40)) {
                                $this->addCareplanAcknowledgement($weeklyCarePlan, $beneficiary, $planDate);
                            }
                            
                            $wcpCount++;
                        }
                        
                        // Move to next month
                        $currentDate->addMonth();
                    }
                    
                } else {
                    // We have visitations, so let's use them first and supplement as needed
                    
                    // Group visitations by month
                    $visitationsByMonth = [];
                    foreach ($visitations as $visit) {
                        $visitDate = Carbon::parse($visit->occurrence_date);
                        $yearMonth = $visitDate->format('Y-m');
                        
                        if (!isset($visitationsByMonth[$yearMonth])) {
                            $visitationsByMonth[$yearMonth] = [];
                        }
                        
                        $visitationsByMonth[$yearMonth][] = $visit;
                    }
                    
                    // Process each month in our date range
                    $currentDate = $startDate->copy();
                    while ($currentDate->lte($endDate)) {
                        $yearMonth = $currentDate->format('Y-m');
                        $daysInMonth = $currentDate->daysInMonth;
                        
                        // Determine how many WCPs to create this month (3-4)
                        $numPlansThisMonth = rand(3, 4);
                        
                        // Use existing visitations for this month if available
                        $monthVisits = isset($visitationsByMonth[$yearMonth]) ? $visitationsByMonth[$yearMonth] : [];
                        $numExistingVisits = count($monthVisits);
                        
                        // First, create WCPs for all existing visitations in this month
                        foreach ($monthVisits as $visit) {
                            // Get care worker ID from the visitation
                            $careWorker = User::find($visit->care_worker_id);
                            if (!$careWorker) {
                                // If care worker not found, find another care worker in the same municipality
                                $municipalityCareWorkers = $careWorkers->filter(function ($worker) use ($beneficiary) {
                                    return $worker->assigned_municipality_id == $beneficiary->municipality_id;
                                });
                                $careWorker = $municipalityCareWorkers->isEmpty() ? 
                                    $careWorkers->random() : 
                                    $municipalityCareWorkers->random();
                            }
                            
                            // Get visit date
                            $planDate = Carbon::parse($visit->occurrence_date);
                            
                            // Get a random vital sign
                            $vitalSign = $vitalSigns->random();
                            
                            // Create the weekly care plan
                            $weeklyCarePlan = new WeeklyCarePlan();
                            $weeklyCarePlan->beneficiary_id = $beneficiary->beneficiary_id;
                            $weeklyCarePlan->care_worker_id = $careWorker->id;
                            $weeklyCarePlan->vital_signs_id = $vitalSign->vital_signs_id;
                            $weeklyCarePlan->date = $planDate->format('Y-m-d');
                            $weeklyCarePlan->assessment = $this->getRandomAssessment();
                            $weeklyCarePlan->evaluation_recommendations = $this->getRandomEvaluation();
                            $weeklyCarePlan->created_by = $careWorker->id;
                            $weeklyCarePlan->updated_by = $careWorker->id;
                            $weeklyCarePlan->created_at = $planDate;
                            $weeklyCarePlan->updated_at = $planDate->copy()->addHours(2);
                            $weeklyCarePlan->photo_path = "uploads/weekly_care_plans/{$planDate->format('Y')}/{$planDate->format('m')}/beneficiary_{$beneficiary->beneficiary_id}_assessment_{$this->faker->randomNumber(8)}.jpg";
                            
                            // Generate random illnesses (0-3) for this weekly care plan
                            $weeklyCarePlan->illnesses = json_encode($this->faker->randomElements(
                                $possibleIllnesses, 
                                $this->faker->numberBetween(0, 3)
                            ));

                            // Save the weekly care plan to get an ID
                            $weeklyCarePlan->save();
                            
                            // Now create interventions
                            $this->createWeeklyCarePlanInterventions($weeklyCarePlan, $planDate, $interventionCount);
                            
                            // Only add acknowledgements for past dates
                            if ($planDate->lt(Carbon::now()) && $this->faker->boolean(40)) {
                                $this->addCareplanAcknowledgement($weeklyCarePlan, $beneficiary, $planDate);
                            }
                            
                            $wcpCount++;
                        }
                        
                        // If we have fewer visitations than needed, add additional plans
                        if ($numExistingVisits < $numPlansThisMonth) {
                            // Find a suitable care worker
                            $municipalityCareWorkers = $careWorkers->filter(function ($worker) use ($beneficiary) {
                                return $worker->assigned_municipality_id == $beneficiary->municipality_id;
                            });
                            $careWorker = $municipalityCareWorkers->isEmpty() ? 
                                $careWorkers->random() : 
                                ($municipalityCareWorkers->count() > 1 ? $municipalityCareWorkers->random() : $municipalityCareWorkers->first());
                            
                            // How many more plans do we need?
                            $additionalNeeded = $numPlansThisMonth - $numExistingVisits;
                            
                            // Get the existing visit days to avoid duplicates
                            $existingDays = array_map(function($visit) {
                                return Carbon::parse($visit->occurrence_date)->day;
                            }, $monthVisits);
                            
                            // Create additional plans for this month
                            for ($i = 0; $i < $additionalNeeded; $i++) {
                                // Keep trying to find a date that doesn't conflict with existing visits
                                $attempts = 0;
                                do {
                                    $day = rand(1, $daysInMonth);
                                    $attempts++;
                                } while (in_array($day, $existingDays) && $attempts < 10);
                                
                                // If we couldn't find a non-conflicting day after 10 attempts, just use any day
                                if (in_array($day, $existingDays)) {
                                    $day = rand(1, $daysInMonth);
                                }
                                
                                // Add this day to our list of existing days to avoid duplicates
                                $existingDays[] = $day;
                                
                                // Create the plan date
                                $planDate = $currentDate->copy()->setDay($day);
                                
                                // Skip if date is past the end date
                                if ($planDate->gt($endDate)) {
                                    continue;
                                }
                                
                                // Get a random vital sign
                                $vitalSign = $vitalSigns->random();
                                
                                // Create the weekly care plan
                                $weeklyCarePlan = new WeeklyCarePlan();
                                $weeklyCarePlan->beneficiary_id = $beneficiary->beneficiary_id;
                                $weeklyCarePlan->care_worker_id = $careWorker->id;
                                $weeklyCarePlan->vital_signs_id = $vitalSign->vital_signs_id;
                                $weeklyCarePlan->date = $planDate->format('Y-m-d');
                                $weeklyCarePlan->assessment = $this->getRandomAssessment();
                                $weeklyCarePlan->evaluation_recommendations = $this->getRandomEvaluation();
                                $weeklyCarePlan->created_by = $careWorker->id;
                                $weeklyCarePlan->updated_by = $careWorker->id;
                                $weeklyCarePlan->created_at = $planDate;
                                $weeklyCarePlan->updated_at = $planDate->copy()->addHours(2);
                                $weeklyCarePlan->photo_path = "uploads/weekly_care_plans/{$planDate->format('Y')}/{$planDate->format('m')}/beneficiary_{$beneficiary->beneficiary_id}_assessment_{$this->faker->randomNumber(8)}.jpg";
                                
                                // Generate random illnesses (0-3) for this weekly care plan
                                $weeklyCarePlan->illnesses = json_encode($this->faker->randomElements(
                                    $possibleIllnesses, 
                                    $this->faker->numberBetween(0, 3)
                                ));

                                // Save the weekly care plan to get an ID
                                $weeklyCarePlan->save();
                                
                                // Now create interventions
                                $this->createWeeklyCarePlanInterventions($weeklyCarePlan, $planDate, $interventionCount);
                                
                                // Add acknowledgements for past care plans
                                if ($planDate->lt(Carbon::now()) && $this->faker->boolean(40)) {
                                    $this->addCareplanAcknowledgement($weeklyCarePlan, $beneficiary, $planDate);
                                }
                                
                                $wcpCount++;
                            }
                        }
                        
                        // Move to next month
                        $currentDate->addMonth();
                    }
                }
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


    private function generateNotifications()
    {
        // Generate notifications for all beneficiaries (1-3 per beneficiary)
        $beneficiaries = Beneficiary::all();
        $notificationCount = 0;
        
        // Realistic notification types for beneficiaries
        $beneficiaryNotifications = [
            'Care Plan Updated' => 'Your weekly care plan has been updated by your care worker. Please review the new recommendations.',
            'Upcoming Appointment' => 'You have an appointment scheduled for tomorrow at 10:00 AM with your care worker.',
            'Medication Reminder' => 'Please remember to take your prescribed medication as scheduled.',
            'Service Request Status' => 'Your service request for home assistance has been approved.',
        ];
        
        foreach ($beneficiaries as $beneficiary) {
            $count = rand(1, 3);
            $selectedNotifications = array_rand($beneficiaryNotifications, min($count, count($beneficiaryNotifications)));
                
            // Convert to array if only one notification is selected
            if (!is_array($selectedNotifications)) {
                $selectedNotifications = [$selectedNotifications];
            }
                
            foreach ($selectedNotifications as $notificationType) {
                Notification::create([
                    'user_id' => $beneficiary->beneficiary_id,
                    'user_type' => 'beneficiary',
                    'message_title' => $notificationType,
                    'message' => $beneficiaryNotifications[$notificationType],
                    'date_created' => now()->subDays(rand(1, 14)),
                    'is_read' => (rand(1, 3) == 1), // 1/3 chance of being read
                    'created_at' => now()->subDays(rand(1, 14)),
                    'updated_at' => now()
                ]);
                    
                $notificationCount++;
            }
        }
        
        \Log::info("Created {$notificationCount} notifications for beneficiaries");
        
        // Generate notifications for family members
        $familyMembers = FamilyMember::all();
        $notificationCount = 0;
        
        // Realistic notification types for family members
        $familyNotifications = [
            'Beneficiary Health Update' => 'The weekly care plan for your family member has been updated. Please review the changes.',
            'Upcoming Visit Information' => 'A care worker visit is scheduled for your family member tomorrow at 11:00 AM.',
            'Medication Change Alert' => 'There has been a change in your family member\'s medication schedule. Please review the details.',
            'Care Plan Approval Needed' => 'Please review and approve the proposed care plan for your family member.',
            'Service Request Update' => 'The additional service request you submitted for your family member is being processed.',
        ];
        
        foreach ($familyMembers as $familyMember) {
            // Create 1-2 notifications for each family member
            $count = rand(1, 2);
            $selectedNotifications = array_rand($familyNotifications, min($count, count($familyNotifications)));
            
            // Convert to array if only one notification is selected
            if (!is_array($selectedNotifications)) {
                $selectedNotifications = [$selectedNotifications];
            }
            
            foreach ($selectedNotifications as $notificationType) {
                // Get related beneficiary name for personalization
                $beneficiary = Beneficiary::find($familyMember->related_beneficiary_id);
                $beneficiaryName = $beneficiary ? $beneficiary->name : "your family member";
                
                // Personalize the message
                $message = str_replace("your family member", $beneficiaryName, $familyNotifications[$notificationType]);
                
                Notification::create([
                    'user_id' => $familyMember->family_member_id,
                    'user_type' => 'family_member',
                    'message_title' => $notificationType,
                    'message' => $message,
                    'date_created' => now()->subDays(rand(1, 10)),
                    'is_read' => (rand(1, 3) == 1), // 1/3 chance of being read
                    'created_at' => now()->subDays(rand(1, 10)),
                    'updated_at' => now()
                ]);
                
                $notificationCount++;
            }
        }
        
        \Log::info("Created {$notificationCount} notifications for family members");
        
        // Generate notifications for ALL COSE staff
        $staffMembers = User::where('role_id', '<=', 3)->get();
        $notificationCount = 0;
        
        $notificationTypes = [
            // Admin notifications (role_id = 1)
            1 => [
                'System Update' => 'The system has been updated with new features for care plan management.',
                'Internal Appointment Created' => 'A new internal team meeting has been scheduled for next Monday at 10:00 AM.',
                'Staff Performance Report' => 'The monthly staff performance reports are now available for review.',
                'Security Alert' => 'A new security patch has been applied to protect beneficiary data.',
                'New User Registration' => 'Three new care workers have been registered in the system.',
                'Data Backup Complete' => 'The weekly data backup has completed successfully.',
                'Performance Report' => 'The quarterly performance metrics report is ready for your review.'
            ],
            // Care Manager notifications (role_id = 2)
            2 => [
                'New Case Assigned' => 'You have been assigned 3 new beneficiaries to manage.',
                'Internal Appointment Created' => 'A staff training session has been scheduled for Wednesday at 2:00 PM.',
                'Care Plan Review' => 'Five care plans are due for review this week.',
                'Staff Schedule Update' => 'There are changes to the care worker schedule for next week.',
                'Patient Status Alert' => 'Beneficiary Maria Santos has reported increased pain levels.',
                'Weekly Report Due' => 'Your weekly team status report is due tomorrow.',
                'Training Completion' => 'Your team has completed 95% of the required training modules.'
            ],
            // Care Worker notifications (role_id = 3)
            3 => [
                'Visit Reminder' => 'You have 3 scheduled visits tomorrow starting at 9:00 AM.',
                'Medication Update' => 'Medication schedule has been updated for beneficiary Juan Cruz.',
                'Training Available' => 'A new training module on diabetes management is available for completion.',
                'Shift Change Request' => 'Your schedule change request for next Friday has been approved.',
                'Documentation Reminder' => 'Please complete your visit documentation for today\'s appointments.',
                'Assessment Due' => 'Weekly assessment for beneficiary Elena Reyes is due by Thursday.',
                'Supply Restock' => 'Your care supply kit has been restocked and is ready for pickup.'
            ]
        ];
        
        // For each staff member, create role-specific notifications
        foreach ($staffMembers as $staff) {
            $roleSpecificMessages = $notificationTypes[$staff->role_id] ?? $notificationTypes[1];
            $count = rand(3, 7); // Create 3-7 notifications per user
            
            // Create some read and some unread notifications
            $selectedNotifications = array_rand($roleSpecificMessages, min($count, count($roleSpecificMessages)));
            
            // Convert to array if only one notification is selected
            if (!is_array($selectedNotifications)) {
                $selectedNotifications = [$selectedNotifications];
            }
                
            foreach ($selectedNotifications as $title) {
                Notification::create([
                    'user_id' => $staff->id,
                    'user_type' => 'cose_staff',
                    'message_title' => $title,
                    'message' => $roleSpecificMessages[$title],
                    'date_created' => now()->subHours(rand(1, 72)), // Random time within last 3 days
                    'is_read' => rand(0, 100) < 30, // 30% chance of being read
                    'created_at' => now()->subHours(rand(1, 72)),
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
            $weeklyRecurringCount = 0;
            $monthlyRecurringCount = 0;
            $occurrenceCount = 0;
            $canceledCount = 0;
            
            // Create base weekly routine care visits for EVERY beneficiary first
            $this->command->info('Creating one weekly routine care visit for each beneficiary with their assigned care worker...');
            
            // Track which beneficiaries have already been assigned weekly recurring visits
            $beneficiariesWithWeeklyVisits = [];
            
            foreach ($beneficiaries as $beneficiary) {
                // Check if beneficiary has a general care plan with an assigned care worker
                if ($beneficiary->general_care_plan_id) {
                    $generalCarePlan = \App\Models\GeneralCarePlan::find($beneficiary->general_care_plan_id);
                    
                    if ($generalCarePlan && $generalCarePlan->care_worker_id) {
                        // Find the assigned care worker from the general care plan
                        $assignedCareWorker = $careWorkers->firstWhere('id', $generalCarePlan->care_worker_id);
                        
                        // If found, use the assigned care worker, otherwise find one from the same municipality
                        if (!$assignedCareWorker) {
                            // Find a care worker in the same municipality
                            $municipalityId = $beneficiary->municipality_id;
                            $municipalityCareWorkers = $careWorkers->filter(function($worker) use ($municipalityId) {
                                return $worker->assigned_municipality_id == $municipalityId;
                            });
                            
                            // If no workers in this municipality, use any available care worker
                            if ($municipalityCareWorkers->isEmpty()) {
                                $assignedCareWorker = $careWorkers->random();
                            } else {
                                $assignedCareWorker = $municipalityCareWorkers->random();
                            }
                        }
                        
                        // Create a weekly recurring routine care visit for this beneficiary
                        $startDate = $this->faker->dateTimeBetween('-2 weeks', 'now');
                        $visitation = $this->createRecurringVisitation(
                            $assignedCareWorker, 
                            $beneficiary, 
                            'routine_care_visit', 
                            'weekly',
                            $startDate
                        );
                        
                        // Generate occurrences for this recurring visitation (6 months worth)
                        $generatedOccurrences = $this->generateVisitationOccurrences($visitation, 6, true);
                        $occurrenceCount += count($generatedOccurrences);
                        $weeklyRecurringCount++;
                        
                        // Count how many occurrences were canceled
                        $canceledOccurrences = VisitationOccurrence::where('visitation_id', $visitation->visitation_id)
                            ->where('status', 'canceled')
                            ->count();
                        $canceledCount += $canceledOccurrences;
                        
                        // Mark this beneficiary as having a weekly visit
                        $beneficiariesWithWeeklyVisits[] = $beneficiary->beneficiary_id;
                    }
                }
            }
            
            // Create regular (non-recurring) visitations for variety
            $this->command->info('Creating additional non-recurring visitations...');
            for ($i = 0; $i < 30; $i++) {
                // Randomly select a care worker
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
                    'visit_type' => $this->faker->randomElement(['service_request', 'emergency_visit']), // Only non-routine visits
                    'visitation_date' => $this->faker->dateTimeBetween('-2 weeks', '+2 weeks')->format('Y-m-d'),
                    'assigned_by' => User::where('role_id', 2)
                        ->where('assigned_municipality_id', $municipalityId)
                        ->first()->id ?? User::where('role_id', 1)->first()->id
                ]);
                
                // Create a single occurrence for this visitation
                $occurrence = VisitationOccurrence::factory()->create([
                    'visitation_id' => $visitation->visitation_id,
                    'occurrence_date' => $visitation->visitation_date,
                    'start_time' => $visitation->start_time,
                    'end_time' => $visitation->end_time,
                    'status' => Carbon::parse($visitation->visitation_date)->isPast() ? 
                        $this->faker->randomElement(['completed', 'canceled']) : 'scheduled'
                ]);
                
                // Count canceled occurrences
                if ($occurrence->status === 'canceled') {
                    $canceledCount++;
                }
                
                $regularCount++;
            }
            
            // Create some recurring visitations with monthly pattern (for additional variety)
            $this->command->info('Creating monthly recurring visitations for additional variety...');
            for ($i = 0; $i < 8; $i++) {
                // Randomly select a care worker
                $careWorker = $careWorkers->random();
                $municipalityId = $careWorker->assigned_municipality_id;
                
                // Find beneficiaries in the same municipality, preferably ones that don't already have monthly visits
                $municipalityBeneficiaries = $beneficiaries->where('municipality_id', $municipalityId);
                
                if ($municipalityBeneficiaries->isEmpty()) {
                    $beneficiary = $beneficiaries->random();
                } else {
                    $beneficiary = $municipalityBeneficiaries->random();
                }
                
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
                $generatedOccurrences = $this->generateVisitationOccurrences($visitation, 6, true);
                $occurrenceCount += count($generatedOccurrences);
                $monthlyRecurringCount++;
                
                // Count canceled occurrences
                $newCanceledCount = VisitationOccurrence::where('visitation_id', $visitation->visitation_id)
                    ->where('status', 'canceled')
                    ->count();
                $canceledCount += $newCanceledCount;
            }
            
            $this->command->info("Generated visitations: {$regularCount} regular, {$weeklyRecurringCount} weekly recurring, " . 
                "{$monthlyRecurringCount} monthly recurring with {$occurrenceCount} total occurrences ({$canceledCount} canceled)");
            
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
     * 
     * @param Visitation $visitation The visitation to generate occurrences for
     * @param int $months Number of months to generate occurrences for
     * @param bool $includeCancellations Whether to include random cancellations
     * @return array Array of generated occurrence IDs
     */
    private function generateVisitationOccurrences($visitation, $months = 3, $includeCancellations = false)
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
                    // Determine if this occurrence should be canceled (about 10% chance)
                    $shouldCancel = $includeCancellations && $this->faker->boolean(10);
                    
                    // Calculate status based on date and cancellation flag
                    $status = 'scheduled';
                    if ($dateIterator->isPast()) {
                        $status = $shouldCancel ? 'canceled' : 'completed';
                    } else {
                        $status = $shouldCancel ? 'canceled' : 'scheduled';
                    }
                    
                    // Create appropriate notes only for canceled or completed visits
                    $notes = null;
                    if ($status === 'canceled') {
                        $notes = $this->faker->randomElement([
                            'Canceled due to care worker illness',
                            'Beneficiary requested to reschedule',
                            'Scheduling conflict with medical appointment',
                            'Weather conditions made travel unsafe',
                            'Family emergency required rescheduling',
                            'Beneficiary hospitalized on this date',
                            'Care worker reassigned to emergency case'
                        ]);
                    } elseif ($status === 'completed') {
                        $notes = $this->faker->optional(0.7)->sentence();
                    }
                    
                    // Create the occurrence
                    $occurrence = VisitationOccurrence::create([
                        'visitation_id' => $visitation->visitation_id,
                        'occurrence_date' => $dateIterator->format('Y-m-d'),
                        'start_time' => $visitation->start_time,
                        'end_time' => $visitation->end_time,
                        'status' => $status,
                        'notes' => $notes
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
                    // Determine if this occurrence should be canceled (about 10% chance)
                    $shouldCancel = $includeCancellations && $this->faker->boolean(10);
                    
                    // Calculate status based on date and cancellation flag
                    $status = 'scheduled';
                    if ($currentDate->isPast()) {
                        $status = $shouldCancel ? 'canceled' : 'completed';
                    } else {
                        $status = $shouldCancel ? 'canceled' : 'scheduled';
                    }
                    
                    // Create appropriate notes only for canceled or completed visits
                    $notes = null;
                    if ($status === 'canceled') {
                        $notes = $this->faker->randomElement([
                            'Canceled due to care worker illness',
                            'Beneficiary requested to reschedule',
                            'Scheduling conflict with medical appointment',
                            'Weather conditions made travel unsafe',
                            'Family emergency required rescheduling',
                            'Beneficiary hospitalized on this date',
                            'Care worker reassigned to emergency case'
                        ]);
                    } elseif ($status === 'completed') {
                        $notes = $this->faker->optional(0.7)->sentence();
                    }
                    
                    // Create the occurrence
                    $occurrence = VisitationOccurrence::create([
                        'visitation_id' => $visitation->visitation_id,
                        'occurrence_date' => $currentDate->format('Y-m-d'),
                        'start_time' => $visitation->start_time,
                        'end_time' => $visitation->end_time,
                        'status' => $status,
                        'notes' => $notes
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
            EmergencyNotice::factory()->count(5)->resolved()->create();
            
            // Create 5 new service requests
            ServiceRequest::factory()->count(5)->asNew()->create();
            
            // For approved service requests
            ServiceRequest::factory()->count(3)->approved()->create();

            // For rejected service requests
            ServiceRequest::factory()->count(3)->rejected()->create();

            // For completed service requests
            ServiceRequest::factory()->count(4)->completed()->create();
            
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

    /**
     * Get a simple random assessment text
     */
    private function getRandomAssessment()
    {
        $pairs = $this->getAllAssessmentEvaluationPairs();
        $randomPair = $this->faker->randomElement($pairs);
        return $randomPair['assessment'];
    }

    /**
     * Get a simple random evaluation text
     */
    private function getRandomEvaluation()
    {
        $pairs = $this->getAllAssessmentEvaluationPairs();
        $randomPair = $this->faker->randomElement($pairs);
        return $randomPair['evaluation'];
    }

    /**
     * Get a simple custom intervention description
     */
    private function getSimpleCustomDescription($careCategoryId)
    {
        $descriptions = [
            1 => 'Personalized mobility assistance adapted for client needs',
            2 => 'Memory enhancement through personalized activities',
            3 => 'Specialized personal care technique for client comfort',
            4 => 'Enhanced medication management with safety measures',
            5 => 'Customized social engagement adapted to client needs',
            6 => 'Recreational activities adapted to client abilities',
            7 => 'Household management with simplified systems'
        ];
        
        return $descriptions[$careCategoryId] ?? 'Personalized care assistance for client needs';
    }

    /**
     * Get all assessment-evaluation pairs - moved from WeeklyCarePlanFactory
     * 
     * @return array
     */
    private function getAllAssessmentEvaluationPairs()
    {
        return [
            // Mobility-related assessments
            [
                "assessment" => "Si Lolo ay nakakaranas ng matinding paghina ng kanyang paglalakad. Dati, nakakalakad pa siya nang may tungkod, ngunit ngayon ay kinakailangan na niyang humawak sa dingding o sa anumang matibay na bagay upang mapanatili ang balanse. Sinabi niya na madalas siyang nakakaramdam ng pamamanhid sa kanyang mga binti, at minsan ay biglang napapaluhod dahil parang nawawalan siya ng lakas. Nakita ko rin na hirap siyang umakyat kahit sa mababang hagdan, at nangangailangan siya ng tulong ng iba para makalipat sa ibang kwarto ng bahay. Bukod dito, madalas siyang natatakot lumabas ng bahay dahil sa posibilidad ng pagkakadulas, lalo na kapag basa ang sahig. May pagkakataon ding umaabot sa sampung minuto bago siya tuluyang makatayo mula sa upuan, at minsan ay kinakailangan pa siyang buhatin upang maiwasan ang pananakit ng kanyang likod. Dahil sa kanyang sitwasyon, unti-unti siyang nagiging sedentary at mas madalang na siyang makihalubilo sa ibang tao.",
                "evaluation" => "Iminungkahi kong gumamit si Lolo ng walker na may gulong upang mabawasan ang kanyang pangangailangan na kumapit sa dingding o kasangkapan. Mahalaga rin na ilagay ang lahat ng madalas niyang puntahan sa loob ng bahay sa abot-kamay na lugar upang mabawasan ang kanyang paggalaw. Upang maiwasan ang pagbagsak, dapat alisin ang mga madulas na basahan sa sahig at gumamit ng rubberized flooring o anti-slip mats sa mga lugar tulad ng banyo at kusina. Bukod dito, dapat palaging may kasamang tagapag-alaga tuwing siya ay lalabas ng bahay upang masigurong may sasalo sa kanya kung sakaling mawalan siya ng balanse. Iminungkahi ko rin ang araw-araw na magaan na ehersisyo tulad ng gentle leg lifts at ankle rotations upang mapanatili ang kanyang kakayahan sa paggalaw at maiwasan ang mabilisang paghina ng kanyang kalamnan. Sa mental at emotional aspect, mahalaga ring i-reassure si Lolo na hindi siya pabigat at hikayatin siyang makihalubilo sa pamilya kahit nasa loob lamang ng bahay."
            ],
            [
                "assessment" => "Si Nanay ay nakakaranas ng matinding kahirapan sa pagtulog, na nagiging sanhi ng kanyang iritabilidad at pagkahina sa araw. Sinabi niya na sa nakalipas na tatlong buwan, madalas siyang nakakatulog lamang ng tatlo hanggang apat na oras sa gabi, kahit anong gawin niyang paraan upang makatulog. Nakaramdam siya ng pag-aalala at labis na pagkabalisa bago matulog, at minsan ay nagigising siya sa gitna ng gabi na pinagpapawisan nang hindi niya alam ang dahilan. Bukod dito, madalas siyang gumigising nang alas-tres ng madaling araw at hindi na makatulog muli, kahit pagod na pagod ang kanyang katawan. Sa kanyang umaga, ramdam niya ang sobrang pagkaantok at mabagal na pag-iisip, na nagiging dahilan upang hindi niya magawa ang mga simpleng gawain sa bahay. Nabanggit din niya na tuwing gabi, maraming negatibong kaisipan ang pumapasok sa kanyang isip tungkol sa kanyang kalusugan, takot sa hinaharap, at kawalan ng silbi sa kanyang pamilya.",
                "evaluation" => "Dahil sa malalang insomnia ni Nanay, iminungkahi ko ang pagkakaroon ng isang structured bedtime routine, kabilang ang pagtigil sa panonood ng TV o paggamit ng cellphone isang oras bago matulog upang maiwasan ang overstimulation. Bukod dito, dapat na alisin ang anumang nakakagambalang ingay sa kanyang paligid at tiyakin na ang ilaw sa kwarto ay dim o malambot upang makapag-relax. Para sa kanyang pagkabalisa bago matulog, maaaring subukan ang deep breathing exercises o guided meditation upang mapahupa ang kanyang stress. Kung patuloy siyang nagigising sa kalagitnaan ng gabi, mahalagang suriin kung may underlying medical condition tulad ng sleep apnea na maaaring nakakaapekto sa kanyang tulog. Para maiwasan ang daytime drowsiness, maaaring ipatupad ang isang regular na sleep-wake schedule, na nangangahulugan ng pag-iwas sa matagalang pagtulog sa hapon. Bukod dito, ang pakikipag-usap sa kanyang pamilya tungkol sa kanyang alalahanin sa hinaharap ay makakatulong upang mabawasan ang kanyang stress."
            ],
            [
                "assessment" => "Napansin ko ang matinding kahirapan ni Tatay sa pagtayo mula sa nakaupo. Noong una ay kaya pa niyang tumayo mula sa upuan sa loob ng ilang segundo, ngunit ngayon ay umaabot na ng dalawa hanggang tatlong minuto ang kanyang pakikibaka para lang makatayo. Madalas siyang sumusubok nang tatlo o apat na beses bago tuluyang makatayo, at sa tuwing gagawin niya ito, nakikita ko sa kanyang mukha ang matinding pagsisikap at pagod. Bukod dito, kapag nakatayo na siya, kinakailangan pa rin niyang humawak nang mahigpit sa pinakamalapit na muwebles o dingding upang mapanatili ang kanyang balanse. May mga pagkakataon din na bigla siyang mawawalan ng balanse kahit nakatayo nang tuwid, na para bang may biglang panghihina sa kanyang mga binti. Dahil dito, naging takot na siya na gumamit ng karaniwang upuan at mas gusto na niyang umupo sa mga upuang may matinding suporta sa braso. Ang dating masigla at aktibong si Tatay ay unti-unti nang nawawalan ng kumpiyansa sa kanyang sariling kakayahan at natatakot nang magpunta sa mga lugar na wala siyang mapaglalagyan ng kamay para sa suporta.",
                "evaluation" => "Batay sa aking nakita, ipinapayo kong magkaroon si Tatay ng mga upuan na may mataas at matibay na arm rests upang magamit niya ito bilang suporta sa pagtayo. Mainam din na lagyan ng mga grab bars ang strategic na lokasyon sa bahay, lalo na sa madalas niyang puntahan at sa mga lugar kung saan siya madalas na nauupo. Makakatulong din ang pagkakaroon ng raised toilet seat na may handles sa banyo para mabawasan ang hirap sa pagtayo mula sa inidoro. Iminumungkahi ko rin ang pangangalaga sa kanyang tuhod sa pamamagitan ng pag-iwas sa pagkakaluhod o pag-upo nang masyadong mababa, at ang paggamit ng knee braces kung kinakailangan. Mahalaga ring ipagpatuloy niya ang mga thigh strengthening exercises na ituturo ko sa kanya, gaya ng seated leg lifts at gentle squats na may suporta. Bilang karagdagan, nirerekomenda ko ang paggamit ng assistive device gaya ng quad cane o walker, lalo na kapag lalabas ng bahay, para mabawasan ang posibilidad ng pagkahulog. Kinakailangang tandaan ng pamilya na paalalahanan si Tatay na huwag magmadali sa pagtayo at laging mag-ingat upang maiwasan ang pagkabalisa na maaaring makadagdag sa kanyang kahinaan."
            ],
            [
                "assessment" => "Si Lola ay dumaranas ng matinding pananakit ng mga kasukasuan, lalo na sa kanyang mga kamay, tuhod, at balakang, na lubhang nakakaapekto sa kanyang kakayahang gumalaw nang malaya. Naobserbahan ko na ang mga daliri niya ay namamaga at medyo baluktot, na nagpapahirap sa kanya sa simpleng gawain tulad ng paghawak ng kutsara at tinidor o pagbukas ng mga garapon. Kapag naglalakad, napapansin kong mabigat ang kanyang mga hakbang at tila nagpipigil siya ng kanyang hininga tuwing luluhod o yuyuko. Madalas niyang hinahawakan ang kanyang balakang at likod habang naglalakad at nakangiwi ang kanyang mukha sa tuwing umuupo o tumatayo. Sa umaga, sinabi niyang mas matindi ang pananakit at nangangailangan siya ng halos isang oras para lang makapag-ready sa kanyang sarili dahil sa matinding stiffness. Kapag maulan o malamig ang panahon, lalo pang lumalalâ ang kanyang kondisyon kaya't mas gusto na lang niyang manatili sa kama sa mga ganoong araw. Dahil sa patuloy na pananakit, nawawalan na siya ng gana sa pakikilahok sa mga aktibidad na dating kinagigiliwan niya, tulad ng pagtatanim sa hardin at pakikipag-chika sa kanyang mga kaibigan sa plaza.",
                "evaluation" => "Upang matulungan si Lola sa kanyang kondisyon, iminumungkahi kong gumamit siya ng heating pad o warm compress sa mga masakit na kasukasuan bago bumangon sa umaga upang mabawasan ang stiffness. Maaari din siyang uminom ng mainit na tubig sa umaga para mapainit ang katawan bago magsimula ng mga aktibidad. Para sa kanyang mga kamay, nirerekomenda ko ang paggawa ng mga gentle hand exercises tulad ng finger stretching at wrist rotations, at ang paggamit ng mga adaptive utensils na may malaking grip para sa pagkain. Para naman sa kanyang tuhod at balakang, makakatulong ang paggamit ng walking aids tulad ng cane na may tamang taas para sa kanya, at ang pagsusuot ng mga slip-resistant na sapatos na may cushioned soles para sa mas mahusay na shock absorption. Nakipag-usap na ako sa kanyang pamilya tungkol sa kahalagahan ng pagbibigay ng pain medication sa tamang oras ayon sa reseta ng doktor, at hindi lang kapag nakakaramdam na siya ng sakit. Ipinaliwanag ko rin sa kanila ang kahalagahan ng regular ngunit gentle na ehersisyo para mapanatili ang flexibility ng mga joints, at ang posibilidad ng physical therapy para sa mas specialized na approach. Sa huli, pinayuhan ko si Lola na mag-pace ng kanyang activities at huwag itulak ang kanyang sarili nang sobra sa mga araw na maganda ang kanyang pakiramdam upang maiwasan ang flare-ups."
            ],
            [
                "assessment" => "Si Nanay ay nagpapakita ng matinding takot sa pagkahulog na nagiging hadlang sa kanyang aktibidad at kalidad ng pamumuhay. Ayon sa kanya, dalawang beses siyang nadulas at nahulog noong nakaraang taon—isang beses sa banyo at isang beses sa hagdan—bagaman hindi naman siya nagkaroon ng seryosong pinsala. Mula noon, napansin ko na nag-iiba ang kanyang kilos sa bahay. Humahanap siya ng mga bagay na mahahawakan habang naglalakad, kahit sa mga pamilyar na lugar sa bahay. Mabagal at maingat ang kanyang paglalakad, na minsan ay halos kakaladkad na niya ang kanyang mga paa sa sahig para matiyak na hindi siya madudulas. Kapag nagbibihis, umuupo siya sa kama imbes na nakatayo, at tuwing kakain, mas pipiliin niyang umupo sa sulok ng hapagkainan para sa karagdagang suporta. Kahit sa simpleng pagtawid sa isang mababaw na kanal o pagtapak sa medyo hindi pantay na daan, kitang-kita ko ang pagkabalisa sa kanyang mukha. Noong isang linggo, tumanggi siyang sumama sa handaan ng kanyang kaibigan dahil nag-alala siyang baka madulas siya sa hindi pamilyar na lugar. Dahil sa takot niyang ito, unti-unti nang nababawasan ang kanyang social engagements, physical activities, at maging ang kanyang araw-araw na gawain sa bahay.",
                "evaluation" => "Ang takot ni Nanay sa pagkahulog ay isang seryosong concern dahil nagdudulot ito ng pag-iwas sa mga aktibidad, na maaaring magresulta sa muscle deconditioning at higit na kahinaan. Iminumungkahi ko ang isang multifaceted approach para tugunan ito. Una, kailangan gawin ang kanyang environment na mas ligtas—tanggalin ang mga clutter, loose mats, at cords sa daanan; maglagay ng grab bars sa banyo at sa tabi ng kama; pagandahin ang ilaw sa bahay lalo na sa hagdan at mga madilim na sulok; at magkabit ng non-slip mats sa shower area. Pangalawa, mahalagang i-address ang kanyang fear psychologically. Sa halip na isipin ang kanyang limitasyon, turuan siyang mag-focus sa mga hakbang na magagawa niya para mapalaki ang kanyang kumpiyansa—tulad ng mga balance exercises na ituturo ko, unti-unting pagtatagumpay sa mga simpleng gawain, at pag-acknowledge ng mga sitwasyon na successfully niyang na-navigate. Iminumungkahi ko rin ang paggamit ng tamang assistive device gaya ng quad cane, na makakatulong sa kanyang stability habang naglalakad. Para sa long-term recovery, bibigyan ko siya ng set ng progressive balance exercises na gagawin araw-araw, simula sa mga simpleng pagtayo nang may suporta hanggang sa mas challenging na exercises habang lumalaki ang kanyang kumpiyansa. Pinayuhan ko rin ang pamilya na paganahin siyang lumabas ng bahay at makisalamuha sa iba, kasama ng isang kasama na kayang tumulong kung kinakailangan, upang hindi siya ma-isolate dahil sa kanyang takot."
            ],
            [
                "assessment" => "Si Lolo ay dumaranas ng matinding muscle weakness at pagkapagod habang naglalakad, na nagpapahirap sa kanya sa kanyang daily activities. Napansin ko na kapag naglalakad siya, kailangan niyang huminto at magpahinga pagkatapos ng 15-20 metro lamang, at madalas niyang hinahawakan ang kanyang mga binti habang nakangiwi ang mukha. Kahapon, habang kami ay naglalakad-lakad sa bakuran, sinabi niya sa akin na parang may mabibigat na bato ang nakasabit sa kanyang mga paa. Ayon sa pamilya, dati ay kaya pa niyang maglakad hanggang sa palengke na mga 300 metro ang layo mula sa bahay, ngunit sa nakaraang tatlong buwan, dumadali siyang napapagod at kailangang umupo. Bukod dito, nahihirapan na rin siyang buhatin ang kanyang mga paa habang naglalakad, at minsan ay nagdudulot ng pagtisod o pagkatisod. Napag-alaman ko rin na kapag napapagod, nanginginig ang kanyang mga binti at maaaring tumagal ang pakiramdam na ito ng ilang minuto pagkatapos siyang maupo. Ang mas nakababahala ay ang pananakit sa kanyang dibdib at kakapusan ng paghinga pagkatapos ng kahit kaunting paghihirap lamang. Dahil sa mga limitasyong ito, naging dependent na siya sa iba para sa simpleng gawain tulad ng pagkuha ng tubig o pagpunta sa CR, lalo na kapag nasa labas siya ng bahay.",
                "evaluation" => "Ang muscle weakness at fatigue na nararanasan ni Lolo ay nangangailangan ng comprehensive na approach. Unang-una, iminumungkahi ko ang pagkonsulta sa doktor upang masuri kung may underlying medical conditions tulad ng anemia, hypothyroidism, o cardiac issues na maaaring nagiging sanhi ng kanyang kahinaan at kakapusan ng paghinga. Habang naghihintay ng medical assessment, maglaan ng mga rest areas sa loob at paligid ng bahay—strategically placed chairs o upuan kung saan madali siyang makakaupo kapag napapagod. Para sa short-term management, magandang bumuo ng energy conservation techniques gaya ng pag-schedule ng activities sa mga oras na malakas ang kanyang pakiramdam, pagkakaroon ng balanseng activity at pahinga, at pag-prioritize ng mga gawain. Nirerekomenda ko rin ang progressive strengthening exercises na magsisimula sa seated exercises tulad ng leg lifts, ankle rotations, at gentle resistance band exercises. Sa pag-uusap namin ng pamilya, binigyang-diin ko na ang bawat physical activity ay dapat na nakabase sa tolerance ni Lolo sa araw na iyon, at hindi dapat itulak hanggang sa point ng extreme fatigue. Para sa kanyang mobility concerns, iminumungkahi ko ang paggamit ng quad cane o walker na may seat para may mapagpahingahan siya kapag napapagod habang nasa labas ng bahay. Mahalaga ring i-monitor ang kanyang cardiovascular response sa exertion, at kung mayroong chest pain o labored breathing, dapat tigilan ang aktibidad at ipagbigay-alam sa healthcare provider. Sa pangmatagalang plano, ang pagpapanatili ng regular ngunit gentle exercise routine ang makakatulong upang mapalakas muli ang kanyang mga kalamnan at maiwasan ang further deconditioning."
            ],
            [
                "assessment" => "Si Tatay ay nagpapakita ng matinding kahirapan sa pag-navigate ng mga hagdanan at hindi pantay na surfaces, na seryosong nakakaapekto sa kanyang mobility at independence. Noong binisita ko siya, nakita kong pababa siya mula sa second floor ng bahay. Sa bawat hakbang, mahigpit siyang nakahawak sa handrail at dahan-dahan niyang inilalagay ang magkabilang paa sa isang step bago lumipat sa susunod. Halos sampung minuto ang inabot niya para makababa ng isang flight lamang ng hagdan. Kapag umakyat naman, kinakailangan niyang magpahinga pagkatapos ng bawat tatlo o apat na steps dahil sa hingal at pananakit ng kanyang mga tuhod at balakang. Napansin ko rin ang kanyang hirap sa labas ng bahay—kapag naglalakad sa bakuran na may mga maliliit na bato o sa daan na medyo hindi patag, napakalaki ng bigat ng kanyang mga hakbang at tila natataranta siya. Madalas din siyang tumitig sa lupa habang naglalakad, tila sumusuri sa bawat hakbang na gagawin niya. Minsan, nakita ko siyang muntik nang madapa sa isang maliit na crack sa sidewalk, at mula noon, takot na siyang lumabas nang mag-isa. Ayon sa kanyang asawa, dati ay walang problema si Tatay sa pag-akyat-baba sa hagdan o paglalakad sa hindi patag na daan, ngunit sa nakalipas na anim na buwan, unti-unti itong naging hamon para sa kanya.",
                "evaluation" => "Ang paghawak ni Tatay sa mga hagdanan at hindi pantay na surfaces ay isang seryosong concern na kinakailangang ma-address dahil ito'y nakakaapekto sa kanyang kaligtasan at kalayaan sa paggalaw. Nagsimula ako sa pagsusuri ng kanyang bahay at napansin na may ilang lugar na kailangang pagandahin para sa kanyang kaligtasan. Una, iminumungkahi ko ang pagkabit ng pangalawang handrail sa hagdan upang magkaroon siya ng suporta sa magkabilang kamay habang umakyat o bumababa. Ang mga steps ay dapat markahan ng contrasting color strips sa edge para mas makita niya ang bawat step boundary. Para sa intermediate term, maaaring isaalang-alang ang paglalagay ng stair lift kung patuloy na magiging problema ang hagdan. Para sa outdoor mobility, ipinapayo ko ang paggamit ng hiking poles o specialized canes na may wider base para sa mas stable na paglalakad sa hindi patag na terrain. Kausapin ko rin ang pamilya tungkol sa kahalagahan ng tamang footwear—rubber-soled na sapatos na may sapat na support at grip para mabawasan ang posibilidad ng pagkadulas. Bilang parte ng kanyang rehabilitation plan, magbibigay ako ng specific exercises na nakatuon sa pagpapalakas ng leg muscles, particularly ang quadriceps at hip extensors, pati na rin ang ankle strength at flexibility exercises para mapabuti ang kanyang balanse sa hindi patag na surfaces. Bukod dito, pinapayuhan ko si Tatay na i-practice ang visual scanning techniques—ang pagsusuri sa environment nang hindi masyadong nakayuko, upang maiwasan ang pagka-disoriented at mapabuti ang posture habang naglalakad. Sa mga susunod na linggo, gagabayan ko siya sa progressive stair exercises, simula sa lower steps hanggang sa buong flight, habang binabantayan ang kanyang tolerance at kumpiyansa."
            ],
            
            // Visual and hearing-related assessments
            [
                "assessment" => "Si Lolo ay dumaranas ng progresibong pagkawala ng pandinig na nagresulta sa malaking pagbabago sa kanyang pakikipag-ugnayan sa ibang tao. Napansin ko na madalas niyang hinihiling sa kausap na ulitin ang sinabi, at kapag may nagsasalita sa kanya, tila sinusubukan niyang basahin ang labi ng taong ito. Kapag nanonood ng TV o nakikinig ng radyo, madalas niyang nilalakas ang volume na nagiging sanhi ng reklamo mula sa ibang miyembro ng pamilya. Bukod dito, sa mga pagkakataong may group conversation, lalo na sa hapagkainan o family gatherings, unti-unti siyang nagiging tahimik at lumalayo sa usapan. Nabanggit ng kanyang anak na naging iritable si Lolo kapag hindi niya naiintindihan ang sinasabi sa kanya, at minsan ay nagagalit siya dahil akala niya ay bulong-bulungan siya ng mga tao. Naobserbahan ko rin na tumataas ang volume ng boses ni Lolo kapag nagsasalita, na tila hindi niya napapansin. Ang dating masayahing si Lolo na mahilig makipagkwentuhan ay ngayon ay madalas na nananahimik na lang sa sulok, nanonood ng TV nang mag-isa sa malakas na volume, at umiiwas sa mga pagtitipon kung saan maraming tao at ingay.",
                "evaluation" => "Ang pagkawala ng pandinig ni Lolo ay hindi lamang isang hearing issue kundi nakakaapekto na rin sa kanyang psychological well-being at social interactions. Iminumungkahi ko na magpatingin siya sa ENT specialist para sa komprehensibong hearing assessment at para malaman kung angkop sa kanya ang hearing aid. Habang naghihintay ng appointment, makakatulong ang ilang communication strategies: dapat makipag-usap sa kanya nang harapan para makita niya ang inyong mukha at lips; magsalita nang malinaw at medyo mabagal pero hindi pasigaw; at panatilihin ang mga kamay palayo sa mukha habang nagsasalita. Para sa inyong tahanan, iminumungkahi ko ang paglagay ng visual cues tulad ng doorbell na may kasamang ilaw, at ang paggamit ng mga subtitle sa TV para mabawasan ang pangangailangan sa malakas na volume. Pag-usapan din natin ang posibilidad ng mga assistive devices tulad ng amplified telephone at personal sound amplifiers na maaaring gamitin habang naghihintay ng proper hearing aid. Mahalaga ring i-explain sa pamilya na ang kanyang pagka-iritable ay maaaring dulot ng frustration sa hindi pakakaintindi at hindi dapat personal na ipagpalagay. Hinihikayat ko si Lolo na ipagpatuloy ang pakikisalamuha sa iba at huwag umiwas sa social situations, ngunit sa mas maliliit at tahimik na environment muna para hindi siya ma-overwhelm. Sa ating susunod na pagkikita, sisikapin nating paganahin ang mga daluyan ng komunikasyon upang mapabuti ang kanyang kalagayan at maibalik ang kanyang dating masayahing personalidad."
            ],
            [
                "assessment" => "Si Nanay ay nagpapakita ng matinding kahirapan sa pakikinig at pag-unawa ng mga usapan tuwing may maraming tao o kung maingay ang paligid. Sa tuwing binibisita ko siya, napansin kong normal naman ang aming usapan kapag kaming dalawa lang at tahimik ang paligid. Subalit noong isang linggo, sumama ako sa kanila sa family gathering, at doon ko napansin na tila nawala siya sa usapan nang nagsimulang magsalita nang sabay-sabay ang mga tao. Nakita ko ang frustration sa kanyang mukha habang paulit-ulit niyang tinatanong ang mga kasama kung ano ang sinasabi. Lalo itong lumala nang may nagbukas ng TV sa background. Sinabi ng kanyang anak na kahit sa simbahan ay hindi na maintindihan ni Nanay ang sermon kung hindi sila nakaupo sa harapan, at madalas na naiinis siya kapag nagtatanong sila tungkol sa sinabi ng pari. Sa bahay naman, hindi niya naririnig ang cellphone kapag tumatawag, lalo na kung nasa kusina siya at tumutugtog ang telepono sa sala. Dagdag pa rito, napansin ng pamilya na minsan ay mali ang kanyang mga sagot sa mga tanong, na para bang hindi niya narinig nang tama ang itinatanong sa kanya. Ayon sa kanyang asawa, dati ay aktibong kalahok si Nanay sa kanilang senior citizens' meetings, pero ngayon ay umiiwas na siyang pumunta dahil nahihirapan siyang sumunod sa mga diskusyon.",
                "evaluation" => "Ang nahihirapan si Nanay sa pakikinig kapag maingay ang paligid ay maaaring tanda ng age-related hearing loss na partikular na nakakaapekto sa ability niya na i-filter ang background noise—isang common na problema sa mga matatanda. Una sa lahat, iminumungkahi kong magpatingin siya sa audiologist para sa proper evaluation at para malaman kung maaari siyang makinabang sa mga hearing aids na may noise-reduction features. Habang naghihintay para sa appointment, may mga paraan tayo para mapabuti ang kanyang karanasan sa pakikinig. Sa bahay, mahalaga ang pagbabawas ng background noise: patayin ang TV o radyo kapag may nag-uusap, iwasan ang sabay-sabay na pagsasalita, at siguraduhin na maayos ang acoustics sa mga lugar na madalas niyang ginagalawan. Para sa mga social gatherings, subukang ilagay siya sa tahimik na sulok na malayo sa speakers o ingay ng kusina, at hilingin sa lahat na magsalita isa-isa. Kapag kailangan magbigay ng importanteng impormasyon, direktang kausapin siya, face-to-face, at siguraduhing nakikita niya ang inyong mukha para makatulong ang visual cues. Para sa mga regular na lugar tulad ng simbahan, maaaring hilingin na maupo sa harap o kalapit ng speakers, at kung posible, kumuha ng kopya ng sermon o readings in advance. Sa bahay, maaaring maglagay ng visual alert system para sa telepono, doorbell, at iba pang appliances. Pinapayuhan ko rin ang pamilya na maging sensitibo sa frustration ni Nanay at paganahin siyang ipagpatuloy ang pakikilahok sa mga social activities sa paraang komportable sa kanya. Sa susunod nating visit, balak kong subukan ang ilang communication strategies at turuan ang pamilya kung paano effectively makipag-usap kay Nanay para mabawasan ang kanyang pagkabigo at mapabuti ang kanyang quality of life."
            ],
            [
                "assessment" => "Si Lolo ay nakakaranas ng matinding kahirapan sa paningin sa gabi at kapag lumilipat mula sa maliwanag patungo sa madilim na lugar. Noong nakaraang linggo, sinamahan ko siya sa paglalakad pauwi nang magsimulang dumilim, at napansin ko kung paano siya biglang bumagal at tila nangangapa ang kanyang mga hakbang. Ilang beses siyang muntik nang matisod sa mga bato at bitak sa daan na hindi niya nakikita. Pagpasok sa bahay, hindi siya agad nakapag-adjust mula sa liwanag ng labas patungo sa loob, at kailangan niyang tumigil ng ilang sandali sa may pintuan hanggang sa unti-unting maging malinaw ang kanyang paningin. Ayon sa kanyang asawa, hindi na raw siya lumalabas ng bahay pagkatapos magsimulang dumilim, at natatakot na siyang maglakad sa bahay sa gabi kahit gising pa ang iba. Kapag kailangang bumangon sa gabi para pumunta sa banyo, kinakailangan munang i-on lahat ng ilaw sa daraanan. Kahit may nightlight sa kanilang kwarto, hinahanap pa rin niya ang switch ng main light bago tumayo. Bago ito mangyari, sinabi ng pamilya na walang problema si Lolo sa paglalakad sa gabi o sa mga lugar na medyo madilim gaya ng sinehan. Nabanggit din nila na nahihirapan na siyang makilala ang mga mukha sa gabi o sa mga lugar na may mababang ilaw, at madalas ay hindi niya napapansin ang mga bagay na nahuhulog sa sahig kapag madilim.",
                "evaluation" => "Ang night vision problem ni Lolo ay isang seryosong concern na kailangang ma-address agad dahil naglalagay ito sa kanya sa panganib ng pagkahulog. Una sa lahat, nirerekomenda kong magpatingin siya sa ophthalmologist upang ma-assess kung may mga kondisyon tulad ng cataracts, macular degeneration, o glaucoma na maaaring nagko-contribute sa problema. Habang naghihintay ng medical evaluation, may mga hakbang na pwedeng gawin para mapabuti ang kanyang kaligtasan at mobility. Sa bahay, mahalagang magkaroon ng adequate na ilaw sa lahat ng sulok, lalo na sa mga daanan at hagdan. Iminumungkahi ko ang strategic na paglalagay ng motion-activated lights sa hallways, banyo, at sa tabi ng kama para hindi na kailangang hanapin ang switch kapag bumabangon sa gabi. Magandang i-maximize ang natural light sa araw, at gumamit ng higher wattage bulbs sa mga lugar na madalas niyang pinupuntahan. Dapat din alisin ang mga obstacles sa daanan at siguraduhing may consistent na arrangement ang furniture para maging pamilyar siya sa layout kahit hindi gaanong malinaw ang paningin. Para sa transition mula sa maliwanag tungo sa madilim na environment, turuan si Lolo na tumigil muna at hayaang mag-adjust ang kanyang mata bago magpatuloy. Kapag lumalabas, iminumungkahi ko ang pagsusuot ng non-tinted na salamin (kung gumagamit siya) at ang paggamit ng flashlight o headlamp kahit nagsisimula pa lang dumilim. Para sa long-term management, makakatulong ang pagkakaroon ng routine ng eye exercises upang mapalakas ang kanyang visual adaptation capacity. Ipinaliwanag ko rin sa pamilya ang kahalagahan ng pag-unawa sa sitwasyon ni Lolo at na huwag siyang i-pressure na kumpleto sa mga gawain o lakad sa gabi kung hindi siya komportable."
            ],
            [
                "assessment" => "Si Nanay ay nakakaranas ng matinding problema sa paningin sa iba't ibang distansya at may kahirapan sa adjustment mula sa near vision patungo sa far vision. Napansin ko na kapag nagbabasa siya ng dyaryo o libro, malapit na malapit ito sa kanyang mukha, at madalas niyang inilalayo at inilalapit ang materyal bago niya mahanap ang tamang distansya para mabasa ito. Kahit na may suot siyang salamin, nahihirapan pa rin siyang makita ang malalayong bagay, at kapag nanonood ng TV, kailangan niyang lumapit nang husto sa screen upang makita ang detalye. Noong nakaraang linggo, hiniling niya sa akin na tingnan ang expiration date ng isang de-lata dahil hindi niya ito mabasa, at napansin ko na kahit malaki ang font, hindi pa rin niya ito makita. Sa labas naman ng bahay, nagkaroon ng ilang insidente kung saan hindi niya nakilala ang mga kakilala hanggang sa makalapit sa kanya. Kapag naglalakad, nahihirapan din siyang makita ang mga steps, curbs, o pagkakaiba sa elevation ng daan, na nagresulta sa ilang pagkakataon na natapilok siya. Ayon sa kanyang anak, dati ay mahilig si Nanay manahi at gumawa ng crochet, ngunit sa nakaraang anim na buwan, unti-unti niyang tinalikuran ang mga gawaing ito dahil sa hirap na dulot nito sa kanyang mata at sa pananakit ng ulo na madalas niyang nararamdaman pagkatapos.",
                "evaluation" => "Ang mga problema ni Nanay sa iba't ibang distansya ng paningin ay nagpapahiwatig ng posibleng presbyopia (age-related farsightedness) na pinagsamang may iba pang visual issues. Kinakailangan ng komprehensibong eye exam mula sa ophthalmologist para ma-assess kung kailangan niya ng progressive o multifocal lenses na magbibigay ng tamang correction sa iba't ibang viewing distances. Habang naghihintay ng appointment, may mga strategies na maaaring gawin para mapabuti ang kanyang functionality. Para sa pagbabasa at close work, iminumungkahi ko ang paggamit ng magnifying glass o reading lamp na may magnifier. Mainam ding gumamit ng large-print books, magazines, at kung posible, i-adjust ang font size sa kanyang cellphone o tablet para mas madaling makita. Sa bahay, siguraduhing mayroon siyang dedicated na reading area na may maayos at sapat na ilaw upang mabawasan ang eye strain. Para naman sa mga daily tasks tulad ng pagluluto at pag-inom ng gamot, puwedeng gumamit ng color-coding at malalaking labels sa mga containers at medicine bottles. Sa usapin ng safety sa paglalakad, ipinapayo ko na laging magsuot ng updated prescription glasses kapag lumalabas, at maging extra careful sa new environments o sa mga lugar na may changing elevation. Dapat ding iwasan ang pagmamaneho sa gabi o sa mga kondisyong may mababang visibility. Para sa long-term vision health, ipinapaalala ko ang kahalagahan ng regular na pagkain ng mga pagkaing mataas sa antioxidants tulad ng dark leafy greens, at pag-iwas sa paninigarilyo at labis na alak na maaaring makaapekto sa eye health. Kinausap ko rin ang pamilya tungkol sa pagbibigay ng verbal cues kapag naglalakad kasama si Nanay, lalo na sa mga lugar na may stairs o uneven surfaces para mabawasan ang risk ng pagkahulog."
            ],
            [
                "assessment" => "Si Tatay ay nakakaranas ng eye strain at matinding sakit ng ulo kapag nagbabasa o nanonood ng TV nang matagal. Sa aking pagmamasid, napansin ko na madalas siyang napapakurap at nagmamasahe ng kanyang mga mata habang nagbabasa ng dyaryo o nanonood ng kanyang paboritong TV show. Pagkalipas ng humigit-kumulang 20-30 minuto, kinakailangan na niyang ihinto ang aktibidad dahil sa pagsisimula ng sakit ng ulo, na madalas ay nakakaapekto sa kanyang nape at temples. Ayon sa kanyang asawa, nagbabasa noon si Tatay ng dalawa hanggang tatlong oras nang tuloy-tuloy, ngunit sa nakalipas na tatlong buwan, hindi na niya kayang magtagal ng higit sa 30 minuto. Napansin din niya na lagi nang mamula at maluha ang mga mata ni Tatay pagkatapos magbasa. Sa tuwing titigil si Tatay sa pagbabasa, sinasabi niyang masakit ang kanyang mata, at may pakiramdam na parang may buhangin sa loob nito. Bukod dito, nagtuturo siya ng parte sa gilid ng kanyang ulo kung saan nagsisimula ang sakit. Dahil dito, nabawasan na ang interes ni Tatay sa pagbabasa ng dyaryo at mga libro, at naglalaan na lang siya ng limitadong oras sa panonood ng TV, na dating pangunahing libangan niya. Napansin ko rin na madalas siyang humihinto sa gitna ng isang aktibidad para kuskusin ang kanyang mga mata, at minsan ay nagko-complain na malabo ang kanyang paningin pagkatapos ng ilang minuto ng screen time.",
                "evaluation" => "Ang eye strain at headaches na nararanasan ni Tatay kapag nagbabasa o nanonood ay maaaring sanhi ng maraming factors, kabilang ang need for updated prescription glasses, dry eyes, o posibleng underlying conditions tulad ng glaucoma. Iminumungkahi kong magpa-schedule agad ng appointment sa ophthalmologist para sa komprehensibong eye check-up. Habang naghihintay, maaari nating i-implement ang 20-20-20 rule: pagkatapos ng 20 minutong screen time o pagbabasa, dapat siyang tumingin sa isang bagay na nasa 20 feet ang layo ng hindi bababa sa 20 segundo, para mapahinga ang kanyang mga mata. Binigyang-diin ko rin ang pagkakaroon ng tamang ilaw sa reading area—hindi masyadong maliwanag o madilim, at dapat positioned correctly para maiwasan ang glare. Para sa TV viewing, iminungkahi ko na i-adjust ang brightness at contrast settings para maging mas komportable sa kanyang mga mata, at siguraduhing nasa tamang distansya siya mula sa screen. Nag-recommend din ako ng lubricating eye drops na maaaring gamitin kapag nagsimula siyang makaramdam ng dryness o irritation. Makakatulong din ang paggamit ng warm compress sa kanyang mga mata sa umaga at gabi para mapabuti ang tear production at maibsan ang discomfort. Sa usapin ng headaches, inirekomenda kong i-track niya kung kailan nagsisimula ito at kung may correlation sa ibang factors tulad ng pagod, gutom, o stress. Pinayuhan ko rin ang pamilya na hikayatin si Tatay na magpatuloy sa pagbabasa at panonood pero sa mas maikling intervals, upang hindi mawala ang kanyang cognitive stimulation mula sa mga activities na ito. Sa susunod na pagbisita ko, titingnan natin kung mayroon nang improvement at kung kailangan niya ng further modifications sa environment o sa kanyang habits para maibsan ang mga symptoms na ito."
            ],
            // Mental and cognitive health-related assessments
            [
                "assessment" => "Si Lolo ay nagpapakita ng lumalalang problema sa memorya at cognition na lubhang nakakaapekto sa kanyang araw-araw na pamumuhay. Napansin ko na madalas na niyang nakakalimutan ang mga kaganapan mula sa malapit na nakaraan, bagama't malinaw pa rin ang kanyang alaala sa mga pangyayari noong kanyang kabataan. Sa nakaraang linggo, tatlong beses niyang tinanong sa loob ng isang oras kung ano ang aming plano sa araw na iyon, kahit paulit-ulit kong sinasagot ang kanyang tanong. Nakakabahala rin na dalawang beses siyang nagulat nang makita ako sa bahay nila, na tila nakalimutan niyang pumunta ako para sa regular na checkup. Ang kanyang asawa ay nagkuwento na minsan ay iniwan ni Lolo ang kalan na nakabukas at naglakad sa labas ng bahay upang magtanim, at nang tanungin ay hindi niya matandaan na nagluluto siya. Napansin ko rin ang kanyang kahirapan sa paghahanap ng tamang salita habang nakikipag-usap at madalas siyang tumitigil sa gitna ng pangungusap na parang nakalimutan niya kung ano ang kanyang sasabihin. May ilang pagkakataon din na naguguluhan siya sa panahon at lugar, tulad ng pagtawag sa akin gamit ang pangalan ng kanyang dating kasamahan sa trabaho at pagtatanong kung bakit siya nasa bahay nila gayong nasa retirement home siya. Sinabi rin ng kanyang asawa na nahihirapan na si Lolo sa simpleng mathematical calculations na dati ay madali para sa kanya, tulad ng pagbabayad at pagkuwenta ng sukli sa tindahan.",
                "evaluation" => "Ang mga pagbabago sa memorya at cognition ni Lolo ay nangangailangan ng komprehensibong assessment mula sa neurologist o geriatrician para matukoy kung ito ay dulot ng normal na pagtanda o posibleng early signs ng dementia tulad ng Alzheimer's disease. Habang hinihintay ang medical evaluation, mahalaga ang paglikha ng structure at routine sa kanyang pang-araw-araw na buhay. Iminumungkahi kong gumawa ng daily schedule na nakasulat sa malaking calendar o whiteboard na madaling makikita niya, na naglalaman ng mahahalagang impormasyon tulad ng petsa, araw, at mga planong aktibidad. Para sa safety concerns, inirekomenda ko sa pamilya ang pag-install ng automatic shut-off devices para sa mga appliances tulad ng kalan, at ang pagtiyak na may ID bracelet si Lolo na naglalaman ng kanyang pangalan at contact information kung sakaling malayo siyang makapaglakad. Sa pakikipag-usap kay Lolo, mahalagang magbigay ng simpleng instructions, isa-isang hakbang, at hintayin ang kanyang tugon bago magbigay ng panibagong instruction. Kapag nahihirapan siyang humanap ng salita, bigyan siya ng sapat na oras at huwag siyang agad tutulan o itama. Para sa cognitive stimulation, inirerekomenda ko ang regular na mental exercises tulad ng puzzles na angkop sa kanyang skill level, at regular ngunit hindi nakaka-overwhelm na social activities. Ipinaliwanag ko rin sa pamilya ang kahalagahan ng pagpapanatili ng dignidad at pagrespeto kay Lolo sa kabila ng kanyang cognitive difficulties. Hindi dapat tratuhing parang bata si Lolo o pag-usapan siya na para bang wala siya sa harapan. Sa halip, patuloy siyang isali sa mga family decisions sa paraang naaangkop sa kanyang kasalukuyang kakayahan. Binigyang-diin ko rin ang kahalagahan ng self-care para sa kanyang asawa at mga tagapag-alaga, dahil ang pag-aalaga sa isang taong may cognitive decline ay maaaring maging physically at emotionally draining. Sa ating susunod na session, babalikan natin kung paano nagre-respond si Lolo sa mga ipinapanukalang interventions at kung anong adjustments ang kailangan batay sa kanyang pangangailangan."
            ],
            [
                "assessment" => "Si Nanay ay nagpapakita ng malalang sintomas ng depression na nagsimula nang pumanaw ang kanyang asawa anim na buwan na ang nakalipas. Sa nakalipas na dalawang buwan, napansin kong unti-unti siyang nag-withdraw sa kanyang mga dating aktibidad at social connections. Dati ay aktibo siya sa kanilang senior citizens' group at linggo-linggo ay nakikisali sa mga community projects, ngunit ngayon ay tumanggi na siyang dumalo kahit sa mga espesyal na okasyon. Sinabi ng kanyang anak na dalawang beses nang sinubukang sunduin si Nanay para sa family gatherings, ngunit nagdahilan siyang masama ang kanyang pakiramdam, bagama't walang nakikitang pisikal na karamdaman. Sa aking mga pagbisita, madalas kong nakikitang nakaupo lang siya sa sala, nakatitig sa malayo at hindi interesado sa TV o radyo na dati ay pinagkakaabalahan niya. Kapag kinakausap, maikli lang ang kanyang mga sagot at madalas ay sinasabi niyang 'pagod lang' siya kahit halos buong araw siyang nakaupo o nakahiga. Hindi rin gaanong kumakain si Nanay—sinabi ng kanyang anak na kalahati na lang ng dating kinakain ang kanyang nakokonsume, at bumaba na ng 7 kilos ang kanyang timbang sa loob ng tatlong buwan. Kapag tinatanong tungkol sa kanyang damdamin, madalas niyang binabanggit na 'wala nang saysay ang buhay' at minsan ay sinabi sa kanyang anak na 'mas mabuti pa kung kinuha na rin ako kasama ng iyong ama.' Napansin ko rin na parang nahihirapan siyang makatulog sa gabi, dahil madalas siyang may malalim na eye bags at napapahikab sa araw, bagama't sinasabi niyang 'masyadong mahaba ang mga araw' para sa kanya.",
                "evaluation" => "Ang mga sintomas ni Nanay ay strongly indicative ng major depressive disorder na nag-develop matapos ang pagkawala ng kanyang asawa—isang kondisyon na kilala bilang complicated grief o prolonged grief disorder. Dahil sa kalubhaan ng kanyang sintomas, lalo na ang mga pahayag tungkol sa kawalang-saysay ng buhay, inirerekomenda ko ang agarang psychiatric evaluation. Ipinaliwanag ko sa pamilya na ang depression sa matatanda ay isang seryosong medical condition na nangangailangan ng professional intervention, at hindi lang simpleng 'kalungkutan' na maaaring 'labanan' sa pamamagitan ng pagiging positibo. Habang hinihintay ang professional help, iminumungkahi ko ang paggawa ng gentle routine na may structured activities para kay Nanay. Mahalagang huwag siyang i-pressure na agad bumalik sa dating social activities, pero unti-unti siyang hikayating sumali sa mga small, manageable social interactions. Halimbawa, pwedeng mag-umpisa sa pagkakaroon ng isang kaibigan o kamag-anak na regular na bumibisita para sa maikling panahon, at dahan-dahang palawakin ang social circle kapag komportable na siya. Para sa kanyang poor appetite, inirerekomenda ko ang pagbibigay ng small, frequent meals na nutritionally dense, at ang pagmo-monitor ng kanyang fluid intake para maiwasan ang dehydration. Hinggil sa insomnia, ipinapayo ko ang pagtatatag ng consistent sleep schedule at bedtime routine, at ang pag-iwas sa mga activities na nakaka-stimulate bago matulog. Mahalaga ring bigyan si Nanay ng ligtas na espasyo para ma-express ang kanyang grief. Imbes na sabihing 'kailangan mo nang mag-move on,' hikayatin siyang pag-usapan ang kanyang asawa at mga alaala nila, at i-acknowledge ang kanyang feelings of loss. Inimungkahi ko rin ang posibilidad ng grief counseling o support group para sa mga namatayan ng asawa, kung saan makakakita siya ng ibang nakakaunawa sa kanyang pinagdaraanan. Pinaalala ko sa pamilya ang kahalagahan ng regular na pag-check in kay Nanay at ng pagiging alerto sa mga warning signs ng suicidal ideation. Hindi dapat iwanan nang matagal si Nanay nang nag-iisa sa kasalukuyan, at dapat alisin ang access sa mga potensyal na mapanganib na gamit o gamot."
            ],
            [
                "assessment" => "Si Lola ay nagpapakita ng tumataas na antas ng pagkabalisa (anxiety) at hindi mapanatag na pag-iisip, lalo na sa mga pagkakataong nag-iisa siya. Sa aking mga pagbisita sa loob ng nakaraang dalawang buwan, napansin kong patuloy na lumalala ang kanyang nervousness at pag-aalala. Sa tuwing magtatanong ako tungkol sa kanyang pang-araw-araw na gawain, paulit-ulit niyang inaalala ang mga pinagdaanan niyang hindi magagandang karanasan noong nakaraang taon—lalo na ang pagkakaospital niya dahil sa pneumonia at ang pagpanaw ng kanyang matalik na kaibigan. Ayon sa kanyang anak, madalas na hindi makatulog si Lola dahil sa kanyang mga alalahanin, at minsan ay nagigising siya sa kalagitnaan ng gabi na hinahabol ang kanyang hininga at pinagpapawisan dahil sa panic attack. Sa aking huling pagbisita, hindi mapakali si Lola: patuloy na naglalakad-lakad sa sala, kinakabahan kapag may tumutunog na telepono, at laging sinusuri kung nakasara ang mga bintana at pintuan kahit na katatapos lang niyang i-check ito. Napansin ko rin na patuloy siyang nagtitingin sa kanyang relo at inaalala kung nakainom na siya ng gamot, kahit na nakita kong ininom niya ito ilang minuto pa lang ang nakalilipas. Kapag tinatanong kung bakit siya nababahala, paulit-ulit niyang sinasabi na pakiramdam niya ay may 'masamang mangyayari,' kahit na wala namang partikular na banta o dahilan para matakot. Sa aming huling pag-uusap, nabanggit niya na minsan ay 'para siyang mababaliw' sa dami ng iniisip, at nahihirapan siyang paganahin ang kanyang isip para makapag-focus sa isang bagay.",
                "evaluation" => "Ang generalized anxiety disorder na nararanasan ni Lola ay nangangailangan ng komprehensibong approach na nagsasama ng medical, psychological, at lifestyle interventions. Una sa lahat, inirerekomenda ko ang konsultasyon sa geriatric psychiatrist o psychologist para sa formal evaluation at appropriate treatment, dahil ang matinding pagkabalisa sa mga matatanda ay maaaring magresulta sa mas malubhang komplikasyon tulad ng hypertension, insomnia, depression, at cognitive impairment. Habang naghihintay ng professional intervention, may mga praktikal na hakbang na maaari nating ipatupad kaagad. Para sa immediate management ng anxiety symptoms, tinuruan ko si Lola ng breathing exercises at simple meditation techniques na maaari niyang gawin kapag nakakaramdam siya ng pagkabalisa. Iminungkahi ko rin ang pagiging regular sa physical activity, katulad ng 15-20 minutong gentle walking araw-araw, dahil napatunayang nakakatulong ito sa pagbawas ng anxiety. Sa usapin ng information management, kinausap ko ang pamilya tungkol sa kahalagahan ng pag-regulate ng exposure ni Lola sa stressful news at social media, dahil maaaring mag-trigger ito ng karagdagang anxiety. Sa halip, hikayatin siyang makinig sa kaaya-ayang musika, manood ng light-hearted shows, o sumali sa mga recreational activities tulad ng gardening. Para sa pang-araw-araw na routine, mahalagang magtatag ng predictable schedule ng mga aktibidad, meals, at medication, dahil ang structure ay nagbibigay ng sense of control at security. Ipinapayo ko rin ang paggawa ng 'worry time'—isang designated na 15-20 minuto kada araw kung kailan pwede niyang ilabas lahat ng kanyang mga alalahanin, para hindi ito nag-occupy sa kanyang isip buong araw. Para sa kanyang pamilya, mahalagang maintindihan ang nature ng anxiety at kung paano tumugon nang maayos—huwag i-dismiss ang kanyang mga alalahanin kahit mukhang hindi makatotohanan, pero huwag din palakasin ang mga irrational fears sa pamamagitan ng overprotection o reassurance-seeking behavior. Sa ating follow-up sessions, i-monitor natin ang effectiveness ng mga interventions na ito at titingnan kung kailangan ng adjustment sa approach batay sa kanyang response."
            ],
            [
                "assessment" => "Si Tatay ay nagpapakita ng mga sintomas ng sundowning syndrome—isang phenomenon kung saan lumalala ang confusion, agitation, at behavioral problems sa mga taong may dementia kapag lumubog na ang araw. Ayon sa pamilya, ang kanyang kondisyon ay nagsimulang kapansin-pansin tatlong buwan na ang nakalilipas. Sa umaga at tanghali, kaya pa ni Tatay na makipag-usap nang maayos, kumain nang sarili, at maging cooperative sa kanyang mga routine. Ngunit kapag nagdadapithapon na, mga bandang 4-5PM, napansin kong nagbabago ang kanyang mood at behavior. Nagiging iritable siya, at patuloy na lumalaki ang kanyang pagkabahala habang dumidilim ang paligid. May mga gabing sumisigaw siya sa mga taong 'nakikita' niyang nasa sulok ng kwarto, kahit wala namang tao roon. Tuwing binibisita ko siya sa gabi, napapansin kong hindi mapakali si Tatay, palakad-lakad sa bahay na parang naghahanap o naghihintay ng isang tao. Minsan, nagagalit siya at nagsasabing kailangan na niyang umalis para 'pumasok sa trabaho' (retired na siya nang mahigit 15 taon). Ang kanyang anak ay nagkuwento na may mga gabi na sinusubukan ni Tatay na lumabas ng bahay kahit madaling araw na, at nahihirapan silang pigilan siya nang hindi nagkakaroon ng matinding altercation. Nag-aalala rin sila dahil kapag nasa peak ang kanyang agitation, humihiling si Tatay ng mga gamot na hindi naman prescribed sa kanya, nagiging suspicious sa mga tao sa bahay, at may mga pagkakataong nagiging verbally aggressive. Pinakamahirap ang oras ng pagtulog dahil walang pahinga si Tatay at laging naniniwalang may gagawin pa siyang 'trabaho' o may 'importanteng lakad' siya.",
                "evaluation" => "Ang sundowning syndrome na nararanas ni Tatay ay isang common ngunit challenging na aspeto ng dementia care na nangangailangan ng multifaceted na approach. Una sa lahat, nirerekomenda ko ang pagkonsulta sa geriatrician o neurologist upang ma-assess ang underlying dementia condition at tingnan kung may medical factors na nakakaambag sa paglala ng symptoms sa gabi, tulad ng pain, infection, o medication side effects. Habang hinihintay ang medical evaluation, may ilang environmental at behavioral strategies na maaaring gawin para mabawasan ang severity ng sundowning. Sa aspeto ng environment, iminumungkahi ko ang pagpapanatili ng maayos na ilaw sa bahay simula hapon hanggang gabi para mabawasan ang shadows at maiwasan ang sensory confusion. Mahalaga ring gawing kalming environment ang bahay sa ganitong oras—bawasan ang noise at busy activities, at iplay ang relaxing music na pamilyar kay Tatay. Para sa daily routine, pinakamaganda kung magkakaroon ng consistent na schedule, partikular na ang oras ng pagkain at pagtulog. Inirerekomenda ko ang pag-iwas sa stimulating activities, caffeine, at screen time sa mga oras bago matulog. Maaari ding maglagay ng mga familiar na larawan at meaningful objects sa kanyang paligid para magkaroon ng sense of security. Para sa mga caregivers, mahalagang maintindihan na ang agitation at confusion ay hindi sinasadyang behaviors kundi symptoms ng kanyang kondisyon. Kapag nagsisimula siyang maging agitated, effective ang gentle redirection at validation therapy—huwag itama o kalabanin ang kanyang mga false beliefs, pero i-acknowledge ang kanyang feelings at i-redirect ang conversation sa ibang topic. Mainam din ang paggamit ng comforting phrases tulad ng 'Safe ka dito' o 'Nandito lang ako para samahan ka.' Hinggil sa medication concerns, iminumungkahi ko ang pagsimula ng simple log para i-track ang mga triggers at patterns ng sundowning episodes, kasama ang effectiveness ng iba't ibang interventions—ang information na ito ay magiging valuable sa doktor para sa appropriate management plan. Bukod dito, mahalagang magkaroon ng respite plan ang primary caregiver dahil ang pag-aalaga sa taong may sundowning ay nakakapagod lalo na sa gabi."
            ],
            [
                "assessment" => "Si Nanay ay nagpapakita ng mga seryosong palatandaan ng paranoia at delusional thinking na sumisidhi sa nakaraang tatlong buwan. Sa aking mga pagbisita, naobserbahan ko na patuloy na lumalala ang kanyang mga irrational suspicions at false beliefs, na nagsisimula nang lubos na makaapekto sa kanyang well-being at sa dynamic ng pamilya. Ang pinaka-persistent na delusion ni Nanay ay ang paniniwalang ninanakawan siya ng kanyang mga gamit. Sa tuwing binisita ko siya, inilalahad niya sa akin ang mga detalyadong kwento tungkol sa mga 'magnanakaw' na pumapasok sa kanyang kwarto kapag natutulog siya para kunin ang kanyang alahas at pera. Kahit na walang ebidensya ng pagnanakaw at lahat ng kanyang mga possessions ay natatagpuan naman sa mga lugar na kanyang nakalimutan, hindi siya kumbinsido at patuloy na naniniwala sa conspiracy laban sa kanya. Nabanggit ng kanyang anak na minsan ay pinalitan ni Nanay ang lock ng kanyang kwarto at itinatago niya ang mga personal items sa mga kakaibang lugar tulad ng freezer o sa ilalim ng karpet, at pagkatapos ay nakakalimutan niya kung saan niya inilagay ang mga ito. Kamakailan, nagsimula siyang magduda kahit sa kanyang mga matagal nang kaibigan at miyembro ng pamilya. Noong nakaraang linggo, tumanggi siyang uminom ng gamot dahil naniniwala siyang sinusubukan siyang lasunin ng kanyang anak. Sa aking huling pagbisita, napagmasdan kong mayroon na rin siyang mga auditory hallucinations—naririnig niya raw ang mga boses ng mga hindi kilalang tao na pinag-uusapan siya at pinaplano ang 'pagnanakaw' ng kanyang mga ari-arian. Dahil sa mga false beliefs na ito, naging socially isolated na si Nanay, takot na siyang manatiling mag-isa sa bahay pero ayaw rin niyang ipasok ang mga kapitbahay o dating kaibigan.",
                "evaluation" => "Ang psychotic symptoms na nararanasan ni Nanay—paranoia, delusions, at hallucinations—ay nangangailangan ng immediate at komprehensibong psychiatric evaluation. Ang pagsisidhi ng ganitong symptoms sa kanyang edad ay maaaring palatandaan ng late-onset psychotic disorder, o kaya naman ay manifestation ng neurodegenerative condition tulad ng dementia with psychotic features. Ipinaliwanag ko sa pamilya ang kahalagahan ng pagkonsulta sa geriatric psychiatrist sa lalong madaling panahon dahil ang mga ganitong symptoms ay maaaring humantong sa mas malubhang problema kung hindi maagapan. Habang naghihintay ng professional intervention, binigyan ko sila ng practical guidelines para mapangasiwaan ang sitwasyon. Una, mahalagang huwag direktang kalabanin o itama ang kanyang delusional beliefs dahil ito ay maaaring magdulot ng defensive responses o pagkagalit. Sa halip, iminumungkahi ko ang validation ng kanyang feelings habang dahan-dahang nagre-redirect sa reality—halimbawa, 'Naiintindihan kong nag-aalala ka tungkol sa iyong mga alahas, pero safe ang bahay natin at nandito tayong lahat para pangalagaan ka.' Ipinapayo ko rin na panatilihin ang isang kalming environment at regular na routine para mabawasan ang stress at anxiety na maaaring nagpapalalâ ng kanyang symptoms. Mahalagang obserbahan din ang mga triggers ng kanyang paranoid episodes at regular na i-document ang frequency at intensity ng mga ito, pati na ang response sa mga intervention, dahil mahalaga ito para sa proper clinical assessment. Para sa safety concerns, iminumungkahi ko ang discrete na pag-secure ng mga potensyal na mapanganib na bagay tulad ng mga gamot at kitchen items, at ang pag-install ng mga simple security measures tulad ng door alarms para ma-monitor ang kanyang movements lalo na sa gabi. Ipinapayo ko rin na i-maintain ang consistent na pagbibigay ng kanyang regular medications sa paraang hindi threatening o suspicious, tulad ng pag-integrate nito sa mga regular meals. Binigyang-diin ko sa pamilya na ang paranoia at delusions ay symptoms ng isang medical condition at hindi personal na choice o stubbornness. Sa ganitong sitwasyon, kailangan nilang maging patient at empathetic kahit challenging, at humingi ng suporta para sa kanilang sarili tulad ng joining support groups para sa mga pamilya ng mga taong may similar conditions."
            ],
            [
                "assessment" => "Si Lolo ay nagpapakita ng matinding social withdrawal at isolating behaviors na unti-unting lumalalâ sa nakaraang anim na buwan. Sa nakalipas na mga konsultasyon namin, nakikita ko ang patuloy na pagbabago sa kanyang inclination for social engagement. Dati-rati, ayon sa kanyang pamilya, si Lolo ay kilala bilang isang aktibong miyembro ng kanilang komunidad—regular na sumasali sa mga seniors' club activities, dumadalo sa mga weekly na misa, at nagho-host pa ng mga small gatherings ng kanyang dating mga katrabaho sa bahay. Ngunit simula nang namatay ang kanyang asawa 11 buwan na ang nakararaan, unti-unti siyang nag-withdraw mula sa kanyang social circles. Sa aking mga pagbisita, madalas kong makita si Lolo na nakaupo lang sa kanyang favorite chair, nakatitig sa mga lumang litrato at halos hindi gumagalaw. Hindi na siya interesado sa mga dating hilig tulad ng pagbabasa ng dyaryo at pagtatanim sa hardin. Kapag may bumibisita, madalas niyang sinasabi na 'pagod' siya o may 'masakit' sa kanya kahit walang nakikitang pisikal na problema, at mabilis siyang lumalayo sa mga guests. Nakausap ko ang kanyang anak at ikinuwento niya na nang subukan nilang hikayatin si Lolo na sumama sa birthday celebration ng kanyang apo noong nakaraang buwan, tumanggi siya at nagkulong sa kwarto. Napansin ko rin na hindi na gaanong nagbibihis si Lolo—palagi na lang siyang nakapajama kahit tanghali na, at minsan ay napapabayaan na niya ang kanyang personal hygiene. Kapag tinatanong ko siya tungkol sa kanyang feelings, paulit-ulit niyang sinasabi na 'wala nang saysay' ang pakikipag-usap sa mga tao at mas gugustuhin na lang niyang 'maghintay' hanggang sa siya'y 'sumunod na' sa kanyang asawa.",
                "evaluation" => "Ang social isolation at withdrawal na naobserbahan kay Lolo ay nagpapahiwatig ng isang kombinasyon ng complicated grief at posibleng clinical depression na nangangailangan ng specialized intervention. Inirerekomenda ko ang pagkonsulta sa geriatric mental health specialist para sa komprehensibong psychological assessment at appropriate therapy. Ang kanyang statements tungkol sa 'paghihintay na sumunod' sa kanyang asawa ay nagpapakita ng passive suicidal ideation na kailangang seryosong bigyan ng pansin. Habang hinihintay ang professional help, maaari nating simulan ang ilang supportive strategies. Una sa lahat, mahalagang magkaroon si Lolo ng opportunity para ma-process at ma-express ang kanyang grief sa isang safe at supportive environment. Maaaring simulan ito sa pamamagitan ng gentle conversations tungkol sa kanyang asawa, mga alaala nila, at kung paano niya hina-handle ang pag-adjust sa buhay without her. Minsan, ang pagbibigay ng 'permission' para mag-grieve ay makakatulong sa isang taong nahihirapang i-process ang kanilang loss. Tungkol sa kanyang social reintegration, iminumungkahi ko ang gradual approach—simula sa maliit na interactions sa loob ng kanyang comfort zone. Halimbawa, imbes na imbitahin siyang sumama sa isang malaking family gathering, mas mainam na simulan ang isang one-on-one walk o simple na coffee time kasama ang isang close family member. Mahalaga ring ibalik siya sa mga dating activities na nagbibigay sa kanya ng sense of purpose, pero sa modified way. Kung dati ay mahilig siyang magtanim, maaaring simulan sa isang indoor plant na mangangailangan ng kanyang pag-aalaga. Para sa kanyang self-care at daily structure, inirerekomenda ko ang pagkakaroon ng simple pero regular na routine. Ang pagkakaroon ng daily schedule—kahit simple lang—ay makakatulong para magkaroon siya ng sense of normalcy at purpose. Maaaring i-involve ang mga small responsibilities na nakakapagbigay ng sense of being needed, tulad ng simpleng pagtuturo sa apo o pagtulong sa mga gawaing-bahay na kaya pa niyang gawin. Ipinapayo ko rin ang pagsasama ng light physical activity sa kanyang routine, dahil ang exercise, kahit gentle walking lang, ay may significant effect sa mood. Kinausap ko rin ang pamilya tungkol sa kahalagahan ng patience at consistency—ang recovery mula sa grief at social withdrawal ay hindi overnight process. Binigyang-diin ko rin na habang mahalagang hikayatin siya, dapat iwasan ang pressure at criticism dahil maaari itong magresulta sa further withdrawal."
            ],

            // Physical health and comfort-related assessments
            [
                "assessment" => "Si Tatay ay nakakaranas ng matinding chronic pain na nagmumula sa kanyang lower back at lumalaganap sa kanyang mga hita at binti. Sa aking pag-oobserba sa nakaraang linggo, napansin kong nagbabago ang intensity ng kanyang sakit sa buong araw—mas matindi sa umaga pagkagising at sa gabi bago matulog. Kapag tinanong kung gaano kalala ang sakit sa scale na 1-10, madalas niyang sinasabing 7-8 sa umaga at gabi, at 4-5 sa gitna ng araw kapag nasa maximum effect ang kanyang pain medication. Nakita ko rin kung paano niya hinahawakan ang kanyang lower back tuwing tatayo siya mula sa pagkakaupo, at kung paano siya gumagalaw nang dahan-dahan at may pag-iingat. Ang kanyang facial expressions—lalo na ang pagngiwi at pagsimangot—ay malinaw na nagpapakita ng discomfort. Ayon sa kanya, nakakatulong ang pag-iinat at pagbubuhat ng mabibigat na bagay sa pansamantalang pagbawas ng sakit, ngunit nagtatagal lang ito ng 15-20 minuto bago bumalik ang sakit. Dahil sa patuloy na pananakit, naaapektuhan na ang kanyang pagtulog—nagigising siya 4-5 beses sa gabi dahil sa sakit at kailangang baguhin ang kanyang posisyon. Sinabi rin niya na nahihirapan na siyang maglakad nang mahigit 10 minuto nang tuluy-tuloy dahil sa lumalalang sakit. Napansin ko rin na medyo nagiging iritable na si Tatay, lalo na kapag nasa peak ang kanyang pain at kapag kulang siya sa tulog. Ikinukuwento ng kanyang asawa na dati ay mahilig siyang maglakad-lakad sa umaga, magtanim sa hardin, at maglaro kasama ang kanyang mga apo, ngunit ngayon ay hindi na niya kayang gawin ang mga aktibidad na ito dahil sa matinding sakit.",
                "evaluation" => "Ang chronic back pain ni Tatay ay malinaw na nakakaapekto sa kanyang quality of life at functional capacity, at nangangailangan ng multimodal approach para sa effective management. Una, inirerekomenda ko ang konsultasyon sa pain specialist o rehabilitation medicine physician para sa komprehensibong assessment at para ma-update ang kanyang pain management regimen. Sa usapin ng immediate relief, pinapayuhan ko ang paggamit ng hot compress sa kanyang lower back sa umaga para mabawasan ang morning stiffness, at cold compress naman kapag may acute flare-ups o inflammation. Para sa proper body mechanics, itinuro ko kay Tatay at sa kanyang asawa ang tamang paraan ng pagtayo mula sa pagkakaupo, pag-abot ng mga bagay, at pag-angat ng mga bagay para mabawasan ang strain sa kanyang lower back. Iminungkahi ko rin ang paghahanda ng ergonomic environment sa bahay—tulad ng pagkakaroon ng upuang may adequate lumbar support, kama na hindi masyadong malambot o masyadong matigas, at ang paggamit ng lumbar roll o cushion kapag nakaupo nang matagal. Para sa long-term management, bumuo ako ng gentle exercise program na nakatuon sa back strengthening at stretching, kabilang ang modified yoga poses at low-impact activities tulad ng swimming o water therapy kung available. Tinuruan ko rin si Tatay ng pain-relief breathing techniques at basic mindfulness exercises na magagamit niya kapag matindi ang sakit. Para sa gabi, inirekomenda ko ang paggamit ng supportive pillows sa pagitan ng mga tuhod kapag natutulog sa kanyang tagiliran upang ma-maintain ang proper spinal alignment, at ang pagsunod sa regular na sleep hygiene routine. Ipinaliwanag ko rin sa pamilya na ang chronic pain ay may psychological component, at ang stress, anxiety, at depression ay maaaring magpalalâ sa pakiramdam ng sakit. Dahil dito, mahalaga ang kanyang mental health at social support. Hinihikayat ko si Tatay na ipagpatuloy ang mga social activities na kaya niyang gawin nang hindi lumalala ang sakit, at na mag-adapt ng dating hobbies para mag-accommodate sa kanyang kondisyon—tulad ng container gardening imbes na traditional gardening para mabawasan ang pangangailangang yumuko. Hiniling ko rin sa pamilya na maging understanding sa mga pagkakataong iritable si Tatay dahil sa sakit, at tulungan siyang ma-navigate ang frustrations na dulot ng kanyang mga limitations."
            ],
            [
                "assessment" => "Si Lola ay naghahayag ng matinding problema sa pagtulog at araw-gabing pagod na lubhang nakakaapekto sa kanyang pangkalahatang kalusugan at well-being. Sa aking pakikipag-usap sa kanya, sinabi niyang mahigit isang oras siyang nakahiga sa kama bago nakakatulog dahil sa mga 'gumugulo sa isip' at 'hindi mapakaling katawan.' Kapag nakatulog na, nagigising siya nang 3-4 na beses sa gabi—minsan dahil sa pangangailangang umihi, pero kadalasan ay dahil lang bigla siyang nagigising at hindi na makabalik sa pagtulog. Ayon sa kanyang apo na nakatira sa kanya, madalas na gising na si Lola nang alas-tres o alas-kuwatro ng madaling araw, at kung minsan ay naririnig siyang naglalakad-lakad sa bahay o nanonood ng TV sa ganoong oras. Sa umaga, laging pagod at inaantok si Lola, at madalas na hirap siyang manatiling gising pagkatapos ng tanghalian. Napansin ko rin na dalawa o tatlong beses siyang nakakatulog sa araw, na tumatagal ng 30 minutos hanggang isang oras bawat tulog. Bukod dito, nakita ko ang mga pagbabago sa kanyang behavior—mas nagiging iritable siya, nahihirapang mag-concentrate, at minsan ay nagkakalimot ng mga bagay na kababanggit lang. Inobserbahan ko ang mga gawi ni Lola bago matulog at napansin kong umiinom siya ng kape hanggang gabi, at minsan ay nanonood ng TV o gumagamit ng cellphone bago matulog. Sinuri ko rin ang kapaligiran ng kanyang kwarto at napansin na malakas ang ilaw mula sa streetlight na pumapasok sa kanyang bintana, at medyo mainit at hindi komportable ang temperatura ng kwarto. Nabanggit din ng kanyang apo na may mga araw na tumatanggi si Lola na kumain nang maayos o gumawa ng normal na mga aktibidad dahil sa sobrang pagod, at minsan ay nalilito siya sa kung anong petsa o araw na.",
                "evaluation" => "Ang sleep disturbance ni Lola ay isang complex issue na maaaring may biological, environmental, at psychological factors. Inirerekomenda ko ang comprehensive sleep assessment mula sa geriatric specialist o sleep medicine doctor para ma-rule out ang specific sleep disorders tulad ng sleep apnea o restless leg syndrome. Habang naghihintay ng medical evaluation, maraming praktikal na hakbang na maaaring gawin para mapabuti ang kanyang sleep hygiene at quality. Una, mahalagang magtatag ng consistent sleep schedule—parehong oras ng pagtulog at paggising araw-araw, pati na sa weekends. Iminumungkahi ko ang pag-iwas sa caffeine sa hapon at gabi, kaya dapat limitahan ang pag-inom ng kape, tsaa, at soft drinks pagkatapos ng tanghalian. Sa usapin ng environment, binigyan ko ng mga rekomendasyon para gawing mas conducive sa pagtulog ang kanyang kwarto: blackout curtains para sa bintana, maintaining ng komportableng temperatura (24-25°C), at paggamit ng white noise machine kung may ingay mula sa labas. Para sa evening routine, iminumungkahi ko ang pagkakaroon ng relaxing activities 1-2 oras bago matulog, tulad ng pagbabasa, pakikinig ng kalming music, o gentle stretching. Mahalaga ring iwasan ang electronic screens (TV, cellphone, tablet) nang hindi bababa sa isang oras bago matulog dahil ang blue light mula sa mga ito ay maaaring mag-interfere sa natural na production ng melatonin. Para sa mga sandaling hindi siya makatulog o nagigising sa gitna ng gabi, tinuruan ko si Lola ng relaxation techniques gaya ng progressive muscle relaxation at deep breathing exercises. Kung hindi siya makabalik sa pagtulog sa loob ng 20 minuto, inirerekomenda kong tumayo muna siya, pumunta sa ibang silid, at gumawa ng isang relaxing activity hanggang sa makaramdam siyang muli ng antok. Para sa daytime management, iminumungkahi ko ang pag-iwas sa mahabang naps, lalo na pagkatapos ng 3PM. Sa halip, magkaroon ng structured na schedule sa araw na may light physical activities para naturally na mapagod ang katawan. Pinag-usapan din namin ng pamilya ang kahalagahan ng regular na pagmo-monitor sa kanyang mood at cognition, dahil ang chronic sleep deprivation ay maaaring magmanifest bilang mood disorders o cognitive impairment. Ipinaliwanag ko rin sa kanila na may mga non-prescription sleep aids na maaaring isaalang-alang (tulad ng melatonin supplements), ngunit dapat muna itong pag-usapan sa doktor dahil maaaring may interaction sa iba niyang gamot."
            ],
            [
                "assessment" => "Si Nanay ay nagpapakita ng malaking pagbabago sa kanyang cognitive function at behavior na naobserbahan ko sa nakaraang tatlong pagbisita. Nitong huling dalawang buwan, napansin kong unti-unting lumalala ang kanyang confusion at disorientation. Noong una, paminsan-minsan lang siyang naliligaw ng landas ng usapan o nakakalimutan ang detalye ng mga kaganapan sa nakaraang linggo, ngunit ngayon ay madalas na niyang hindi maalala ang mga pangyayari mula sa parehong araw. Sa aking pagbisita kahapon, tinanong niya ako nang tatlong beses kung sino ako at bakit ako nandoon, kahit na regular ko na siyang binibisita sa loob ng dalawang taon at nasagot ko na ang kanyang tanong sa bawat pagkakataon. Nakita ko ring naglalakad siya sa bahay na tila naghahanap ng isang bagay, at nang tanungin kung ano iyon, hindi niya matandaan kung ano ang hinahanap niya. Ayon sa kanyang anak, minsan ay nalilito si Nanay sa oras, akala niya ay umaga na kahit gabi na, at naghahanda na para matulog kahit kalagitnaan pa lang ng araw. May mga pagkakataon din na naguguluhan siya sa lugar—isang beses ay na-distress siya at sinabing gusto na niyang 'umuwi' kahit nasa sarili niya siyang bahay. Malaking pagbabago rin ang napansin ko sa kanyang personality at behavior. Dati ay mahinahon at matiyaga si Nanay, ngunit ngayon ay mabilis siyang mairita at magalit, lalo na kapag kinukwestiyon o itinatama. Noong nakaraang linggo, nagkaroon siya ng matinding agitation nang hindi niya makita ang kanyang wallet (na natagpuan sa ref), at nang subukang tulungan siya ng kanyang anak, sinigawan niya ito at pinagbintangan na ninakaw ang kanyang pera. Nabanggit din sa akin na nagkaroon na siya ng ilang episodes ng paranoia, kung saan nag-iisip siya na may nagnanakaw ng kanyang mga gamit o may taong pumapasok sa bahay nila sa gabi. Bukod dito, napansin ko ang kanyang kahirapan sa language at communication—madalas siyang humihinto sa gitna ng pangungusap na tila nakalimutan ang sasabihin, at minsan ay gumagamit ng maling salita o hindi maipaliwanag ang kanyang ibig sabihin.",
                "evaluation" => "Ang pattern ng cognitive decline, confusion, disorientation, at personality changes na nakikita kay Nanay ay strongly suggestive ng dementia, posibleng Alzheimer's disease o ibang form tulad ng vascular dementia. Dahil sa kalubhaan at progression ng symptoms, inirerekomenda ko ang immediate neurological assessment at comprehensive geriatric evaluation. Mahalaga ang early diagnosis para masimulan agad ang appropriate interventions at medication kung kinakailangan. Habang hinihintay ang medical evaluation, may mga strategies na maaari nating gawin upang mapabuti ang kanyang daily functioning at mabawasan ang behavioral issues. Una, mahalagang i-structure ang environment para maging simple, predictable, at safe. Iminumungkahi ko ang paglalagay ng orientation cues sa bahay—malaking calendar at orasan sa mga common areas, label sa mga pinto ng silid, at night lights para mabawasan ang confusion sa gabi. Para sa communication, itinuro ko sa pamilya ang specific techniques: paggamit ng simple at maikling sentences; pagbibigay ng isa-isang instruction lang sa isang pagkakataon; pagsasalita nang malumanay at may pasensya; at pag-iwas sa pagtataas ng boses o pagmamadali kay Nanay. Sa usapin ng behavioral management, ipinaliwanag ko na ang mga agitation episodes ay kadalasang triggered ng confusion, fear, o unmet needs. Kaya mahalagang i-identify ang mga triggers at proactively address ang mga ito. Halimbawa, kung nagkakaroon siya ng agitation kapag hindi niya makita ang kanyang wallet, magandang magkaroon ng designated place para dito at regular na tiyaking nandoon ito. Para sa episodes ng paranoia, hindi effective ang direct contradiction ('Hindi totoo iyan' o 'Walang nagnanakaw')—sa halip, i-acknowledge ang kanyang feelings at i-redirect ang kanyang attention ('Naiintindihan kong nag-aalala ka, pero safe ka dito. Halika, tingnan natin ang garden mo'). Binigyang-diin ko rin ang kahalagahan ng maintaining routine at structure sa araw-araw na pamumuhay ni Nanay. Ang regular na schedule ng meals, medication, hygiene, activities, at bedtime ay makakatulong para mabawasan ang confusion. Para sa long-term care planning, kinausap ko ang pamilya tungkol sa kahalagahan ng advanced care planning habang malinaw pa ang mga moments ni Nanay, at ang pangangailangan na i-anticipate ang increasing care needs sa hinaharap. Inilatag ko rin ang availability ng support services tulad ng respite care at support groups para sa mga pamilya ng taong may dementia, dahil ang pag-aalaga sa kondisyong ito ay maaaring maging exhausting at emotionally taxing."
            ],
            [
                "assessment" => "Si Lolo ay nagpapakita ng lumalalang respiratory distress at breathing difficulties na naobserbahan ko sa nakaraang dalawang linggo. Sa aking unang pagbisita, napansin kong medyo hingal siya pagkatapos maglakad mula sa kama patungo sa sala, isang distansya na humigit-kumulang 10 metro lamang. Ngunit sa aking pagbisita kahapon, napansin kong kahit sa pagbangon lang mula sa pagkakaupo ay nakakaranas na siya ng kakapusan ng hininga at kailangang huminto at magpahinga bago magpatuloy. Sa aking pag-assess, naobserbahan ko ang kanyang increased respiratory rate na 24-26 breaths per minute habang nakaupo, na lumalala sa 32-34 breaths per minute matapos ang minimal exertion. Kapag nahihirapan siyang huminga, nakikita ko ang paggamit ng kanyang accessory muscles—nag-flare ang kanyang nostrils at gumagalaw nang husto ang kanyang abdominal muscles. Napansin ko rin ang bluish discoloration (cyanosis) ng kanyang lips at nail beds tuwing nasa peak ang kanyang breathing difficulty. Sa pakikipag-usap sa kanya, hindi siya nakakakompleto ng buong pangungusap nang hindi humihinto para huminga. Naobserbahan ko rin ang produktibo niyang pag-ubo na may makapal at yellowish na plema, lalo na sa umaga pagkagising. Ayon sa kanyang anak, nagkaroon si Lolo ng low-grade fever (37.8°C) sa nakaraang apat na araw, nasusuka, at nawalan ng gana sa pagkain. Sa gabi, hirap siyang humiga nang flat sa kama at kailangan ng tatlo o apat na mga unan para ma-elevate ang kanyang upper body, at kahit ganito ay nagigising siya ilang beses sa gabi dahil sa hirap sa paghinga. Kapag tinanong kung saan siya nahihirapan, itinuro niya ang kanyang dibdib at sinabing 'mabigat at masikip' doon.",
                "evaluation" => "Ang respiratory distress na nararanasan ni Lolo ay isang urgent medical concern na nangangailangan ng immediate intervention. Base sa mga naobserbahang symptoms—increased respiratory rate, accessory muscle use, productive cough with colored sputum, fever, at cyanosis—posible itong indikasyon ng acute respiratory infection tulad ng pneumonia, exacerbation ng chronic condition tulad ng COPD, o posibleng cardiac-related issue. Inirekomenda ko ang agarang pagpapatingin sa emergency room o urgent care center para sa comprehensive medical evaluation, lalo na dahil lumalala ang kanyang kondisyon, at lalong nakakaalarma ang presence ng cyanosis. Habang hinihintay ang medical transport, binigyan ko siya ng immediate relief measures, tulad ng pagtulong sa kanya na umupo sa upright position na may proper support sa likod at mga braso, at binigyang-diin ko sa pamilya ang kahalagahan ng pagbibigay ng prescribed medications, kung mayroon. Nakipag-coordinate ako sa kanyang pamilya para masiguradong may magdala ng kanyang current medications, medical records, at listahan ng allergies sa hospital. Para sa short-term management pagkatapos ng medical intervention, iminumungkahi ko ang pagkakaroon ng proper positioning sa bahay—laging naka-semi-Fowler's position (naka-elevate ang upper body), lalo na sa pagtulog. Kailangan ding masiguro ang proper hydration, maliban na lang kung may fluid restriction siyang iniutos ng doktor, dahil makakatulong ito para mas maging loose ang kanyang secretions. Sa usapin ng air quality, ipinapayo ko na masiguradong malinis at well-ventilated ang kanyang living space—iwasan ang mga irritants tulad ng usok, matapang na amoy, at allergens na maaaring magpalala ng kanyang respiratory issues. Para sa pangmatagalang pangangalaga matapos ang immediate medical management, iminumungkahi ko ang paggawa ng home care plan na nakatuon sa: regular monitoring ng vital signs, lalo na ang respiratory rate at oxygen saturation kung posible; pagkakaroon ng organized medication schedule; integration ng pulmonary rehabilitation exercises kapag appropriate na; at pagpapalakas ng immune system sa pamamagitan ng balanced nutrition. Kinausap ko rin ang pamilya tungkol sa kahalagahan ng pagtukoy ng early warning signs ng respiratory distress para maagapan ang future episodes at maiwasan ang emergency situations."
            ],
            [
                "assessment" => "Si Nanay ay nakakaranas ng malalang sintomas ng peripheral edema at circulatory issues na lumalala sa nakaraang dalawang buwan. Sa aking pagbisita kahapon, naobserbahan ko ang matinding pamamaga ng kanyang mga paa, ankles, at lower legs. Ang swelling ay bilateral (pareho ang mga paa) pero mas pronounced sa kanang paa. Kapag pinipindot ko ang edematous areas, nag-i-indent ito at mabagal na bumabalik sa normal (3+ pitting edema), at ayon kay Nanay, lumalala ang pamamaga sa dulo ng araw at bahagyang bumababa pagkatapos ng overnight rest. Napansin ko rin ang pagbabago ng kulay ng kanyang lower extremities—medyo brownish at may areas ng discoloration, lalo na sa paligid ng ankles. Mayroon ding dry, flaky skin sa mga affected areas, at complained si Nanay ng periodic itchiness. Bukod sa physical signs, nagrereport si Nanay ng sensations of heaviness, aching, at occasional sharp pain sa kanyang lower legs, lalo na kapag matagal siyang nakatayo o nakaupo. Sinabi niya rin na nakakaramdam siya ng leg cramps sa gabi na nakakaapekto sa kanyang tulog. Tinanong ko siya tungkol sa kanyang mobility, at ikinuwento niya na unti-unti na niyang binabawasan ang kanyang paglalakad at ibang physical activities dahil sa discomfort at hirap sa paggalaw. Ayon sa kanyang anak, hindi na raw sumasakit masyado ang mga paa ni Nanay kapag naka-elevate ito, kaya't mas madalas na siyang nakaupo sa bahay na may nakapatong na paa sa footstool. Napansin ko rin na nagsusuot siya ng maluwag na tsinelas imbes na mga tamang sapatos dahil hindi na raw kasya ang dating mga sapatos niya dahil sa pamamaga. Nahalata ko rin na nag-aalala si Nanay tungkol sa kanyang kondisyon at nagtatanong kung reversible pa ba ang swelling o permanente na.",
                "evaluation" => "Ang persistent peripheral edema at circulatory issues ni Nanay ay nangangailangan ng comprehensive medical evaluation para matukoy ang underlying cause, na maaaring vascular insufficiency, heart failure, kidney problems, lymphatic issues, o medication side effects. Inirekomenda ko ang agarang pagpapatingin sa physician para sa proper diagnosis at treatment plan. Habang hinihintay ang medical consultation, may mga immediate interventions na maaaring gawin para maibsan ang kanyang discomfort. Una, binigyan ko ng guidelines ang pamilya tungkol sa proper leg elevation—dapat mas mataas ang mga paa kaysa sa level ng heart nang hindi bababa sa 30 minutos, 3-4 na beses sa isang araw, at lalo na sa gabi habang natutulog. Para sa positioning, nagbigay ako ng specific instructions para sa proper placement ng mga pillows o cushions para masiguro ang adequate na elevation na hindi nagsa-strain sa likod. Iminungkahi ko rin ang gentle exercises na makakatulong sa circulation, tulad ng ankle pumps, ankle rotations, at simple leg stretches na maaari niyang gawin habang nakaupo. Nagbigay din ako ng demonstration at guidelines para sa gentle massage techniques na maaaring gawin ng pamilya para makatulong sa fluid movement mula sa extremities pabalik sa central circulation, na dapat gawin in an upward motion. Sa usapin ng skin care, mahalagang panatilihin ang cleanliness at moisture ng affected areas para maiwasan ang dryness, cracking, at potential infection. Iminungkahi ko ang paggamit ng mild, fragrance-free moisturizers at pag-iwas sa mga produktong may strong chemicals. Para sa kanyang mobility concerns, binigyang-diin ko ang kahalagahan ng balance—dapat iwasan ang prolonged standing o sitting, pero hindi rin magandang maging completely sedentary. Regular na movement at short walks ay makakatulong para mapabuti ang circulation. Tungkol sa kanyang footwear, iminungkahi ko ang paghahanap ng adjustable, supportive na sapatos imbes na tsinelas, para ma-accommodate ang swelling habang nagbibigay ng adequate support. Bukod dito, tinuruan ko ang pamilya kung paano i-monitor ang edema gamit ang simple techniques tulad ng marking the outline ng kanyang feet at paggamit ng measuring tape para i-track ang changes. Binigyang-diin ko rin ang kahalagahan ng pag-track ng ibang factors na maaaring nakaka-influence sa edema tulad ng diet (lalo na ang salt intake), fluid intake, at environmental temperature. Sa usapin ng longer-term management, ipinapayo ko ang pagkakaroon ng proper medical evaluation para ma-consider ang possible use ng compression stockings, kung appropriate, at medication review para matiyak na walang umiinom na gamot si Nanay na maaaring nagko-contribute sa kanyang edema."
            ],
            [
                "assessment" => "Si Lola ay nagpapakita ng symptoms ng gastrointestinal distress at nutritional concerns na patuloy na lumalala sa nakaraang tatlong buwan. Sa aking mga regular na pagbisita, nakikita ko ang kanyang patuloy na pagbaba ng timbang—ayon sa aming pagmo-monitor, bumaba siya ng 7 kilograms mula noong unang bahagi ng quarter. Napansin ko rin ang visible na changes sa kanyang appearance—naging loose ang dating well-fitting na mga damit, at halatang humuhulma ang mga facial bones dahil sa pagkawala ng subcutaneous fat. Sa pakikipag-usap sa kanya, inilahad ni Lola ang kanyang persistent digestive issues—nakakaranas siya ng heartburn at reflux symptoms halos araw-araw, lalo na 20-30 minutos pagkatapos kumain. Nagrereklamo rin siya ng madalas na pagkahilo at sakit ng tiyan na inilalarawan niya bilang 'cramping' at 'burning sensation' sa upper at middle abdomen. Dahil sa symptoms na ito, unti-unti niyang binabawasan ang kanyang food intake—sa halip na tatlong meal sa isang araw, kumakain na lang siya ng maliit na portion 1-2 beses sa isang araw dahil natatakot siyang magkasakit. Napansin ko rin ang kanyang altered food preferences—umiiwas na siya sa mga pagkaing dating paborito niya tulad ng mga pritong pagkain at matatamis, at mas pinipili niya ang mga bland na pagkain tulad ng lugaw at sopas. Ayon sa kanyang anak, bukod sa pagbabago sa pagkain, nagkaroon din ng changes sa kanyang bowel movement—nagkakaroon siya ng constipation na tumatagal ng 3-4 na araw, na sinusundan ng loose bowel movement. Sa aking huling pagbisita, napansin ko rin na medyo maputla si Lola at madaling mapagod, at minsan ay nahihilo siya kapag mabilis na tumayo mula sa pagkakaupo.",
                "evaluation" => "Ang gastrointestinal symptoms at significant weight loss na nararanasan ni Lola ay nangangailangan ng comprehensive medical evaluation para ma-rule out ang serious underlying conditions tulad ng gastrointestinal ulcers, malabsorption syndromes, o posibleng malignancies. Inirerekomenda ko ang agarang pagkonsulta sa gastroenterologist para sa proper diagnostic work-up. Habang hinihintay ang medical assessment, may mga nutrition-focused interventions na maaari nating simulan. Una, ipinapayo ko ang mga modifications sa kanyang diet pattern—sa halip na kakaunti at malalaking meals, mas mainam ang frequent, small meals (5-6 na beses sa isang araw) para mabawasan ang burden sa kanyang digestive system. Para sa meal composition, iminumungkahi ko ang pagbawas ng high-fat, acidic, at spicy foods na maaaring mag-trigger ng kanyang reflux symptoms. Pinapayuhan ko rin ang pag-iwas sa caffeine, alcohol, at carbonated beverages na maaaring magpalala ng gastric acid production. Para sa management ng reflux symptoms, inirekomenda ko na manatiling nakaupo nang tuwid sa loob ng 30 minutos pagkatapos kumain, at ang pag-elevate ng upper body habang natutulog sa pamamagitan ng paglalagay ng mga unan o pag-adjust ng kama. Upang matugunan ang nutritional deficiencies dahil sa decreased intake, ibinigay ko ang mga rekomendasyon para sa nutrient-dense pero madaling ma-digest na foods, tulad ng protein smoothies, fortified cereals, at nutrient-rich soups. Tungkol sa constipation issues, iminungkahi ko ang gradual increase ng dietary fiber (soluble fiber muna para hindi ma-irritate ang tiyan), adequate hydration, at regular physical activity ayon sa kanyang tolerance. Bukod dito, tinuruan ko ang pamilya kung paano secret-monitor ang kanyang nutritional status at digestive symptoms gamit ang simple na food and symptom diary, na magiging valuable para sa mga healthcare professionals sa kanyang paparating na assessment. Mahalagang ma-document ang timing ng symptoms, mga pagkaing na-consume, at anumang factors na nagpapalala o nagpapagaan ng symptoms. Iminumungkahi ko rin ang pagpapakonsulta sa registered dietitian para sa personalized nutrition plan na sasagot sa kanyang specific nutritional needs habang ina-address ang kanyang digestive issues. Para sa interim symptom management, pinayuhan ko ang pamilya na kausapin ang kanyang primary care physician tungkol sa posibleng paggamit ng over-the-counter antacids o acid reducers, pero binigyang-diin ko na dapat temporary lang ito habang hinihintay ang komprehensibong medical assessment. Hinggil sa kanyang postural dizziness, nirerekomenda ko ang paunti-unting pagtayo mula sa pagkakaupo o pagkakahiga para mabigyan ng pagkakataon ang kanyang blood pressure na ma-adjust. Ipinaliwanag ko rin sa pamilya na mahalagang mag-monitor ng signs ng dehydration tulad ng dry mouth, decreased urination, at increased dizziness, lalo na dahil may risk ng inadequate fluid intake dahil sa kanyang reduced oral intake."
            ],
            // Medication management assessments
            [
                "assessment" => "Si Nanay ay nagpapakita ng matinding kahirapan sa pagsunod sa komplikadong medication regimen niya na binubuo ng 12 na iba't ibang gamot sa araw. Sa aking mga pagbisita, napansin ko na hindi niya maayos na naiintindihan kung kailan dapat inumin ang bawat gamot—may mga tableta na dapat inumin bago kumain, may mga dapat kasabay ng pagkain, at may mga dapat 2 o 3 beses sa isang araw. Noong tinanong ko siya kung paano niya natatandaan ang schedule, ipinakita niya sa akin ang isang lumang shoebox na puno ng iba't ibang bote at blister packs ng gamot, na marami ang nakasulat sa maliliit na letra na nahihirapan siyang basahin. Napansin ko ring may mga expired na gamot na kasama pa rin sa kanyang koleksyon. Sinubukan niyang ipaliwanag sa akin ang kanyang pag-inom ng gamot, pero nali-lito siya sa mga pangalan at sa kung ano ang para saan. Ayon sa kanyang anak, minsan ay double dose ang naiinom ni Nanay dahil nakakalimutan niyang uminom na siya, at may mga araw naman na nalilimutan niyang uminom ng ilang gamot. Kamakailan, napag-alaman na ang isa sa mga gamot na para sa kanyang hypertension ay nadoble ang dose dahil dalawang magkaibang doktor ang nagreseta sa kanya ng parehong gamot na may iba't ibang generic name. Mayroon din siyang mga gamot na paulit-ulit na kinukuha sa botika kahit hindi pa niya naubos ang dating supply. Sinabihan ako ng kanyang anak na madalas ding nagre-reklamo si Nanay tungkol sa mga side effects ng mga gamot, tulad ng pagkahilo, panunuyo ng bibig, at kawalan ng gana sa pagkain, pero hindi niya alam kung aling gamot ang nagdudulot nito.",
                "evaluation" => "Ang komplexity ng medication regimen ni Nanay at ang kanyang kahirapan sa pag-manage nito ay nagpapataas ng risk para sa medication errors, adverse effects, at poor health outcomes. Kailangang-kailangan ang komprehensibong medication review at simplification ng kanyang regimen. Unang-una, inirerekomenda ko ang pagkonsulta sa geriatrician o pharmacist para sa medication reconciliation at assessment ng posibleng drug interactions o inappropriate medications. Mahalaga na mai-consolidate ang lahat ng kanyang reseta sa iisang healthcare provider o sa isang pharmacy para ma-monitor ang potensyal na contraindications o duplications. Para sa agarang intervention, binuo ako ng personalized medication management system para kay Nanay. Gumawa ako ng weekly pill organizer na may clearly labeled compartments para sa umaga, tanghali, at gabi, at tinulungan ko siyang punuin ito kasama ang kanyang anak. Nagdisenyo rin ako ng simplified medication chart na may malaking letra, mga kulay para sa pag-identify ng bawat gamot, at mga simbolo (tulad ng plato para sa gamot na dapat inumin kasabay ng pagkain). Tinanggal namin ang lahat ng expired at duplicate medications, at ibinigay ang mga ito sa proper disposal. Para sa mga side effects na kanyang nararanasan, nagtala kami ng isang symptom diary para ma-track kung kailan nangyayari ang mga ito at kung anong gamot ang posibleng dahilan. Binigyang-diin ko sa pamilya ang kahalagahan ng regular monitoring—pagsusuri ng mga vital signs tulad ng blood pressure at blood sugar (kung applicable) para matasa kung effective ang mga gamot. Para sa long-term strategy, kinausap ko ang pamilya tungkol sa pagtatalaga ng isang specific family member na responsable sa weekly medication setup. Bilang karagdagan, inirerekomenda kong magkaroon ng regular na (quarterly) medication review sa kanyang doctor para matiyak na lahat ng gamot ay necessary pa rin, at para ma-consider ang posibilidad ng dose reductions o discontinuation ng ilang medications. Hinimok ko rin ang pamilya na gumamit ng medication reminder apps o alarms para sa tamang timing ng pag-inom, at na mag-maintain ng updated medication list na madaling dalhin sa mga doctor appointments para maiwasan ang future duplication issues."
            ],
            [
                "assessment" => "Si Lolo ay nagpapakita ng malalang problema sa medication adherence dahil sa kanyang lumalalang memory issues at difficulty understanding treatment plans. Sa nakaraang dalawang buwan, napansin ng pamilya na madalas niyang nalilimutan na ininom na niya ang kanyang maintenance na gamot para sa diabetes at hypertension. Madalas, o hindi niya maalala kung uminom na siya o hindi, na nagresulta sa irregular na dosing at fluctuations sa kanyang blood sugar at blood pressure readings. Ayon sa kanyang asawa, minsan ay umabot sa tatlong araw na hindi niya naalala na uminom ng kanyang gamot para sa cholesterol, at noong pinaalalahanan siya, nagalit siya at naniniwalang kataka-takang kinuha ng ibang tao ang mga gamot niya. Sa aking obserbasyon habang binisita ko sila, nakita ko ang kanyang gamot na nakakalat sa iba't ibang lugar sa bahay—may ilang tableta sa kusina, may mga bote sa kanyang kwarto, at may mga nahulog na pills sa ilalim ng kanyang mesa. Kapag tinatanong ko si Lolo tungkol sa purpose ng bawat gamot, halos wala siyang masabi tungkol sa iba, at sa ilang gamot naman ay nagbibigay siya ng maling impormasyon. Sinabi rin ng kanyang anak na mayroong mga pagkakataon na tinatanggihan ni Lolo na inumin ang kanyang gamot dahil 'indi naman daw siya maysakit' o 'hindi niya kailangan ng mga chemicals na iyan.' Minsan din, sinimulan niyang inumin ang gamot ng kanyang asawa dahil akala niya ay para sa kanya ito. Bukod dito, napansin ko ang mga markings sa ilang bote na maling dinudouble-dose ni Lolo ang ilang medications ayon sa kanyang sariling desisyon dahil sa paniniwala niyang 'mas mabisa ito kung mas marami.'",
                "evaluation" => "Ang mga isyu ni Lolo sa medication adherence ay maaaring humantong sa serious health complications kung hindi maagapan. Kailangan ng structured at supervised approach sa medication management. Una sa lahat, inirerekomenda ko ang cognitive assessment upang matukoy kung gaano kalala ang memory impairment at executive dysfunction ni Lolo, dahil ang kanyang confusion at defensiveness ay maaaring indications ng early dementia o cognitive decline. Habang hinihintay ang formal assessment, binuo ko ang isang immediate medication management plan para sa pamilya. Nirekomendasyon ko ang pag-centralize ng lahat ng gamot sa iisang secure pero visible location na madaling ma-access ng mga caregivers pero hindi direktang accessible kay Lolo para maiwasan ang unsupervised medication use. Nagbigay ako ng locked medication box na may timer at alarm para sa pang-araw-araw na gamot. Para sa implementation ng medication schedule, ginamit ko ang simpleng visual aids at color-coding system para kay Lolo, at isang detailed medication administration log para sa mga caregivers upang maiwasan ang double-dosing o missed doses. Dahil sa kanyang resistance sa pag-inom ng gamot, tinuruan ko ang pamilya ng effective communication techniques: pagpapaliwanag sa simpleng paraan ng purpose ng bawat gamot; pag-iwas sa arguments tungkol sa pangangailangan sa medication; at pagkonekta ng medication sa mga goals na mahalaga sa kanya (tulad ng 'para makasama mo nang mas matagal ang mga apo mo' o 'para makapaglakad ka pa rin sa garden'. Binigyang-diin ko sa pamilya ang kahalagahan ng consistent supervision sa medication intake—kinakailangan na makita nila na nilulunok ni Lolo ang mga tableta at hindi itinatabi. Para sa long-term management, inirerekomenda ko ang regular na pagdalo ng primary caregiver sa doctor appointments ni Lolo para matiyak ang accurate na information transfer, at ang paggamit ng medication reconciliation forms na ida-update sa tuwing magkakaroon ng pagbabago sa gamot. Tinalakay ko rin ang posibilidad ng simplification ng medication regimen—baka maaaring mabawasan ang frequency ng doses o gumamit ng combination medications kung appropriate. Bukod dito, nirerekomenda ko ang weekly pre-filling ng pill organizers ng isang designated na caregiver, at ang paggawa ng system para sa regular monitoring ng therapeutic effects at side effects ng mga gamot para matiyak na nakukuha ni Lolo ang tamang benepisyo mula sa kanyang medications."
            ],
            [
                "assessment" => "Si Tatay ay nakakaranas ng matinding side effects at adverse reactions sa kanyang mga kasalukuyang medications, na lumalala sa nakaraang 6 na linggo. Sa aking mga follow-up visits, patuloy siyang nagrereklamo ng persistent dry mouth, dizziness, at excessive drowsiness na nagsisimula 30-60 minutos matapos uminom ng kanyang morning medications. Sinabi niya na minsan ay sobrang lala ng kanyang pagkahilo na pinipigilan siyang maglakad nang maayos at natutumba siya, na nagresulta sa isang minor fall noong nakaraang linggo. Bukod dito, napansin niya ang paglala ng kanyang constipation mula nang nagsimula ang bagong gamot para sa kanyang Parkinson's disease, kung saan 4-5 araw na siyang hindi nakakadumi at nakakaramdam ng severe abdominal discomfort. Ikinuwento rin niya sa akin na nagsimula siyang makaranas ng matinding pangangalay ng mga binti at muscle cramps sa gabi, na nagdudulot ng disrupted sleep at pagod sa umaga. Tungkol naman sa kanyang gastro-intestinal functions, nagkaroon siya ng reduced appetite at occasional nausea, na ayon sa kanya ay nagsimula matapos idagdag ang bagong anti-inflammatory medication. Bukod sa physical symptoms, nagpapakita rin si Tatay ng subtle cognitive changes—nahihirapan siyang mag-concentrate sa mga simpleng tasks at nagkakaroon ng occasional confusion, lalo na sa mga oras na nasa peak ang effect ng kanyang pain medications. Sa pakikipag-usap sa pamilya, nalaman ko na hindi nila alam na dapat i-report ang mga side effects na ito sa doctor, at nagtitiis na lamang si Tatay dahil iniisip niyang normal lang ito bilang parte ng pagtanda. Nadiskubre ko rin na bumili si Tatay ng over-the-counter medications para gamutin ang kanyang constipation at muscle cramps nang hindi inireport sa kanyang doktor, at posibleng nagkaroon ng interaction sa kanyang regular na gamot.",
                "evaluation" => "Ang mga adverse reaction at medication side effects na nararanasan ni Tatay ay hindi dapat i-consider na normal na bahagi ng pagtanda at nangangailangan ng immediate attention. Inirerekomenda ko ang agarang pagpapakonsulta sa kanyang healthcare provider para sa comprehensive medication review, lalo na sa mga bagong simula o na-adjust na gamot. Malamang na may mga potential interactions ang nangyayari sa pagitan ng kanyang multiple medications, o maaaring kailangan ng dose adjustment o alternative medications. Sa immediate term, tinuruan ko ang pamilya kung paano i-monitor at i-document ang lahat ng side effects—kung kailan nagsisimula, gaano katagal, intensity, at kung anong gamot ang kamakailan lang na ininom bago nangyari ang symptoms. Binuo ko ang isang structured diary format para sa tracking na ito at ipinakita kung paano gamitin. Para sa mga specific side effects, nagbigay ako ng mga practical na interventions habang hinihintay ang konsultasyon: para sa dry mouth, inirerekomenda ko ang regular fluid intake, sugar-free lozenges, at artificial saliva products kung kinakailangan; para sa dizziness, binigyang-diin ko ang kahalagahan ng slow position changes (especially from lying to standing) at pag-iwas sa sudden movements; para sa constipation, nirerekomenda ko ang pag-adjust ng diet (increased fiber at fluids) at gentle physical activity. Ipinaliwanag ko sa pamilya ang risks ng self-medication at over-the-counter products dahil sa potensyal na drug interactions. Binigyang-diin ko na importanteng i-report sa doctor ang lahat ng self-prescribed remedies na ginagamit ni Tatay. Para sa long-term management approach, kinausap ko ang pamilya tungkol sa pagkuha ng 'brown bag medication review' kung saan dadalhin nila ang LAHAT ng gamot (prescribed at over-the-counter) sa doctor para sa komprehensibong evaluation. Iminungkahi ko rin ang paggamit ng medication tracking app na maaaring gumawa ng notifications tungkol sa potential interactions. Nagturo rin ako sa pamilya tungkol sa red flag symptoms na nangangailangan ng immediate medical attention, tulad ng severe confusion, difficulty breathing, rashes, at significant changes sa vital signs. Sa usapin ng communication, binigyang-diin ko sa pamilya ang kahalagahan ng proactive discussions sa healthcare providers tungkol sa side effects, at ang posibilidad ng therapeutic alternatives na maaaring mas mabuti para kay Tatay. Inirekomenda ko ring sikaping i-schedule ang follow-up appointment sa umaga kung kailan mas alert si Tatay para masiguro na makakapag-participate siya nang maayos sa discussion tungkol sa kanyang gamot."
            ],
            [
                "assessment" => "Si Lola ay nagpapakita ng kawalan ng confidence at kaalaman sa sarili niyang medication management, na naobserbahan ko sa huling apat na linggo. Habang binibisita ko siya, nakita kong nahihirapan siyang kilalanin ang kanyang mga gamot ayon sa physical appearance pagkatapos maipalit ang mga ito sa generic versions na may ibang kulay at hugis. Ipinakita niya sa akin ang kanyang pill box kung saan hinalo niya ang lahat ng umaga at gabing gamot sa iisang compartment dahil hindi niya matiyak kung alin ang dapat inumin sa kung anong oras. May ilang gamot din na tinanggal niya sa kanilang original packaging para sa convenience, kaya nawala ang mga label at instructions. Kapag tinatanong kung para saan ang bawat gamot, hindi niya ito maalala, at kapag tinanong kung paano niya iniinom ang kanyang mga gamot, sinasabi niya na pilit niyang naaalala sa pamamagitan ng kulay, pero hindi siya sigurado. Partikular na nakakabahala na minsan ay humihinto siya sa pag-inom ng kanyang anti-hypertensive medications kapag sumasakit ang kanyang ulo, hindi alam na maaaring mas lumala ang kanyang hypertension dahil sa pag-skip ng doses. Napansin ko rin na may malaking misconception si Lola tungkol sa kanyang cholesterol medication—akala niya ay para ito sa kanyang arthritis, kaya minsan ay dinodoble niya ang dose kapag lumalala ang sakit ng kanyang mga kasukasuan. Hindi rin siya sigurado kung alin sa kanyang mga gamot ang dapat inumin nang may laman ang tiyan o kung alin ang dapat sa walang laman. Napag-alaman ko rin na nag-initiate siya ng pagbabawas ng dose sa kanyang blood pressure medication dahil nabasa niya sa internet na masama ito sa kidney, nang hindi kinokonsulta ang kanyang doktor. Dahil sa kawalan ng clarity at confidence, naging inconsistent ang kanyang medication adherence, at minsan ay napapagod siya at biglang titigil sa pag-inom ng ilang gamot nang ilang araw.",
                "evaluation" => "Ang limitadong health literacy at kawalan ng confidence ni Lola sa kanyang medication management ay nangangailangan ng immediate educational intervention at simplification ng kanyang medication routine. Una sa lahat, inirerekomenda ko ang paggawa ng visual medication guide na personalized para sa kanya, na naglalaman ng colored pictures ng bawat gamot, purpose, dosage, timing, at special instructions (tulad ng 'inumin kasama ng pagkain'). Binigyan ko si Lola at ang kanyang pamilya ng kopya ng guide na ito, na naka-laminate para sa durability. Para ma-address ang issue sa pagkilala sa mga generic medications, nagbigay ako ng medication identification chart na nagpapakita ng original at possible generic versions ng bawat gamot niya, at nagbigay ako ng permanenteng marker para malagyan ng label ang mga non-original containers. Sa usapin ng kanyang misconceptions tungkol sa gamot, naglaan ako ng dedicated teaching session para kay Lola at sa available family members para ipaliwanag ang purpose ng bawat medication, proper administration, at ang potential consequences ng hindi pagsunod sa prescribed regimen. Binigyang-diin ko ang kahalagahan ng hindi paghinto o pagbabago ng dose nang walang medical supervision. Para sa organizational issues, ipinakita ko sa kanya ang proper use ng pill organizer, at tinulungan siyang ayusin ang kanyang mga gamot sa weekly pill organizer na may separate compartments para sa umaga, tanghali, at gabi. Nagset up rin ako ng medication calendar na may visual cues para sa bawat time point. Inirerekomenda ko rin na gumawa ng simplified written schedule na naka-post sa refrigerator o sa ibang visible area sa bahay. Para sa ongoing support, inimungkahi ko sa pamilya na i-consider ang telemedicine pharmacy consultation para regular na ma-review ang kanyang medications, at binigyang-diin ko ang kahalagahan ng pagdadala ng updated medication list sa lahat ng doctor appointments. Ipinaliwanag ko rin sa kanila na importanteng i-double-check sa pharmacist ang anumang pagbabago sa appearance ng gamot para matiyak na ito ay tama pa ring medication. Para sa long-term strategy, hinimok ko ang pamilya na gawing habit ang regular na medication review tuwing magkakaroon ng appointment sa healthcare provider, at inimungkahi ang paggamit ng medication reminder apps o text messaging systems para mapabuti ang adherence. Binigyang-diin ko rin ang kahalagahan ng open communication sa kanyang doctors tungkol sa anumang concerns o side effects, imbes na gumawa ng sariling adjustments batay sa impormasyong nakuha sa internet o sa ibang sources."
            ],
            [
                "assessment" => "Si Nanay ay nagpapakita ng kumplikadong pattern ng medication hoarding at excessive stockpiling na naobserbahan ko sa aking mga pagbisita sa nakaraang tatlong buwan. Sa pagsusuri sa kanyang medication cabinet, nakita ko ang alarming na dami ng naka-stockpile na gamot—maraming partially used bottles ng parehong medication pero may iba't ibang expiration dates, pati na rin ang multiple bottles ng mga discontinued medications na hindi na dapat ginagamit. Napansin ko na may mga antibiotic courses na hindi natapos, mga pain medications na partially consumed, at mga over-the-counter remedies na expired na ng 2-3 taon. Kapag tinanong tungkol sa mga gamot na ito, sinabi ni Nanay na iniimbak niya ang mga ito para sa emergency o para hindi na kailangang bumili ulit. Partikular na nakakabahala ang kanyang practice na nagtitigil sa antibiotic course kapag naramdaman niyang bumubuti na ang kanyang pakiramdam, at itinatago ang natirang pills para sa susunod na magkasakit siya. Napag-alaman ko rin na nagpapalit-palit si Nanay ng mga pharmacies para makakuha ng mga bagong prescriptions kahit may stock pa siya sa bahay, dahil natatakot siyang baka magkaroon ng shortage o tumaas ang presyo. Sa aming mga usapan, napansin ko ang kanyang anxiety tungkol sa potensyal na hindi pag-access ng mga gamot dahil sa financial constraints, at minsan ay hinahati niya ang kanyang maintenance medications para 'ma-extend ang supply.' May ilang incident din kung saan ibinibigay niya ang kanyang mga natirang gamot sa mga kamag-anak o kaibigan na may similar symptoms, at nagbibigay siya ng 'medical advice' batay sa kanyang sariling karanasan. Bukod dito, nahihirapan siyang i-track ang mga expiration dates, at nakita ko siyang gumagamit ng mga expired na gamot dahil sa paniniwala na 'effective pa rin naman ang mga ito.'",
                "evaluation" => "Ang medication hoarding at inappropriate storage practices ni Nanay ay nagpapakita ng significant risk para sa medication safety at efficacy concerns. Una sa lahat, kailangan nating ma-address ang root causes ng kanyang behavior—ang anxiety tungkol sa access at affordability ng mga gamot. Nakipag-usap ako sa kanya tungkol sa mga pharmaceutical assistance programs at generic options na available para sa kanyang maintenance medications. Para sa immediate intervention, nagsagawa kami ng comprehensive medication clean-up. Kasama ang kanyang pahintulot, inuri namin ang lahat ng kanyang gamot, at tinanggal ang lahat ng expired, discontinued, at unidentified medications para sa proper disposal. Ipinaliwanag ko ang proper disposal methods para sa mga expired na gamot (iniiwasan ang pagtapon sa sink o toilet) at tinuruan ko siya tungkol sa local drug take-back programs. Binigyang-diin ko ang mga panganib ng paggamit ng expired medications, self-prescription ng antibiotics, at pagbabahagi ng prescription medications sa iba. Gumawa kami ng simplified current medication list na naglalaman lang ng mga active prescriptions, at ginawa ko itong visual chart na naglalarawan ng purpose ng bawat gamot, proper dosing, at kailan dapat kumuha ng refill (based sa actual consumption, hindi sa stockpiling). Para tulungan siyang ma-track ang kanyang medications at maiwasan ang unnecessary refills, binigyan ko siya ng medication inventory log at medication tracker calendar. Kinausap ko rin ang kanyang primary care provider tungkol sa kanyang concerns sa medication access at costs para ma-optimize ang kanyang prescriptions—baka mas cost-effective ang 90-day supply kaysa monthly refills para sa kanyang maintenance medications. Para ma-address ang kanyang underlying anxiety, nag-recommend ako ng ilang communication strategies para sa appointments: ihanda ang listahan ng concerns bago ang appointment, magtanong tungkol sa mga generic alternatives, at ipahayag ang mga alalahanin tungkol sa cost ng mga gamot. Inimungkahi ko rin sa pamilya ang pagmonitor sa kanyang refill patterns, at ang regular na scheduled clean-out ng medicine cabinet (at least twice a year). Bilang long-term strategy, inirerekomenda ko ang pagtatalaga ng isang consistent na pharmacy para sa lahat ng kanyang prescriptions para matulungan siya sa medication management at para ma-monitor ang potential drug interactions. Pinag-usapan din namin ang importance ng transparent communication tungkol sa kanyang financial concerns sa kanyang healthcare team, para makahanap sila ng sustainable at cost-effective medication regimen na hindi maghihikayat sa kanya na magkaroon muli ng hoarding behaviors."
            ],

            // Emotional well-being assessments
            [
                "assessment" => "Si Lolo ay nagpapakita ng matinding lungkot at kalungkutan dahil sa kanyang progresibong pisikal na limitasyon at pagkawala ng independence. Sa aming mga regular na pag-uusap, napansin kong madalas niyang binabanggit na 'wala nang saysay ang buhay' kapag hindi na niya magawa ang mga dating gawain na nagdudulot sa kanya ng kasiyahan. Dati siyang aktibong magsasaka at karpintero na palaging nagtatanim at gumagawa ng mga muwebles para sa kanyang pamilya, pero sa nakaraang anim na buwan, ang lumalang arthritis at macular degeneration ay matindi nang naglilimita sa kanyang mobility at independence. Sinabi niya sa akin na parang nawalan siya ng purpose sa buhay, dahil ang kanyang pagkakakilanlan ay malalim na nakaugat sa pagiging productive at self-reliant. Sa aming huling session, ikinuwento niya na nahihiya siyang laging humihingi ng tulong sa mga simpleng bagay tulad ng pagbasa ng dyaryo, pagtali ng sintas, o paggamit ng telepono. Sinabi rin niya na nakakaramdam siya ng pagiging pabigat sa kanyang pamilya at nabanggit na 'mas mabuti pa sigurong mawala na ako para hindi na ako maging problema.' Napansin ko rin na tumatanggi na siyang lumabas ng bahay o tumanggap ng mga bisita, at unti-unti nang hindi sumasali kapag may mga family gatherings. Ayon sa kanyang asawa, bigla na lang siyang umiiyak nang walang malinaw na dahilan, madalas na nakatitig sa kawalan, at nawalan na ng interes sa pagkain na nagresulta sa pagbaba ng kanyang timbang ng 5 kilos sa nakaraang dalawang buwan. Hindi na rin siya natutuwa sa mga dating interests at hobbies, tulad ng pakikinig sa radyo at pagkukwento sa mga apo. Bukod dito, ang kanyang sleep pattern ay nagbago—madalas siyang gising hanggang madaling araw, at natutulog naman nang labis sa araw.",
                "evaluation" => "Ang sintomas ni Lolo ng persistent sadness, hopelessness, withdrawal, at loss of interest sa dating mga activities ay strongly suggestive ng clinical depression, na nangangailangan ng professional intervention. Inirerekomenda ko ang referral sa geriatric mental health specialist para sa proper assessment at posibleng treatment. Binigyang-diin ko sa pamilya na ang depression ay isang medical condition—hindi lamang normal na bahagi ng pagtanda o resulta ng pisikal na limitasyon—at kailangan itong tratuhin tulad ng iba pang medical conditions. Habang hinihintay ang professional consultation, iminumungkahi ko ang mga sumusunod na psychosocial interventions: Una, mahalagang bigyan si Lolo ng ligtas na espasyo para ma-express ang kanyang mga damdamin nang walang judgment o agad na pagbibigay ng solusyon. Imbes na sabihing 'wag kang malungkot' o 'mag-isip ka ng masaya,' mas mahalaga ang pakikinig at pag-validate sa kanyang feelings. Para ma-address ang kanyang feelings of uselessness, inirerekomenda ko ang paghahanap ng adapted activities na maaari pa rin niyang gawin despite his limitations—tulad ng pagtuturo ng kanyang carpentry skills sa kanyang mga apo, pagbibigay ng gardening advice kahit hindi na siya ang mismo ang nagtatanim, o paggamit ng adaptive tools para makapagpatuloy sa kanyang mga creative pursuits. Binigyang-diin ko sa pamilya na iwasan ang over-assistance at sa halip ay hikayatin ang kanyang independence sa mga bagay na kaya pa niyang gawin, kahit mas matagal o mahirap ang proseso. Para sa social reintegration, iminumungkahi ko ang dahan-dahang re-exposure sa social situations, simula sa small gatherings ng pinakamalapit na pamilya at unti-unting expanding sa wider social circle. Mahalaga ring magkaroon ng routine at structure sa kanyang araw-araw para mabigyan ng purpose at predictability ang kanyang buhay. Para sa sleep issues, nirerekomenda ko ang pagsunod sa regular sleep schedule, pag-iwas sa pagtulog sa araw, at pagsasagawa ng calming bedtime routine. Sa usapin ng physical health, mahalagang i-address ang pain management para sa kanyang arthritis at i-optimize ang sensory aids para sa kanyang vision problems, dahil ang physical discomfort at sensory deprivation ay maaaring magpalala ng depressive symptoms. Pinayuhan ko rin ang pamilya na maging alert sa anumang suicidal statements o behaviors, at kung magkaroon ng ganito, humingi kaagad ng professional help. Inirerekomenda ko rin ang pagkonsulta sa kanyang primary physician para sa medical evaluation dahil may mga medical conditions at medications na maaaring mag-contribute sa depressive symptoms."
            ],
            [
                "assessment" => "Si Nanay ay nagpapakita ng matinding loneliness at social isolation na lumalala mula nang lumipat ang kanyang mga anak sa malalayong lugar dahil sa trabaho, iniwan siyang nag-iisa sa kanyang tahanan. Sa aking mga pagbisita sa nakaraang tatlong buwan, napansin ko ang progressive na pag-withdraw niya mula sa kanyang dating social connections at community involvement. Dati siyang aktibong miyembro ng kanilang senior citizen's association at parokya, pero ngayon ay tumatanggi na siyang dumalo sa mga regular na pagtitipon. Sa aming mga pag-uusap, palagi niyang binabanggit kung gaano kahaba at katahimik ang mga araw, at kung paano niya hinahanap-hanap ang mga boses at ingay ng mga tao sa kanyang paligid. Sa isang partikular na pagbisita, natagpuan ko siyang nakaupo sa dilim, nakatitig sa lumang photo album ng kanyang pamilya, dahil sinabi niya na ito na lang ang paraan para 'makasama' niya ang kanyang mga anak at apo. Ayon sa kanyang kapitbahay, minsan ay napapansin nilang nakikipag-usap si Nanay sa TV o radyo na para bang nakikipag-usap sa totoong tao. Kapag may dumadalaw, napakahirap na paalisin sila ni Nanay, at madalas ay nakikiusap siya na magpahinga muna o magkape kahit tapos na ang bisita o may ibang pupuntahan pa. Siya mismo ay umamin sa akin na natatakot siyang mapag-isa, lalo na sa gabi, at madalas niyang iniiwang bukas ang mga ilaw at TV para gumawa ng 'presence' sa bahay. Kapag tinanong kung kumusta ang communication sa kanyang mga anak, sinabi niyang tumawag sila kada linggo, pero hindi ito sapat para maalis ang kanyang kalungkutan. Binigyang-diin niya na 'iba pa rin ang physical presence' at minsan ay sinasabi niyang pakiramdam niya ay 'invisible' na siya sa mundo, lalo na't unti-unti nang namamatay ang kanyang mga kaibigan at age-mates.",
                "evaluation" => "Ang social isolation at loneliness ni Nanay ay seryosong concerns na nangangailangan ng multi-faceted approach, dahil ang chronic loneliness ay maaaring magdulot ng significant negative effects sa kanyang physical at mental health. Una kong inirerekomenda ang pagbuo ng structured social connection plan para mapunan ang void na naramdaman niya mula sa paglipat ng kanyang pamilya. Nakipag-usap ako sa kanyang mga anak tungkol sa posibilidad ng mas regular na video calls imbes na audio lang, at kung posible, ang pag-establish ng routine virtual family gatherings kung saan maaari siyang makasali sa mga family activities o meals through video. Binigyan ko sila ng specific recommendations para gawing mas meaningful ang mga virtual interactions, tulad ng shared activities (pagsasama sa pagluluto ng parehong recipe, pagsasagawa ng prayer time, o pagbabasa ng kuwento sa mga apo). Para sa local social connections, kinausap ko ang community senior center at parish outreach program para ma-explore ang posibilidad ng home visitation program o 'companion match' kung saan maaaring magkaroon ng regular na bisita si Nanay. Iminungkahi ko rin ang pagkuha ng emotional support pet kung kaya ng sitwasyon ni Nanay, dahil napatunayan na ang mga alagang hayop ay nakakapagbigay ng sense of purpose at companionship. Para mapalawak ang kanyang social circle, sinaliksik ko ang mga local groups at community activities na angkop sa kanyang interests at mobility level—tulad ng gardening clubs, craft circles, o community volunteer opportunities kung saan maaari siyang makaramdam ng purpose at connection. Tinulungan ko rin siyang ma-identify ang mga practical barriers sa social participation, tulad ng transportation issues o mobility limitations, at naghanap kami ng mga solusyon tulad ng community transportation services o accessibility modifications sa bahay. Para sa kanyang day-to-day experience, iminungkahi ko ang pagiging structured sa kanyang daily routine para magkaroon siya ng sense ng purpose kahit mag-isa siya. Kasama rito ang fixed schedule ng mga activities tulad ng morning prayers, light exercises, hobbies, at communication time sa kanyang pamilya. Tinulungan ko rin siyang mag-develop ng cognitive reframing techniques para ma-address ang negative thoughts tulad ng pagiging 'invisible' o 'walang silbi'—binigyang-diin ko ang kanyang continued value, wisdom, at contributions sa kanyang pamilya at komunidad. Sa mas malawak na lebel, kinausap ko ang local barangay health workers tungkol sa pagbuo ng regular home visitation schedule para kay Nanay, at ang posibilidad ng group activities para sa mga seniors sa komunidad na nararanasan din ang social isolation."
            ],
            [
                "assessment" => "Si Lola ay nagpapakita ng matinding takot at pagkabalisa tungkol sa kanyang progresibong pagkakasakit at posibilidad ng end-of-life na mga sitwasyon. Sa aming mga pag-uusap sa nakaraang dalawang buwan, matapos ma-diagnose ng stage 3 cancer, paulit-ulit niyang inilalahad ang kanyang mga takot tungkol sa pain, suffering, at posibleng abandonment habang lumalala ang kanyang kondisyon. Naobserbahan ko ang kanyang increasing na pagkabalisa kapag nakakaranas siya ng kahit kaunting discomfort, dahil iniinterpret niya ito agad bilang sign ng disease progression. Sa aming pinaka-recent na conversation, inilahad ni Lola ang kanyang matinding takot na mamatay nang nag-iisa o mamatay habang nakakaramdam ng matinding sakit na hindi na-manage. Lumalalim ang kanyang anxiety kapag nagkakaroon siya ng medical appointments, at ayon sa kanyang anak, buong gabi siyang hindi makatulog bago ang mga check-up. Bukod dito, nakikita ko ang kanyang pagkabalisa tungkol sa pagiging pabigat sa kanyang pamilya—madalas niyang binabanggit na ayaw niyang maging 'burden' o gumasta ng malaking halaga ng pera para sa kanyang treatments. Tinukoy din niya na nilalabanan niya ang kanyang urge na i-share ang kanyang mga takot sa kanyang mga anak para hindi sila ma-stress. Sa halip, itinatago niya ang kanyang matinding emotional distress at nagpapanggap na 'okay lang' siya kapag kasama ang pamilya. Napansin ko rin na nagkaroon siya ng existential na mga tanong—paulit-ulit niyang tinatanong kung may saysay ba ang kanyang buhay, kung may nagawa ba siyang kabutihan, at kung maaalala pa siya ng mga tao pagkatapos niyang mamatay. Nabanggit din niya ang kanyang agam-agam tungkol sa hindi pa nasasabi o nagagawa sa kanyang buhay at ang takot na hindi na niya magagawa ang mga bagay na pinangarap niya."
                ,
                "evaluation" => "Ang end-of-life anxiety ni Lola ay normal at understandable response sa kanyang diagnosis, pero ang intensity nito ay nangangailangan ng specialized psychosocial at spiritual support para mapabuti ang kanyang quality of life sa gitna ng kanyang illness journey. Una sa lahat, inirerekomenda ko ang referral sa palliative care team na maaaring mag-provide ng holistic approach sa kanyang physical at emotional needs. Habang hinihintay ang formal palliative care involvement, iminumungkahi ko ang mga sumusunod na interventions: Sa usapin ng communication, binigyan ko si Lola ng safe at non-judgmental space para ma-express ang lahat ng kanyang takot at concerns. Ipinaliwanag ko sa kanya na normal ang makaramdam ng takot at anxiety sa kanyang sitwasyon, at hindi niya kailangang magpanggap na malakas o 'okay lang' para sa kapakanan ng kanyang pamilya. Binigyang-diin ko ang kahalagahan ng open communication sa kanyang pamilya—hindi lamang tungkol sa kanyang fears at concerns, kundi pati na rin sa kanyang wishes para sa kanyang care. Tinulungan ko siyang simulan ang advanced care planning process para magkaroon siya ng sense of control at matiyak na ang kanyang preferences ay maririnig at rerespetuhin. Para ma-address ang kanyang concerns tungkol sa pain at suffering, nagbigay ako ng factual at reassuring na information tungkol sa modern pain management at comfort care approaches. Ipinaliwanag ko kung paano gumagana ang palliative care para ma-address ang physical symptoms habang sinusuportahan ang emotional at spiritual needs ng pasyente. Para sa kanyang existential questions, iminungkahi ko ang life review process—isang guided activity kung saan maaalala at mare-reflect niya ang mahahalagang pangyayari, achievements, at relationships sa kanyang buhay. Hinimok ko siyang mag-create ng legacy project (tulad ng memory book, recorded stories, o letters) para sa kanyang pamilya bilang paraan para makaramdam ng continued connection at meaning. Kinausap ko rin ang pamilya tungkol sa kahalagahan ng quality time at meaningful conversations with Lola, para matulungan siyang maramdaman na valued at connected siya. Sa usapin ng spiritual support, nagrekomenda ako ng pastoral care visit kung aligned ito sa kanyang beliefs, at binigyang-diin ang value ng spiritual practices na nagbibigay sa kanya ng comfort. Para sa ongoing support, inirerekomenda ko ang regular na psychological support sessions para kay Lola, at family counseling para matulungan ang buong pamilya na i-navigate ang complex emotions sa panahong ito. Ipinaliwanag ko rin sa pamilya ang kahalagahan ng self-care para sa kanilang sarili habang nag-a-adjust sila sa mga challenges na dala ng illness ni Lola."
            ],
            [
                "assessment" => "Si Tatay ay nagpapakita ng matinding frustration at anger issues dahil sa biglaang pagbabago ng role sa pamilya matapos siyang ma-stroke noong nakaraang taon. Sa aming mga sessions, napansin ko ang kanyang lumalaking pagkairita at verbal outbursts kapag nahihirapan siyang gawin ang mga dating simpleng tasks. Dati siyang primary breadwinner at decision-maker sa pamilya, pero ngayon ay naka-rely sa kanyang asawa at mga anak para sa maraming pang-araw-araw na gawain. Ayon sa kanyang pamilya, naging significantly volatile ang kanyang temperament—madalas siyang sumisigaw sa mga maliliit na bagay, tulad ng hindi tamang pagkakalagay ng kanyang gamot sa mesa o hindi agad na pagtulong sa kanya kapag kailangan niya. Napansin ko rin na tuwing sinusubukan ng pamilya na gawin ang mga bagay para sa kanya, magiging defensive at magagalit siya, at sasabihing 'Hindi ako inutil!' o 'Kaya ko pa rin!' kahit na obvious na nahihirapan siya. May mga pagkakataon din na sinisisi niya ang kanyang sarili at nagiging self-deprecating, sinasabi niyang 'Wala na akong silbi' o 'Pabigat na lang ako.' Kapag kasama ang kanyang mga apo, napansin ng pamilya na nagkakaroon siya ng mood swings—minsan ay masaya at engaged, pero bigla na lang magagalit kapag hindi niya magawa ang mga simpleng laro kasama nila. Bukod dito, tumatanggi siyang sumali sa mga family gatherings at social events dahil ayaw niyang makita siya ng iba sa kanyang 'mahinang' kondisyon. Observed ko rin ang kanyang tendency na mag-withdraw sa mga conversations tungkol sa household decisions, pero pagkatapos ay magagalit kapag hindi siya nasanguni. Tinukoy rin ng kanyang asawa na madalas siyang nagkakaroon ng emotional outbursts kapag nakikita niyang ginagampanan na ng iba ang mga dating roles niya, tulad ng pagkukumpuni ng mga bagay sa bahay o pangangasiwa sa family finances.",
                "evaluation" => "Ang emotional struggles ni Tatay ay nagpapakita ng adjustment disorder with mixed emotional features bilang response sa significant role changes at perceived loss of identity matapos ang stroke. Inirerekomenda ko ang psychological counseling na particular na focused sa grief at adjustment issues, dahil nararanasan niya ang pagluluksa para sa kanyang dating sarili at dating role. Habang hinihintay ang formal counseling, binuo ko ang mga sumusunod na interventions: Para ma-address ang kanyang frustration at anger, tinuruan ko si Tatay at ang kanyang pamilya ng practical anger management techniques tulad ng deep breathing, counting method, at time-out strategy. Binigyang-diin ko ang kahalagahan ng pag-identify sa early warning signs ng rising anger at ng paggamit ng appropriate coping techniques bago lumala ang emosyon. Ipinapaliwanag ko sa kanyang pamilya ang psychological mechanisms sa likod ng kanyang behavior—na ang anger ay madalas na secondary emotion na nakacover sa deeper feelings of grief, fear, at loss. Inimungkahi ko ang pagkakaroon ng family meetings kung saan lahat ay maaaring mag-express ng kanilang feelings sa supportive at structured environment, kasama na ang mga struggles ni Tatay. Para ma-address ang identity at self-worth issues, tinulungan ko siyang i-identify ang mga aspects ng kanyang identity at roles na nananatili pa rin kahit nagbago na ang kanyang physical capabilities. Binigyang-diin ko ang kanyang continued value at wisdom bilang head of the family. Nagbuo rin kami ng modified roles at responsibilities na appropriate sa kanyang current abilities pero nagbibigay pa rin ng sense of purpose at contribution sa household. Halimbawa, maaari siyang maging financial advisor kahit na hindi na siya ang direct na humahawak ng pera, o maaari siyang magturo sa kanyang anak kung paano ayusin ang mga bagay kahit hindi na niya mismo magawa ang physical repair. Tungkol sa independence issues, iminungkahi ko sa pamilya na mag-strike ng balanse sa pagitan ng assistance at enabling independence—bigyan siya ng pagkakataong gawin ang mga bagay na kaya niya, kahit mabagal, pero maging available for support when needed. Para sa ongoing emotional support, nagrekomenda ako ng peer support group para sa stroke survivors kung saan makikilala niya ang ibang lalaki na dumaan sa same journey. May community stroke group na available na maaari niyang salihan para magkaroon siya ng sense of community at shared experience. Pinag-usapan din namin ang kahalagahan ng finding new sources of meaning at pleasure para mapalitan ang mga nawala dahil sa stroke, tulad ng pagkakaroon ng adaptive hobbies o exploring new interests na aligned sa kanyang current abilities."
            ],
            [
                "assessment" => "Si Nanay ay nagpapakita ng tumataas na level ng anxiety at fear tungkol sa kanyang financial security at potential abandonment sa kanyang senior years. Sa nakaraang dalawang buwan ng ating mga regular sessions, napansin kong naging paulit-ulit ang kanyang pag-uusap tungkol sa kanyang mga pag-aalala sa financial situation. Kahit na may sapat na retirement funds at supportive family, nagpapakita siya ng excessive worry tungkol sa posibilidad na maubusan ng pera o maging dependent sa kanyang mga anak. Sa aming pinaka-recent na session, umiyak siya habang nagkukuwento tungkol sa kanyang takot na 'mamatay nang nag-iisa sa nursing home' o 'maging unwanted burden' sa kanyang pamilya kapag tumanda pa siya. Napansin ko rin ang kanyang repeated na pagmention sa mga kuwento ng mga senior na inabandona ng kanilang mga pamilya, at ang kanyang tendency na i-interpret ang normal na busy schedule ng kanyang mga anak bilang signs na unti-unti na siyang pinababayaan. Ayon sa kanyang anak na kasama niya sa bahay, si Nanay ay naging obsessive tungkol sa pag-save ng pera—tumatanggi siyang gumastos kahit sa essential items tulad ng mga bagong gamot o kailangan na niyang mga appliances. Naobservahan ko rin na nagsimula siyang mag-hoard ng mga grocery items at supplies 'para sa mahirap na panahon,' kahit na wala namang indikasyon ng financial hardship. Kapag binabanggit ang long-term care options o future planning, agad siyang nagiging emotional at defensive, sinasabi niyang 'Hindi ko gustong maging pabigat' o 'Mas mabuti pang mamatay ako kaysa maging burden.' 'Bukod dito, sinabi ng kanyang mga anak na nagsimula na siyang magkaroon ng frequent phone calls sa kanila para i-check kung bibisitahin pa rin nila siya, at nag-e-express ng disproportionate na gratitude kapag binibisita nila siya, na para bang hindi niya ine-expect na darating sila.",
                "evaluation" => "Ang persistent fears ni Nanay tungkol sa abandonment at financial insecurity ay manifestations ng anxiety na maaaring may roots sa developmental experiences, current social circumstances, o cultural expectations. Inirerekomenda ko ang psychological assessment para ma-evaluate ang kanyang anxiety level at ma-determine kung may generalized anxiety disorder na nangangailangan ng professional treatment. Habang hinihintay ang formal assessment, iminumungkahi ko ang implementation ng mga psychological at practical support strategies: Una, binigyan ko ng oras ang open discussion tungkol sa kanyang fears at concerns sa non-judgmental environment, at ni-validate ko na normal lang ang magkaroon ng mga ganitong worries sa kanyang edad. Para ma-address ang kanyang financial anxiety, inirekomenda ko ang consultation sa financial advisor na specialized sa retirement planning para magkaroon siya ng clear at realistic na picture ng kanyang financial situation. Sinuportahan din natin ang paggawa ng detailed budget at financial plan na makikita niya regularly para magkaroon siya ng sense of control at predictability. Para sa abandonment fears, nagbigay ako ng specific recommendations sa pamilya para sa consistent communication at visits para magkaroon ng predictability at security si Nanay. Iminungkahi ko ang regular na scheduled family events na maaari niyang asahan, at ang paggamit ng technology tulad ng video calls para mapanatili ang connection kahit malayo ang ibang family members. Binuo ko rin ang isang family care agreement—isang documented plan na nagde-detail kung paano siya susuportahan ng pamilya sa future, kasama ang specific commitments at reassurances na written form para magkaroon siya ng concrete na katibayan na hindi siya pababayaan. Para ma-address ang hoarding behavior, iminungkahi ko ang gentle approach sa pag-set ng boundaries sa kanyang tendency na mag-accumulate ng excessive items, habang sinusuportahan siya na gumawa ng rational decisions tungkol sa kung ano ang talagang kailangan. Sa usapin ng long-term planning, sinubukan kong i-reframe ang conversation mula sa pagiging burden tungo sa 'advanced planning for independence' —ipinaliwanag ko na ang proper planning ay aktwal na nakakapagbigay sa kanya ng more control at autonomy, hindi ito sign ng dependency. Inirerekomenda ko rin ang participation sa senior support groups kung saan puwede siyang makipag-connect sa iba na may similar experiences at concerns, para makita niyang hindi siya nag-iisa sa kanyang mga pag-aalala. Hinimok ko rin ang pamilya na i-validate ang kanyang contributions sa household at family life, at regular na ipaalala sa kanya ang kanyang continued value at importance sa kanilang buhay para ma-counter ang kanyang feelings na pagiging unwanted o burden.'"
            ],
            // Social interaction assessments
            [
                "assessment" => "Si Lola ay nagpapakita ng matinding pagbabago sa kanyang social participation at pagkakaugnay sa komunidad sa nakaraang apat na buwan. Dati-rati, aktibo siyang kalahok sa kanilang barangay senior citizens' association kung saan siya ay treasurer, at regular siyang dumadalo sa weekly na misa at nagbo-volunteer sa parish outreach program. Sa aking mga pagbisita, napansin kong unti-unting huminto ang kanyang partisipasyon sa mga aktibidad na ito. Noong una ay lumiliban lang siya sa mga meetings dahil sa 'hindi magandang pakiramdam,' pero ngayon ay tuluyan na siyang nag-resign sa kanyang posisyon. Ayon sa kanyang anak, halos hindi na rin siya nakikipag-usap sa dating malalapit na kaibigan, at tinatanggihan niya ang kanilang mga imbistasyon para sa mga kape o meryenda. Sa halip, karamihan ng araw ay ginugugol niya sa bahay, nanunood ng TV o tinitingnan ang mga lumang photo albums. Kapag tinatanong kung bakit ayaw na niyang lumabas, madalas niyang sinasabing, 'Ano pa ang silbi ng pagsali-sali sa mga bagay na iyan? Matanda na ako. Hindi ako kasing-importante ng iba.' Kapag bumibisita naman ang mga kamag-anak, nakikipag-usap pa rin siya, pero mapapansin mo ang kawalan ng sigla at enthusiasm sa kanyang mga kwento, at minsan ay nakakaligtaan niyang mag-engage sa mga conversations. Nababahala rin ang pamilya dahil tumanggi siya kamakailan na dumalo sa kaarawan ng kanyang apo, isang event na hindi niya pinalagpas noon. Nabanggit din ng kanyang anak na nag-aalala siya sa mga pagbabago sa physical appearance ni Lola—hindi na niya gaanong inaayusan ang sarili, at minsan, napansin nila na suot pa rin niya ang parehas na damit sa loob ng tatlong araw.",
                "evaluation" => "Ang pagbabago sa social engagement patterns ni Lola ay nagpapakita ng malalim na isyu na maaaring may kaugnayan sa depression, loss of purpose, o posibleng cognitive changes. Inirerekomenda ko ang psychological assessment para ma-evaluate ang kanyang emotional health, lalo na para sa depression, na madalas na under-recognized sa matatanda. Habang hinihintay ang professional assessment, maaari tayong magsimula ng gentle at gradual approach para maibalik ang kanyang social connections. Una sa lahat, mahalagang maintindihan ang mga barriers na pumipigil sa kanya na lumahok sa dating mga aktibidad. Sa halip na i-challenge ang kanyang resistance, iminumungkahi ko ang pag-start ng small, manageable social interactions sa environment kung saan siya komportable—halimbawa, pag-imbita ng isa o dalawang malapit na kaibigan para sa maikling pagbisita sa bahay. Para matulungan siyang ma-reconnect sa community nang hindi overwhelming, maaaring mag-arrange ng transportation at companionship para sa mahahalagang events, at unti-unting i-build up ang duration at frequency ng social exposure. Binigyang-diin ko sa pamilya ang kahalagahan ng pagbibigay ng 'meaningful roles' kay Lola sa pamilya at komunidad, kahit sa simpleng paraan tulad ng paghiling ng kanyang advice o pagpapahintulot sa kanya na magturo ng family recipes o traditions sa mga apo. Para sa kanyang self-care concerns, maaaring magtulong-tulong ang pamilya para i-encourage ang kanyang daily grooming at proper dressing, sa paraang affirming at hindi judgemental—halimbawa, sa pamamagitan ng pagkukwento kung gaano sila na-inspire sa dating pag-aalaga ni Lola sa kanyang appearance, at pagbibigay ng access sa kanyang favorite na beauty products. Inirerekomenda ko rin ang pagsisikap na maibalik ang kanyang sense of purpose at accomplishment—ito ay maaaring simpleang tulad ng pagbibigay sa kanya ng manageable responsibilities sa bahay, o pag-connect sa kanya sa volunteer opportunities na angkop sa kanyang kasalukuyang kakayahan, maging ito man ay over the phone o sa bahay. Mahalaga ring i-assess kung may mga physical barriers (tulad ng health concerns, mobility issues, o transportation difficulties) na nagiging hadlang sa kanyang social participation. Iminumungkahi ko ang pagtatag ng regular na 'social check-ins' kung saan maaaring maintindihan ng pamilya ang mga specific challenges na nakakaapekto sa kanyang social life at ma-address ang mga ito nang naaangkop. Sa susunod nating mga pagbisita, gusto kong obserbahan ang progression ng kanyang social engagement at i-adjust ang ating mga strategies batay sa kanyang responses sa mga interventions na ito."
            ],
            [
                "assessment" => "Si Tatay ay nagpapakita ng lumalaking kahirapan sa pag-adjust sa bagong social dynamics ng kanilang household matapos umuwi ang kanyang anak kasama ang asawa at dalawang teenage children para manirahan sa kanila dahil sa financial constraints. Sa nakaraang dalawang buwan, naobserbahan ko ang paglala ng kanyang frustration at irritability kapag nakikisalamuha sa extended family members sa kanilang maliit na bahay. Ayon sa kanyang asawa, dati-rati ay tahimik at payapa ang bahay nila, at nagkaroon ng routine si Tatay para sa kanyang araw-araw na gawain. Ngayon, nag-rereklamo siya tungkol sa 'malakas na music,' 'walang-pakundangang mga bata,' at ang kawalan ng privacy at personal space. Napansin ko rin na nagbago ang social routines ni Tatay—dati ay mahilig siyang manuod ng paborito niyang shows sa TV sa sala, ngunit ngayon ay madalas na siyang nagkukulong sa kwarto dahil laging ginagamit ng mga teenagers ang TV para sa kanilang video games. Nababawasan din ang kanyang social circle dahil nahihiya na siyang mag-imbita ng kanyang mga kaibigan sa bahay dulot ng 'kaguluhan.' Sa aming conversation, umamin si Tatay na nalilito siya sa mga bagong technology na ginagamit ng kanyang mga apo at nahihirapan siyang makisali sa mga usapan na puno ng mga terminong hindi niya pamilyar. Partikular na nakakabahala ang naobserbahan kong growing tension sa pagitan ni Tatay at ng kanyang anak—parehong dating close sila pero ngayon ay may ilang beses na nag-aaway dahil sa hindi pagkakaintindihan tungkol sa parenting styles at household rules. May mga pagkakataon din na nararamdaman ni Tatay na hindi na siya respected bilang 'man of the house' dahil marami nang desisyon ang ginagawa ng kanyang anak. Nababahala rin ang pamilya dahil napapansin nilang mas madalas nang sumasagot si Tatay nang may galit o iniirapan na lang at lalayo kapag hindi sang-ayon sa napag-uusapan.",
                "evaluation" => "Ang social adjustment difficulties ni Tatay sa bagong household dynamics ay isang complex challenge na nangangailangan ng balanseng approach na kino-consider ang needs ng lahat ng miyembro ng pamilya habang binibigyan ng partikular na pansin ang kanyang psychological well-being bilang elder sa tahanan. Inirerekomenda ko ang pamilya counseling session kung saan maaaring makapag-express ng kanilang feelings at concerns ang bawat miyembro sa isang facilitated at safe na environment. Habang hinihintay ito, binigyan ko sila ng mga praktikal na strategies para ma-improve ang current situation. Una sa lahat, mahalagang magkaroon ng family meeting upang magtatag ng clear na household rules at boundaries na nagre-reflect sa input ng lahat, kasama si Tatay. Sa meeting na ito, maaaring i-address ang mga specific issues tulad ng noise levels, TV scheduling, at designated spaces para sa bawat miyembro ng pamilya. Partikular na binigyang-diin ko ang kahalagahan ng pagkakaroon ng designated 'quiet hours' sa bahay at ang paggawa ng schedule para sa shared spaces at resources. Para ma-preserve ang sense of authority at respect ni Tatay, iminumungkahi ko ang pag-reframe sa sitwasyon—sa halip na makita ang bagong arrangement bilang pagkawala ng kanyang role, i-emphasize ang bagong role niya bilang family elder na may wisdom at guidance na maibibigay sa extended family. Mahalagang i-acknowledge ng anak at mga apo ang value ng kanyang opinyon at actively na humingi ng kanyang advice sa mga appropriate matters. Para ma-address ang technology gap, maaaring magkaroon ng 'technology exchange' sessions kung saan ituturo ng mga teenagers ang basics ng kanilang technology kay Tatay, habang siya naman ay nagbabahagi ng kanyang kaalaman at skills sa kanila. Para matulungan si Tatay na mapanatili ang kanyang social connections, inirerekomenda ko ang paghahanap ng alternative meeting places kung saan maaari niyang makita ang kanyang mga kaibigan, tulad ng nearby park o community center, o ang pagkakaroon ng dedicated 'visitors day' sa bahay kung kailan magiging priority ang kanyang social needs. Kinausap ko rin ang pamilya tungkol sa kahalagahan ng one-on-one time ni Tatay sa bawat miyembro ng pamilya, lalo na sa kanyang anak, upang mapanatili ang intimate connections sa gitna ng busy household. Ipinapayo ko rin kay Tatay ang pagkakaroon ng personal retreat space—kahit isang simpleng corner ng bahay na designated bilang 'kanya' kung saan maaari siyang magkaroon ng peace at quiet time kapag kailangan niya. Sa long term, inirerekomenda ko ang regular na family activities na nagbo-bond sa lahat, habang sinusuportahan din ang occasional outings ni Tatay sa kanyang sariling social circle para mapanatili ang kanyang sense of identity at independence."
            ],
            [
                "assessment" => "Si Nanay ay nakakaranas ng matinding cultural at social alienation bilang bagong-lipat sa Maynila mula sa kanilang probinsya para manirahan kasama ang pamilya ng kanyang anak. Sa aking mga pagbisita sa nakaraang tatlong buwan, paulit-ulit niyang inilalahad ang kanyang pangungulila para sa kanyang dating komunidad, mga kaibigan, at pamilyar na rural lifestyle. Ikinukwento niya sa akin na sa probinsya, araw-araw siyang bumabangon nang maaga para magtanim sa hardin, makipag-kuwentuhan sa mga kapitbahay, at dumalo sa mga local na pagtitipon at simbahan. Ngayon, sa kanilang condo unit sa lungsod, halos wala siyang kilala at nahihirapan siyang mag-navigate sa urban environment dahil sa traffic, pollution, noise, at overcrowding. Napansin ko na madalas siyang nakaupo sa balcony ng unit, nakatitig sa kalye, at bihirang lumabas ng bahay dahil sa takot na maligaw o ma-overwhelm sa ingay at tao. Ayon sa kanyang anak, tumanggi si Nanay na sumama sa isang community gathering ng seniors sa kanilang neighborhood noong nakaraang buwan dahil 'hindi niya maintindihan ang accent ng mga taga-Maynila' at nag-aalala siyang pagtatawanan siya dahil sa kanyang provincial accent at simple clothing. Naobserbahan ko rin na nahihirapan siyang mag-adapt sa bagong routines ng pamilya—sa probinsya, ang family meals ay isang mahalagang social event, pero dito, madalas ay busy ang lahat at kumakain sa magkakaibang oras. Napansin ko na mas naging tahimik si Nanay sa mga family conversations, lalo na kapag may mga topics tungkol sa city life, current trends, o technology. Sa aming latest session, umamin siya na nakakaramdam siya ng pagiging 'outsider' sa sarili niyang pamilya at komunidad, at hinahangad niya ang kanyang dating simple ngunit fulfilling na buhay sa probinsya kung saan siya ay 'may halaga' at 'may silbi.'",
                "evaluation" => "Ang cultural at social dislocation na nararanasan ni Nanay ay isang mahalagang issue na dapat ma-address upang mapabuti ang kanyang sense of belonging at overall well-being sa kanyang bagong environment. Inirerekomenda ko ang multifaceted approach para matulungan siyang mag-establish ng new connections habang pinapanatili ang valuable links sa kanyang cultural identity at rural roots. Una sa lahat, kinausap ko ang kanyang pamilya tungkol sa kahalagahan ng paglikha ng 'cultural bridges' sa kanilang household—mga paraan para ma-integrate ang mga traditions at practices mula sa probinsya sa kanilang urban lifestyle. Ito ay maaaring kasama ang paglaan ng space para sa small container garden sa balcony kung saan maaari niyang ituloy ang kanyang passion para sa pagtatanim, at ang pagkakaroon ng regular na family meals kahit isang beses sa isang araw kung saan maaari niyang i-share ang kanyang traditional recipes at stories. Para matulungan si Nanay na mag-establish ng new social connections, nakipag-coordinate ako sa local senior center para alamin ang available social groups at activities na specifically designed para sa elderly migrants sa lungsod. Partikular na nahanap ko ang isang group ng seniors na nagmula rin sa parehong rehiyon ni Nanay, at inirerekomenda ko na i-ease siya sa pakikisali dito, posibleng sa pamamagitan ng pag-attend muna ng kanyang anak sa unang ilang sessions kasama siya. Upang ma-improve ang kanyang spatial orientation at confidence sa pag-navigate sa urban environment, binuo ko ang isang step-by-step familiarization plan—simula sa paglalakad sa neighborhood kasama ang family member, pagkatapos ay unti-unting pag-introduce sa nearby establishments, public transportation, at community spaces. Binigyan din namin siya ng emergency contact card at basic mobile phone para sa safety at peace of mind. Para ma-address ang kanyang feelings na 'walang silbi,' iminumungkahi ko na i-explore ang mga ways kung paano magagamit ang kanyang rural knowledge at skills sa urban setting—halimbawa, maaari siyang mag-volunteer sa community garden projects, magturo ng traditional cooking sa kanyang mga apo, o mag-participate sa cultural preservation activities. Nag-suggest din ako ng 'reverse mentoring' kung saan ituturo naman ng kanyang mga apo ang basics ng urban navigation at technology, habang siya ay nagbabahagi ng indigenous knowledge at traditions. Hinggil sa communication barriers, nirerekomenda ko ang paggamit ng mga visual aids at shared activities para ma-facilitate ang meaningful exchanges kahit may linguistic differences. Inirerekomenda ko rin ang pagkakaroon ng regular communication channels (tulad ng video calls) sa kanyang mga kaibigan at relatives sa probinsya para mapanatili ang kanyang important social ties. Sa long term, iminumungkahi ko ang exploration ng hybrid lifestyle kung saan maaaring magkaroon si Nanay ng extended visits sa probinsya at pagkatapos ay bumalik sa Maynila, para magkaroon siya ng best of both worlds habang gradually na nagtatransition sa kanyang new life."
            ],
            [
                "assessment" => "Si Lolo ay nagpapakita ng matinding intergenerational communication challenges sa pakikipag-ugnayan sa kanyang mga teenage at young adult na apo. Sa tatlong magkakahiwalay na pagkakataon na naobserbahan ko, napansin ko ang growing frustration sa magkabilang panig kapag nagkakaroon ng interaction. Sa isang particular na insidente noong isang linggo, nagalit si Lolo nang makita niya ang kanyang 17-anyos na apo na nakatutok sa cellphone habang kinakausap niya ito tungkol sa kanyang karanasan noong EDSA Revolution. Sinabihan niya ang bata na 'walang respeto' at 'addicted sa gadgets,' na nagresulta sa defensive response ng teenager at eventual walkout. Sa isa pang pagkakataon, naobserbahan kong nagkaroon ng heated disagreement tungkol sa political views kung saan inakusahan ni Lolo ang kanyang 22-anyos na apo na 'naive' at 'brainwashed ng internet,' habang tinawag naman siya ng apo na 'old-fashioned' at 'hindi updated.' Ayon sa anak ni Lolo, lumalalim ang gap na ito sa nakaraang taon, at nag-aalala siya dahil dati ay close si Lolo sa kanyang mga apo noong mga bata pa sila. Napansin ko na may tendency si Lolo na magsalita sa authoritarian manner, expecting unquestioning respect and obedience base sa traditional Filipino values ng 'paggalang sa nakatatanda,' habang ang mga apo naman ay lumaki sa more egalitarian at questioning environment. Nakikita ko rin na nahihirapan si Lolo na mag-adjust sa modern terminologies, slang, at social media references na regular na ginagamit ng mga bata, at madalas siyang nagiging defensive kapag hindi niya naiintindihan ang mga ito. Sa interview sa mga apo, naintindihan ko na gusto pa rin nila ang kanilang Lolo pero nahihirapan silang mag-relate sa kanya at ma-appreciate ang kanyang 'outdated' perspectives at 'lengthy stories.' Samantalang nalulungkot naman si Lolo na parang hindi na interesado ang mga apo sa kanyang wisdom at life experiences.",
                "evaluation" => "Ang intergenerational communication gap sa pagitan ni Lolo at ng kanyang mga apo ay isang challenging pero addressable issue na nangangailangan ng mutual understanding at adjustment sa magkabilang panig. Inirerekomenda ko ang facilitated intergenerational dialogue sessions na magiging daan para sa structured pero relaxed sharing of perspectives. Habang hinihintay ang formal intervention, nagbigay ako ng practical strategies para sa immediate implementation. Una sa lahat, kinausap ko si Lolo tungkol sa pagkakaiba ng generational communication styles at expectations, at ipinaliwanag ko na ang questioning at direct communication ng mga kabataan ngayon ay hindi necessarily disrespect kundi produkto ng kanilang educational at social environment. Tinulungan ko siyang ma-understand na ang engagement sa dialogue ay hindi pagtatalo o pagwawalang-bahala sa kanyang authority, kundi paraan ng active learning at critical thinking. Sa kabilang banda, nakipag-usap din ako sa mga apo tungkol sa cultural at historical context ng communication style ni Lolo, at ang kahalagahan ng pagpapakita ng respect sa paraang makabuluhan sa kanya. Para ma-bridge ang gap, iminungkahi ko ang creation ng 'common ground activities' na hindi nakadepende sa verbal communication lang—halimbawa, mga hands-on projects tulad ng gardening, cooking, o home repairs kung saan mapapakita ni Lolo ang kanyang expertise habang naturally na nagaganap ang knowledge sharing. Inirekomenda ko rin ang 'story exchange' kung saan magkakaroon ng designated time para sa structured sharing ng experiences—si Lolo ay magbabahagi ng isang relevant life story at ang mga apo ay mag-share din ng kanilang modern experiences na may similarities sa narrative. Para ma-address ang technology divide, iminumungkahi ko ang 'tech buddy system' kung saan tutulungan ng mga apo si Lolo na matuto ng basic technology skills, habang si Lolo naman ay magbabahagi ng traditional skills o knowledge. Inilatag ko rin ang ilang ground rules para sa family discussions: no interrupting, active listening, asking clarifying questions before disagreeing, at ang paggamit ng 'I statements' para maiwasan ang accusatory tone. Binigyang-diin ko kay Lolo at sa mga apo ang value ng 'generational translation'—ang conscious effort na i-explain ang mga concepts at terms sa paraang maiintindihan ng ibang generation. Para sa long-term strategy, inirerekomenda ko ang creation ng 'family legacy project' kung saan ang mga apo ay ma-involve sa documentation ng life stories at wisdom ni Lolo gamit ang modern media formats (tulad ng video interviews o podcast), na magsisilbing bridge between traditional content at contemporary presentation. Ipinaliwanag ko sa pamilya na hindi nangangahulugang kailangang magkasundo sila sa lahat ng bagay, pero ang mutual respect at willingness to understand ay essential para sa meaningful intergenerational relationships."
            ],
            [
                "assessment" => "Si Nanay ay nakakaranas ng social disconnection at loneliness dahil sa kanyang progressive hearing loss na naobserbahan ko sa nakaraang limang buwan. Sa una, napansin kong tumataas ang volume ng TV at radyo, at madalas siyang nagsasabing 'Ano? Pakiulit' sa mga conversation. Ngayon, nakikita ko na ang kanyang hearing impairment ay malaking hadlang na sa kanyang social participation. Sa aking huling pagbisita, nag-attend ako sa isang family gathering kung saan umupo si Nanay sa sulok, malayo sa main conversation area. Nang tinanong ko siya kung bakit, umamin siyang nahihirapan siyang sumunod sa group conversations dahil sa multiple voices at background noise. Ikinuwento niya sa akin na dati ay aktibo siyang kalahok sa kanilang church choir at weekly mahjong sessions kasama ang kanyang mga kaibigan, pero unti-unti na niyang tinalikuran ang mga ito dahil hindi na niya ma-enjoy ang interactions dahil sa hirap sa pakikinig. Ayon sa kanyang anak, napansin nila na nagbago ang personality ni Nanay—ang dating outgoing at sociable na ina ay naging withdrawn at sometimes irritable, lalo na sa mga sitwasyong maraming tao at ingay. May mga pagkakataon din na nagkaka-misunderstanding dahil sa mali niyang pagkakarinig sa sinabi ng ibang tao, na kung minsan ay nagresulta sa hurt feelings o arguments. Partikular na nakakabahala ang kanyang admission na humihinto na siyang sumagot ng telepono dahil nahihirapan siyang maintindihan ang caller, at madalas na hindi na rin siya sumasagot sa mga taong kumakatok sa pinto dahil natatakot siya na baka hindi niya marinig nang maayos ang sinasabi ng bisita. Napag-alaman ko rin na sinubukan niyang gumamit ng hearing aid noon pero hindi siya komportable at nahihirapan siyang i-manage ang device. Sa aking assessment, nakita ko na ang kanyang hearing difficulties ay hindi lamang nakakaapekto sa kanyang social connections kundi pati na rin sa kanyang safety, independence, at overall quality of life.",
                "evaluation" => "Ang hearing loss ni Nanay at ang resulting social disconnection ay nagre-require ng comprehensive na approach na nagtutugma sa audiological, practical, at psychosocial interventions. Una sa lahat, inirerekomenda ko ang pagpapakonsulta sa audiologist para sa updated hearing assessment at para ma-explore ang mga modern hearing aid options na mas comfortable at user-friendly kaysa sa dating nasubukan niya. Iminumungkahi ko rin ang pagkonsulta sa hearing rehabilitation specialist para sa training sa aural rehabilitation techniques at communication strategies. Habang hinihintay ang professional interventions, nagbigay ako ng immediate practical recommendations para mapabuti ang kanyang communication experiences. Para sa one-on-one conversations, tinuruan ko ang pamilya ng proper communication techniques: pagtiyak na nakikita ni Nanay ang kanilang mukha habang nagsasalita, pag-iwas sa covering ng bibig, pagsasalita sa normal na bilis at volume (hindi sumisigaw), paggamit ng clear at concise sentences, at pag-rephrase sa halip na paulit-ulit na sabihin ang hindi naintindihan. Para sa group settings, binigyang-diin ko ang kahalagahan ng strategic seating arrangements—placing Nanay where she can see everyone's faces, reducing background noise, ensuring proper lighting, at ang gentle inclusion sa conversations na hindi nakakapagpa-feel sa kanya na napag-iiwanan. Iminungkahi ko rin ang paggamit ng assistive listening devices para sa specific situations tulad ng TV watching (wireless headphones), phone calls (amplified phones o caption services), at doorbell (flashing light system). Para ma-address ang psychological impact ng hearing loss, kinausap ko si Nanay tungkol sa normalization ng kanyang experiences at ang importance ng open communication tungkol sa kanyang needs rather than withdrawing from social situations. Binigyan ko siya ng strategies para ma-manage ang challenging listening environments, tulad ng taking breaks from noisy settings, advocating for herself by explaining her hearing difficulties to others, at planning social activities in quieter venues. Inirerekomenda ko rin sa pamilya na i-adapt ang kanilang communication style sa home environment—ensuring one person speaks at a time, minimizing background noise during conversations, at creating a supportive atmosphere kung saan komportable si Nanay na hilingin ang clarification kapag may hindi siya naiintindihan. Para sa broader social reconnection, iminungkahi ko na unti-unting bumalik sa dating activities pero sa modified way—halimbawa, one-on-one meetups with friends muna bago sumali ulit sa larger gatherings, o paghanap ng specialized groups para sa individuals with hearing loss kung available sa komunidad. Lastly, ipinaliwanag ko sa pamilya ang connection between untreated hearing loss at cognitive decline, emphasizing na ang proactive management ng hearing difficulties ay hindi lamang para sa social connection kundi para rin sa long-term brain health ni Nanay."
            ],

            // Daily activities assistance assessments
            [
                "assessment" => "Si Lolo ay nagpapakita ng tumataas na level ng pangangailangan sa assistance para sa personal hygiene at bathing, na naobserbahan ko sa nakaraang tatlong buwan. Sa aking unang pagbisita, napansin kong may ilang difficulties siya sa bathing pero mostly independent pa rin. Ngayon, nakita ko ang significant decline sa kanyang ability na magsagawa ng proper hygiene care. Ayon sa kanyang asawa, dating 15-20 minutes lang ang bath time ni Lolo, pero ngayon ay umaabot na ng 45 minutes o higit pa, at madalas ay hindi pa rin siya nakaka-achieve ng adequate cleanliness. Nakita ko na may areas ng kanyang katawan na hindi nababasa ng maayos, partikular na ang kanyang likod at lower extremities. May ilang beses din na napagmasdan kong nagkaroon ng imbalance si Lolo habang nakatayo sa shower area, na nagresulta sa paghawak niya sa towel bar para sa support, na hindi secure at posibleng mapanganib. Kapag nagbibihis naman, nahihirapan siyang i-manipulate ang mga buttons at zipper, at lalo na ang pagsuot ng medyas at sapatos dahil sa limited flexibility sa kanyang likod at difficulty na mag-bend down. Naobserbahan ko rin ang kanyang pagtanggi na tumanggap ng direct assistance mula sa kanyang asawa, sinasabing 'hindi pa ako ganoon katanda para paliguan' at 'kaya ko pa,' kahit na obvious na nahihirapan siya. May mga pagkakataon din na nagagalit siya kapag sinusubukang tulungan siya. Napansin ko rin na bumababa ang frequency ng kanyang pagligo—dati ay araw-araw, pero ngayon ay 1-2 na lang sa isang linggo, at madalas lang kapag pinipilit ng pamilya. Nababahala ang kanyang anak na babae dahil nagsisimula nang magkaroon ng body odor si Lolo at hindi na gaanong nag-aasikaso sa kanyang appearance, na hindi naman dating attitude niya bilang dating military officer na laging neat at well-groomed.",
                "evaluation" => "Ang increasing assistance needs ni Lolo sa personal hygiene at bathing ay nangangailangan ng sensitibong approach na balanse sa pagitan ng ensuring safety at preserving dignity. Una sa lahat, inirerekomenda ko ang home safety modifications sa bathroom: installation ng grab bars sa strategic locations (hindi lang sa tabi ng toilet kundi pati sa shower area), non-slip mats sa shower floor at sa labas ng tub/shower, shower chair o bench para maiwasang matagal na nakatayo, handheld showerhead para sa flexibility, at consideration ng walk-in shower kung feasible. Para sa immediate intervention, binigyan ko ng training ang primary caregiver sa proper assistance techniques na minimize ang risk ng falls habang naprepreserve ang privacy at dignity ni Lolo. Ito ay kasama ang pagkakaroon ng organized bath caddy na may lahat ng kinakailangang items na within reach, proper water temperature testing bago ang bath, at ang option ng seated bathing. Upang ma-address ang resistance ni Lolo sa assistance, iminumungkahi ko ang behavioral approaches tulad ng 'bridging'—unti-unting transition sa acceptance ng tulong sa pamamagitan ng pagbibigay muna ng minimal assistance at gradually increasing ito habang nagbi-build ng comfort level. Binigyang-diin ko ang kahalagahan ng pag-frame sa assistance bilang 'teamwork' imbes na dependence, at ang pagbibigay ng choices para mapanatili ang sense of control ni Lolo (hal., 'Gusto mo bang maligo sa umaga o sa hapon?', 'Anong damit ang gusto mong isuot?'). Para sa clothing difficulties, nirerekomenda ko ang adaptive clothing na may velcro closures imbes na buttons, elastic waistbands, at slip-on shoes, at ang paggamit ng assistive devices tulad ng long-handled sponges, sock aids, at dressing sticks. Binigyan ko rin ng strategies ang pamilya sa pag-handle ng hygiene sa mga araw na tumanggi si Lolo sa full bath, tulad ng sponge baths, focusing on essential areas, at ang paggamit ng no-rinse cleansing products para sa quick cleaning. Sa usapin ng maintaining grooming standards, inirerekomenda ko ang pag-establish ng regular routine at ang pag-connect sa activities na dating meaningful kay Lolo: 'Alam kong mahalagang laging presentable ka noon bilang officer, maaari tayong magtulungan para siguraduhin na maganda pa rin ang iyong appearance.' Para sa long-term management, binigyang-diin ko ang kahalagahan ng regular na reassessment ng kanyang capabilities at ang gradual adjustment ng level ng assistance base sa kanyang needs. Ipinaliwanag ko rin sa pamilya ang psychological aspects ng resistance—na ang pagtanggi sa tulong ay madalas na hindi tungkol sa hygiene mismo kundi sa deeper issues tulad ng loss of independence, dignity concerns, at fear of vulnerability. Sa susunod na pagbisita, plano kong i-reassess ang effectiveness ng mga interventions at tingnan kung may improvements sa bathing safety at hygiene maintenance."
            ],
            [
                "assessment" => "Si Nanay ay nagpapakita ng matinding kahirapan sa meal preparation at nutritional self-care na naging apparent sa nakaraang apat na buwan. Sa aking mga pagbisita, paulit-ulit kong naobserbahan ang decline sa kanyang ability na magluto ng balanced meals para sa sarili. Dati ay kilala siya bilang mahusay na cocinera na regular na nagluluto ng nutritious Filipino dishes mula sa scratch, ngunit ngayon ay nakikita ko na karamihan ng kanyang meals ay processed, instant, o pre-packaged foods na nag-require ng minimal na preparation. Sa pagbubukas ko ng refrigerator (with her permission), napansin kong may limited fresh produce, at maraming expired items. Naka-stock naman ang kanyang pantry ng instant noodles, canned goods, at cookies—mga foods na high sa sodium at sugar pero low sa nutritional value. Naobserbahan ko rin ang physical challenges na nakakaapekto sa kanyang cooking ability: nahihirapan siyang tumayo ng matagal dahil sa knee pain, may tremors sa kanyang mga kamay na nagpapahirap sa paggamit ng knife at manipulate ng small items, at nagkaka-difficulty siya sa pagbuhat ng mabibigat na pots at pans. Sa pakikipag-usap sa kanya, umamin si Nanay na minsan ay nilalaktawan na lang niya ang meals dahil 'masyadong complicated' ang magluto para sa isang tao lang o 'masyadong nakakapagod.' Kapag kumakain naman siya, madalas itong mga maliit na portions na insufficiently nutritious. Nakita ko rin na nagbawas ang kanyang timbang—ayon sa aming records, humigit-kumulang 5 kilograms sa loob ng nakaraang tatlong buwan. Bukod dito, napansin ko na may cognitive factors din na nakakaapekto: nahihirapan siyang sundin ang mga steps ng dating pamilyar na recipes, nakakalimutan niyang may niluluto pala siya (may insidente ng nasunog na kawali noong nakaraang buwan), at nalilito siya sa mga expiration dates. Nag-aalala rin ang kanyang anak na malalayo dahil napansin niyang dumadami ang instances na nagpapa-deliver na lang si Nanay ng pagkain, na nagre-result sa significant expense at usually unhealthy food choices.",
                "evaluation" => "Ang nutritional at meal preparation challenges ni Nanay ay multifaceted at nangangailangan ng comprehensive approach para ma-address ang physical limitations, cognitive factors, at practical barriers sa adequate nutrition. Una sa lahat, inirerekomenda ko ang consultation sa registered dietitian-nutritionist para sa personalized nutritional assessment at meal planning na appropriate sa kanyang health conditions, cultural preferences, at current abilities. Habang hinihintay ang professional guidance, binuo ako ng immediate intervention plan. Para sa kitchen safety at accessibility, iminumungkahi ko ang reorganization ng kitchen para maging mas ergonomic—placing frequently used items within easy reach, using lightweight cookware, considering adaptive equipment tulad ng jar openers, ergonomic utensils, at food processors para sa cutting tasks. Para ma-address ang physical challenges, nagbigay ako ng recommendations para sa energy conservation techniques sa cooking: paggamit ng high stool sa kitchen para makapag-prepare nang nakaupo, breaking down meal preparation into smaller tasks with rest periods in between, at ang concept ng 'cook once, eat twice' (pagluluto ng larger batches para sa multiple meals). Sa cognitive aspects, binigyan ko siya ng simplified cooking methods at visual aids—laminated recipe cards with large print at pictures, color-coded measuring cups, at mga timers with loud alarms. Para maibalik ang independence habang ensuring adequate nutrition, nagbigay ako ng practical solutions: pre-chopped vegetables at fruits, healthier convenience foods na ready-to-eat o minimal preparation, meal subscription services kung affordable, o participation sa community meal programs for seniors kung available sa locality. Nakipag-coordinate din ako sa kanyang social support network para sa meal assistance. Kinausap ko ang kanyang mga kapitbahay at church friends para ma-organize ang rotational schedule kung saan magdadala sila ng home-cooked meals once or twice a week. Inirerekomenda ko rin ang pagbuo ng 'cooking buddy system' kung saan may kasamang family member o friend si Nanay sa pagluluto once a week, na magdo-double din as social activity. Para sa long-term management, binigyang-diin ko ang kahalagahan ng regular monitoring ng weight at nutritional intake. Binigyan ko ang kanyang primary caregiver ng simple food diary template para ma-track ang meals at identify patterns o concerns. Para sa issues sa grocery shopping at food access, inimbestigahan ko ang availability ng grocery delivery services na senior-friendly, at tinuturuan si Nanay kung paano gumamit ng simplified ordering system. Bilang preventive measure, nag-set up kami ng regular kitchen safety checks at system para sa monitoring ng expiration dates ng pagkain. Sinabi ko rin sa pamilya ang importance na i-evaluate ang underlying causes ng reduced appetite at meal skipping, dahil maaaring may medical o psychological factors tulad ng depression, medication side effects, o dental issues na kailangang ma-address."
            ],
            [
                "assessment" => "Si Tatay ay nagpapakita ng significant na kahirapan sa independent medication management na naobserbahan ko sa nakalipas na limang linggo. Sa aking pag-inspect ng kanyang medication system (with his consent), nakita ko ang disorganized na koleksyon ng pill bottles—ang ilan ay half-empty at ang iba ay mukhang hindi nagagalaw, at marami ang walang clear labels dahil naubos na sa paggamit. Sa pakikipag-usap kay Tatay, napansin kong nahihirapan siyang i-identify ang specific medications at ialala kung para saan ang mga ito—kapag tinanong kung anong gamot ang ininom niya sa umaga, hesitant at confused ang kanyang mga sagot. Ayon sa kanyang asawa, nagkaroon ng instances na double-dosing (lalo na sa kanyang blood pressure medications) at may mga araw na nalilimutan niya ang kanyang morning insulin, na nag-result sa poor glycemic control. Sa aking observation, nakita ko ang multiple barriers sa proper medication administration: nahihirapan siyang buksan ang child-proof containers dahil sa kanyang arthritis; nahihirapan siyang basahin ang small print sa labels dahil sa poor vision; at may difficulty siya sa pagputol ng tablets na kailangang hatiin dahil sa hand tremors. May cognitive challenges din—nalilito siya sa complex medication schedules, nakakalimutan niya kung uminom na ba siya o hindi, at nahihirapan siyang i-adjust ang insulin dose base sa kanyang blood sugar readings. Ang kanyang asawa ay sinubukang tulungan siya, pero may sariling health issues din ito na nagpapahirap sa consistent na assistance. Naobserbahan ko din na may limited health literacy si Tatay—hindi niya fully naiintindihan ang purpose ng iba't ibang medications o ang potential consequences ng missed doses o incorrect administration. Partikular na nakakabahala ang kanyang insulin management, dahil nakita kong sometimes inumin niya ito before testing his blood sugar, at hindi niya naiadjust ang dose base sa readings o meals.",
                "evaluation" => "Ang medication management difficulties ni Tatay ay nagpapakita ng high-risk situation dahil ang kanyang health conditions (particularly diabetes at hypertension) ay nangangailangan ng precise at consistent medication administration. Inirerekomenda ko ang immediate implementation ng comprehensive medication management system at education program. Una sa lahat, nakipag-coordinate ako sa kanyang primary healthcare provider para sa medication reconciliation at simplification ng regimen kung posible (hal., reducing frequency of doses, combining medications, o consideration ng alternative formulations tulad ng once-daily options). Para sa organizational challenges, nagbigay ako ng pillbox organizer na may clearly marked compartments para sa bawat araw at time of day, at tinuruan si Tatay at ang kanyang asawa kung paano ito i-set up weekly. Bilang karagdagan, gumawa ako ng large-print medication chart na naka-post sa kanilang refrigerator, na may simplified information tungkol sa bawat gamot—pangalan, purpose, specific time to take, special instructions (with/without food), at colored pictures ng bawat pill para sa visual identification. Para sa physical barriers, nagrekomenda ako ng practical solutions: request for non-childproof caps sa pharmacy, use of pill crusher o splitter devices para sa mga tablets na kailangang hatiin, at ang paggamit ng magnifier para sa small print. Para sa insulin management, binuo ko ang simplified protocol na may clearly defined steps at gumawa ng large-print log book para sa daily blood sugar readings at insulin doses. Tinuruan ko rin siya ng proper technique sa pagturok ng kanyang insulin using methods appropriate for someone with decreased dexterity. Para sa cognitive challenges, binigyang-diin ko ang kahalagahan ng establishing a consistent routine at binigyan sila ng medication reminder tools: alarmed watch o timer, checklists para sa daily medications, at visual cues (tulad ng placing morning pills next to coffee cup). Sa usapin ng supervision at support, tinalakay namin ang mga available options: daily check-ins mula sa family members (in person o via phone), medication packing services na available sa ilang pharmacies, o consideration ng home health aide visits kung kailangan. Para sa mas complex medications tulad ng insulin, inirekomenda ko ang direct supervision ng trained individual kung hindi consistent si Tatay sa proper administration. Para sa health literacy improvement, nagsagawa ako ng education sessions para sa buong pamilya tungkol sa purpose ng bawat medication, signs of adverse effects to monitor, at kung kailan kailangang i-contact ang healthcare provider. Binigyang-diin ko rin kay Tatay at sa kanyang asawa ang kahalagahan ng pagdadala ng updated medication list sa lahat ng medical appointments at sa emergency situations. Para sa ongoing monitoring at follow-up, inirerekomenda ko ang weekly medication review at refill check para matiyak na hindi nauubusan ng supplies, at ang documentation ng any missed doses o adverse effects para ma-report sa healthcare provider sa kanyang next appointment."
            ],
            [
                "assessment" => "Si Lola ay nagpapakita ng progresibong pagkawala ng kakayahan para sa independent household management at home maintenance na unti-unting lumalala sa nakaraang anim na buwan. Sa aking mga regular na pagbisita, nakita ko ang gradual decline sa cleanliness at organization ng kanyang bahay—dating immaculate ang kanyang tahanan, pero ngayon ay may visible clutter sa surfaces, accumulated mail at unpaid bills sa mesa, at general disarray sa living spaces. Napansin ko rin ang neglected na maintenance issues: may tumutulo na gripo sa banyo, burned-out light bulbs na hindi napapalitan, at accumulated dust sa mga areas na mahirap abutin. Sa kusina, naobserbahan ko ang expired food items sa refrigerator, dirty dishes na nakakalat, at garbage na hindi regular na natatanggal. Ayon sa kapitbahay ni Lola, dating araw-araw siyang nakikitang nagwawalis ng kanyang balkonahe at nagdidilig ng kanyang mga halaman, pero ngayon ay abandoned na ang dating well-maintained garden. Kapag tinatanong tungkol sa household tasks, sinasabi ni Lola na 'gagawin ko bukas' o 'pagod lang ako ngayon,' pero hindi na nagagawa ang mga tasks. Sa ating mga conversation, umamin siya na nahihirapan siyang gawin ang high-energy requiring tasks tulad ng paglilinis ng banyo o pagbubuhat ng mabigat na laundry basket, at nahihirapan siyang mag-multitask at mag-organize ng mga multi-step household processes gaya ng dati. Partikular na concerning na nakita kong may mga potential safety hazards sa bahay: extension cords na nakasabit sa daanan, throw rugs na nagka-curl up sa edges, at precariously stacked na mga box at newspapers. Sa pag-assess ng kanyang physical capabilities, nakita ko ang reduced strength at endurance, joint stiffness lalo na sa morning, at reported na low back pain na lumulala kapag nagbe-bend at nag-lift. May cognitive factors din na nakakaapekto sa kanyang home management—nahihirapan siyang alalahanin kung kailan huling nalinis ang mga areas at kung anong household tasks ang need gawin.",
                "evaluation" => "Ang difficulties ni Lola sa home management ay multifactorial at nangangailangan ng tiered approach para ma-address ang safety concerns, physical limitations, at long-term household maintenance. Inirerekomenda ko ang comprehensive home safety assessment at modifications para ma-eliminate ang immediate hazards: securing o removing loose rugs, organizing extension cords, clearing pathways, at installing adequate lighting in all areas especially hallways at stairs. Para sa daily household tasks, binuo ko ang simplified at prioritized task management system—isang weekly schedule na naka-break down sa manageable daily tasks, focusing muna sa essential activities para sa health at safety. Habang isinasagawa ito, nagbigay ako ng energy conservation techniques at adaptive methods: paggamit ng long-handled dusters at cleaning tools, seated tasks whenever possible, at ang strategic na pag-distribute ng activities throughout the day with rest periods. Para sa physical challenges, inirerekomenda ko ang consultation sa physical at occupational therapist para sa targeted exercises para mapalakas ang functional strength at endurance, at para sa additional adaptive techniques. Para sa high-energy o physically demanding tasks (tulad ng window cleaning, heavy laundry, at yard maintenance), importante ang pag-identify ng sustainable support systems. Nakipag-coordinate ako sa family members para ma-assess kung sino ang maaaring regularly tumulong for specific tasks. Iminungkahi ko rin ang exploration ng community resources—senior service agencies na nagpo-provide ng housekeeping assistance, 'adopt-a-grandparent' programs sa local schools o churches, o consideration ng paid services sa affordable rates (weekly housekeeper, gardener, o handyman for repairs). Para sa home organization at clutter management, gumawa kami ng step-by-step system: designated spaces para sa important documents, simplified filing system para sa bills at papers, at regular decluttering sessions with assistance. Binigyan ko rin si Lola ng simplified systems para sa routine tasks: labeled containers para sa frequently used items, checklist systems para sa grocery needs at household supplies, at centralized calendar para sa bill payment deadlines at home maintenance schedules. Para ma-maximize ang kanyang cognitive function para sa home management, inirerekomenda ko ang consistent routines at visual reminders—written checklists sa common areas, color-coded calendars, at timer systems para sa tasks requiring monitoring (like laundry). Tinalakay ko rin sa pamilya ni Lola ang need to balance independence with appropriate assistance—ang kahalagahan ng supporting her in maintaining control over her environment habang ensuring safety at adequate home maintenance. Para sa long-term planning, kinausap ko ang pamilya tungkol sa regular reassessment ng living situation ni Lola at ang potential future needs para sa additional support, home modifications, o consideration ng alternative living arrangements kung lalo pang lalala ang kanyang difficulties."
            ],
            [
                "assessment" => "Si Lolo ay dumaranas ng matinding kahirapan sa mobility-related self-care activities, partikular na sa kanyang pag-navigate sa pag-shower, pag-toilet, at pagbibihis. Sa aking direct observation at functional assessment, nakita kong nagiging increasingly challenging sa kanya ang paggamit ng toilet nang nag-iisa. Ang proseso ng pagbaba sa toilet seat at pagtayo muli ay nangangailangan na ng significant upper body strength dahil sa weakness ng kanyang lower extremities. May dalawang pagkakataon sa nakaraang linggo na muntik na siyang madapa habang sumusubok na tumayo mula sa toilet bowl. Para sa bathing, naobserbahan ko na kinakailangan niyang humawak nang mahigpit sa towel rack (na hindi designed para sa weight-bearing) habang pumapasok sa shower area dahil sa fear of slipping at poor balance. Nahihirapan rin siyang i-maintain ang standing position habang naliligo, at ayon sa kanyang asawa, madalas siyang napapaupo sa edge ng tub dahil sa fatigue, na naglalagay sa kanya sa risk para sa falls. Sa usapin naman ng dressing, napansin kong prolonged at frustrating process para sa kanya ang pagsuot ng kanyang mga damit, lalo na sa lower body. Nahihirapan siyang magsuot ng pantalon habang nakatayo dahil sa poor balance at leg weakness, at nahihirapan din siyang magsuot habang nakaupo dahil sa limited flexibility at range of motion. Kinakailangan niyang humiga sa kama para maisuot ang kanyang pantalon, at madalas ay hingal na hingal siya pagkatapos ng activity na ito. Ang pagbibihis ng upper body ay less challenging pero nangangailangan pa rin ng assistance sa buttons at closures dahil sa kanyang arthritic fingers. Ayon sa kanyang anak, madalas nang mairita si Lolo kapag tinutulungan siya sa bathing at dressing, at sinasabi niyang 'Hindi ako baby!' o 'Kaya ko pa ito!' kahit obvious na nahihirapan siya. Naobserbahan ko rin na dahil sa mga challenges na ito, nagbabawas siya ng frequency ng kanyang bathing at nagse-settle na sa pagsuot ng paulit-ulit na mga damit para maiwasan ang hassle ng changing clothes.",
                "evaluation" => "Ang self-care at mobility difficulties ni Lolo ay significant concerns na nangangailangan ng comprehensive at dignified approach para ma-maximize ang kanyang independence habang ensuring safety. Inirerekomenda ko ang immediate implementation ng targeted home modifications at assistive devices. Para sa toilet safety at independence, nirerekomenda ko ang installation ng properly anchored grab bars sa magkabilang sides ng toilet (hindi towel racks dahil hindi designed para sa weight support), raised toilet seat na may armrests para mabawasan ang distance ng sit-to-stand transfers, at consideration ng bedside commode sa gabi kung challenging ang pagpunta sa banyo. Para sa shower safety, crucial ang installation ng grab bars sa strategic locations sa shower area, non-slip mats sa shower floor at bathroom floor, shower chair o bench para sa seated bathing, at handheld shower head na may long hose para sa flexible water direction habang nakaupo. Kinausap ko ang occupational therapist para sa assessment at training ni Lolo sa proper transfer techniques sa bathroom—particular sa safe methods ng pagpasok at paglabas sa shower at ang proper body mechanics para sa sit-to-stand transfers mula sa toilet. Para sa dressing difficulties, binigyan ko siya ng specific adaptive techniques at tools: dressing stick para tulungan siyang kunin at isuot ang lower garments nang hindi nagbe-bend excessively, sock aid para makapagsuot ng medyas nang hindi nagbe-bend, long-handled shoe horn para sa independent shoe wearing, at techniques para sa dressing habang nakaupo safely. Iniimungkahi ko rin ang simplification ng clothing choices—transitionng to easier clothing options tulad ng pants na may elastic waistbands imbes na may buttons at zipper, pullover shirts imbes na button-down, at slip-on shoes na may Velcro closures. Para sa issue ng emotional resistance at dignity preservation, binigyang-diin ko sa kanyang family caregivers ang importance ng promoting independence sa mobility tasks kahit mas mabagal—allowing him extra time rather than rushing to help, offering assistance only when clearly needed, at ang practice ng 'stand-by assistance' kung saan nasa malapit lang sila pero hinahayaang gawin ni Lolo ang tasks nang mag-isa hangga't maaari. Inirerekomenda ko rin ang consultation sa physical therapist para sa targeted strengthening exercises particularly for his lower body, core, at upper extremities para mapabuti ang functional mobility for transfers at self-care. Binigyang-diin ko sa pamilya ang kahalagahan ng finding balance between respecting his desire for independence at ensuring safety, at binigyan sila ng specific communication strategies para i-frame ang assistance bilang enabling rather than diminishing his capabilities. Para sa ongoing monitoring, iminumungkahi ko ang regular assessment ng kanyang functional status, ang need for additional adaptive equipment, at ang pag-adjust ng strategies based sa progression ng kanyang condition. Long-term, binigyan ko ang pamilya ng information tungkol sa mga resources tulad ng adaptive equipment providers, specialists in home modifications, at support services kung sakaling mas lumala ang needs ni Lolo for assistance sa self-care activities."
            ],
            // Hygiene and personal care assessments
            [
                "assessment" => "Si Lolo ay nagpapakita ng significant na deterioration sa kanyang oral hygiene na naobserbahan ko sa nakaraang dalawang buwan. Sa aking pagbisita, nakita ko na ang kanyang mga ngipin at artipisyal na pustiso (partial dentures) ay may accumulation ng plaque at food debris. Kapag kinakausap, napansin kong may malaking pagbabago sa kanyang hininga (halitosis) na hindi dati niya problema. Ayon sa kanyang anak, bumaba ang frequency ng pagtoothbrush ni Lolo—dating dalawang beses sa isang araw, ngunit ngayon ay minsan na lamang at may mga araw pa na nalilimutan niya ito. Sa pakikipag-usap kay Lolo, inamin niya na nahihirapan siyang hawakan nang maayos ang toothbrush dahil sa kanyang arthritis sa mga kamay, at nagiging painful ito para sa kanya. Bukod dito, nahihirapan siyang magmaintain ng proper oral care routine dahil sa increasing forgetfulness. Napansin ko rin na kapag nagtatanggal siya ng kanyang partial dentures, obvious na may inflammation sa kanyang gums at may ilang sores na nagde-develop. Kinukuwento niya na minsan ay sumasakit ang kanyang bibig kapag kumakain ng matigas o maasim na pagkain. May mga pagkakataon din na ayaw niyang tanggalin ang kanyang dentures sa gabi dahil nakakalimutan niya ang proper steps para sa cleaning at storage nito. Sinabi rin ng kanyang anak na huminto na si Lolo sa pagpunta sa regular na dental check-ups sa nakaraang taon dahil nahihirapan siyang magbiyahe at natatakot siya sa potential na sakit o discomfort na maaaring idulot ng dental procedures.",
                "evaluation" => "Ang poor oral hygiene ni Lolo ay maaaring magresulta sa malubhang mga komplikasyon kabilang ang gum disease, dental infections, nutritional deficiencies, at systemic health issues. Inirerekomenda ko ang immediate implementation ng comprehensive oral care plan. Una sa lahat, kinakailangan ang dental assessment mula sa isang dentist na may specialization sa geriatric care, at ideal kung mayroong home dental service na available sa inyong lugar. Para sa immediate management, binigyan ko ang pamilya ng specific oral hygiene strategies na adapted para sa kanyang arthritis: paggamit ng electric toothbrush na may wider, cushioned grip; ang pagsuot ng rubber grip extenders sa kanyang kasalukuyang toothbrush; at ang pagkakaroon ng toothbrush holder na naka-suction sa sink para mabawasan ang pangangailangan na hawakan ang toothbrush nang mahigpit. Nagbigay din ako ng demonstrations kung paano gamitin ang floss holders at interdental brushes na mas madaling hawakan kaysa sa traditional floss. Para sa denture care, binuo ko ang simplified routine kasama ng step-by-step instructions na naka-post sa banyo: paglalagay ng towel sa sink para maiwasan ang breakage kung sakaling mahulog ang denture, paggamit ng denture brush na may suction cup holder, at paglalagay ng dentures sa labeled container na madaling makita at buksan. Para sa mga sores at inflammation, inirekomenda ko ang saline rinses at gentle cleaning ng gums gamit ang soft gauze. Binigyan ko rin sila ng guide sa pagpili ng oral care products para sa sensitive mouths – alcohol-free mouthwash, toothpaste para sa sensitive teeth, at xylitol-containing products para tulungan ang dry mouth kung mayroon. Sa aspeto ng cognitive support, nagdisenyo ako ng visual reminder system sa kanyang banyo: color-coded na chart na nagpapakita ng daily oral hygiene routine na may morning at evening sections, at step-by-step sa proper denture care. Binigyan ko rin ang primary caregiver ng training sa gentle oral care assistance, binigyang-diin ang kahalagahan ng preserving dignity at promoting independence hangga't maaari. Inirerekomenda ko rin ang pagkakaroon ng set routine time para sa oral care, ideally after breakfast at before bedtime, at ang paggamit ng gentle verbal prompts. Para ma-address ang kanyang concern tungkol sa dental visits, kinausap ko ang pamilya tungkol sa posibilidad ng sedation dentistry options at nagmungkahi ng transportation services na specialized sa pagdadala ng seniors sa medical appointments. Nilinaw ko rin sa pamilya ang koneksyon sa pagitan ng oral health at general health, at ang kahalagahan ng regular monitoring para sa signs ng dental pain o infection tulad ng facial swelling, increased difficulty sa pagkain, o behavioral changes na maaaring magpapakilala ng dental discomfort."
            ],
            [
                "assessment" => "Si Nanay ay nagpapakita ng significant skin integrity issues at developing pressure points na naobserbahan ko sa nakaraang apat na linggo. Sa aking huling dalawang pagbisita, nakita kong may persistent redness sa kanyang lower back, sacrum, at heels na hindi nare-resolve kahit pagkatapos ng 15-20 minuto pagkatapos magbago ng position. Partikular na nakakabahala ang 4cm x 3cm area sa kanyang sacrum na nagpapakita na ng slight breakdown ng skin. Sa kanyang kanang heel, may dry, flaky skin at minimal maceration na palaging present. Ayon sa kanyang tagapag-alaga, lumala ang condition ni Nanay matapos niyang maospital ng 10 araw dahil sa pneumonia noong nakaraang buwan, kung kailan nagkaroon ng extended periods of immobility. Mula nang makauwi, napansin ng pamilya na nagbago ang kanyang level ng activity – tumatanggi na siyang mag-participate sa dating routine na short walks sa loob ng bahay, at minsan ay umuupo o humihiga siya nang 4-5 oras nang hindi nagbabago ng position. Sa pagmamasid ko sa last visit, napansin ko na kapag nakaratay si Nanay, nagkakaroon ng shearing forces dahil sa paulit-ulit niyang pagkilos dahil sa discomfort, pero hindi naman fully nagbabago ng position. Napansin ko rin na medyo dumidiin ang kanyang pressure points sa kama dahil sa bone prominences, indikasyon ng kanyang nagbabagong body mass at reduced muscle tone. Nag-aalaga rin si Nanay ng urinary incontinence na minsang nagko-contribute sa skin moisture, bagaman palaging sinisigurado ng pamilya na nalilinis siya agad. May distress din si Nanay sa kanyang dry skin, lalo na sa arms at legs, na madalas niyang kinakamot at nakakaresulta sa scratch marks at patuloy na irritation.",
                "evaluation" => "Ang skin integrity issues at developing pressure points ni Nanay ay nangangailangan ng immediate at comprehensive intervention para maiwasan ang progression sa full pressure ulcers. Inirerekomenda ko ang pagsisimula ng multifaceted pressure ulcer prevention program kaagad. Una sa lahat, iminumungkahi ko ang implementation ng systematic repositioning schedule: every 2 hours kapag nasa kama at every 1 hour kapag nakaupo sa upuan, na may proper documentation ng mga position changes sa bedside turning chart. Nagbigay ako ng demonstration sa proper repositioning techniques gamit ang draw sheets para mabawasan ang friction at shear, at ang proper na paggamit ng pillows para i-offload ang pressure sa bony prominences—lalo na sa heels, sacrum, at greater trochanters. Para sa pressure redistribution, inirerekomenda ko ang paggamit ng pressure-redistributing mattress overlay o specialty mattress, at ang pagkakaroon ng pressure-reducing cushion para sa kanyang wheelchair o upuan. Tungkol sa skin care routine, binigyang-diin ko ang kahalagahan ng daily skin inspection, lalo na sa high-risk areas, at ang paggamit ng pH-balanced cleanser imbes na regular soap. Tinuruan ko ang pamilya ng proper cleaning technique para sa incontinence episodes—gentle cleansing with minimal friction at ang paggamit ng moisture barrier cream para sa perineal area. Para sa dry skin sa kanyang extremities, nirerekomenda ko ang hypoallergenic moisturizer na hindi naglalaman ng alcohol o fragrances, na dapat i-apply pagkatapos ng bath habang slightly damp pa ang skin. Para sa nutritional component ng skin health, nakipag-usap ako sa pamilya tungkol sa kahalagahan ng adequate protein intake (1.2-1.5g/kg ng body weight daily), proper hydration (at least 1.5L ng fluids daily kung walang contraindication), at supplementation ng Vitamin C at zinc kung may deficiencies. Tungkol sa existing areas na may early breakdown, binigyan ko sila ng specific wound care instructions: paano gamitin ang transparent film dressing sa sacral area, kung paano i-assess para sa signs ng infection, at kung kailan kailangan tumawag sa healthcare professional kung lumala ang condition. Para sa mobility concerns, binuo ko ang gentle remotivation program para hikayatin si Nanay na gradually bumalik sa kanyang short activity periods—nagsimula sa supported sitting at limb exercises sa kama, progressing sa short assisted standing at eventually light ambulation sa bahay. Ipinaliwanag ko rin sa pamilya ang early warning signs ng worsening skin breakdown at ang kahalagahan ng prompt reporting ng any changes sa healthcare team. Pinaalala ko rin sa kanila na i-document at i-photograph ang skin condition regularly para ma-track ang progress o deterioration."
            ],
            [
                "assessment" => "Si Lola ay nagpapakita ng matinding kahirapan sa pangangalaga ng kanyang hair at nails na lumalala sa nakaraang tatlong buwan. Sa aking mga pagbisita, naobserbahan ko ang significant na pagbabago sa kanyang dating well-groomed appearance. Ang kanyang buhok, na dating laging naka-neat na bun, ay ngayon ay madalas na tangled at matted, lalo na sa back of her head. Mayroong visible scalp flaking at dryness na hindi natutugunan ng regular shampooing. Sinabi ng kanyang anak na nalilimitahan na ang kakayahan ni Lola na i-raise ang kanyang arms dahil sa kanyang frozen shoulder at arthritis, kaya nahihirapan na siyang mag-shampoo at mag-brush ng kanyang sariling buhok. Kapag tinutulungan naman siya ng anak, nagkakaroon ng conflict dahil ayaw ni Lola na mag-rely sa iba para sa kanyang personal care. Sa kanyang mga kuko naman, napansin ko ang overgrown at thick toenails na may signs ng fungal infection (yellow discoloration at thickening). May mga areas ng ingrown toenails sa kanyang right at left big toes na nagdudulot ng pain at discomfort kapag naglalakad. Ang kanyang fingernails ay uneven din at may multiple broken edges na minsan ay nakakasagabit sa kanyang damit. Ayon sa kanyang apo, tumigil na si Lola sa pagpapapedicure at pagpapamanicure sa parlor dahil nahihirapan na siyang pumunta roon, at nahihirapan din siyang gamitin ang nail clippers dahil sa kanyang tremors at poor vision. Kahit ang simple task ng paghuhugas ng kanyang buhok ay naging stressful experience para kay Lola at sa kanyang caregiver dahil sa physical limitations at emotional response ni Lola sa pagiging dependent. Napag-alaman ko rin na ayaw na niyang magpagupit dahil nahihirapan siyang manatiling nakaupo sa extended periods at nahihirapan siyang i-communicate ang gusto niyang style.",
                "evaluation" => "Ang hair at nail care challenges ni Lola ay hindi lamang cosmetic concerns kundi may significant impact din sa kanyang comfort, hygiene, at psychological well-being. Inirerekomenda ko ang comprehensive care approach na naka-balanse between promoting independence at providing necessary assistance. Una sa lahat, para sa hair care, iminumungkahi ko ang reorganization ng hair care routine: paggamit ng dry shampoo sa pagitan ng wet washing para bawasan ang frequency ng full shampooing; pag-ischedule ng hair washing tuwing may maximum energy si Lola (usually mornings); at ang paggamit ng handheld shower spray with chair para maging mas komportable ang experience. Binigyan ko ang family ng specific techniques para sa gentle detangling using wide-tooth combs at leave-in conditioner, at ang paggamit ng satin pillowcases para mabawasan ang matting habang natutulog. Nagsagawa rin ako ng research para sa local mobile salon services na pwedeng pumunta sa bahay para sa regular haircuts at styling, ensuring na may experience sila sa elderly clients. Para sa scalp issues, nirerekomenda ko ang appropriate medicated shampoo para sa kanyang specific scalp condition at ang regular na massage ng scalp gamit ang soft brush para ma-stimulate ang circulation. Tungkol naman sa nail care, inirerekomenda ko ang immediate consultation sa podiatrist o foot care specialist para sa professional treatment ng kanyang ingrown at fungal toenails, kasama ang pagtuturo ng proper ongoing care sa pamilya. Para sa regular maintenance, gumawa ako ng nail care kit na may ergonomic tools—long-handled toe nail clippers, electric nail file para sa thick nails, at cushioned nail scissors na mas madaling hawakan at gamitin ng kanyang caregivers. Tinuruan ko rin ang pamilya ng proper technique sa safe nail trimming at filing, at ipinaliwanag ang kahalagahan ng regular inspection para sa signs ng infection o injury. Sa psychological aspect, binigyang-diin ko sa family ang kahalagahan ng dignified approach sa pagtulong, emphasizing choice at control—pagbibigay kay Lola ng options sa styling at timing ng care, at ang pagbibigay ng privacy hangga't maaari. Upang ma-encourage ang kanyang participation, iminumungkahi ko ang paggamit ng specialized adaptive tools tulad ng long-handled hairbrushes at combs with extended handles para makaya pa rin niyang mag-participate sa kanyang hair care. Nagbigay din ako ng ideas para sa hair styles na low-maintenance pero dignified, tulad ng shorter cuts na hindi kailangang i-style araw-araw pero presentable pa rin. Nilinaw ko sa family na ang regular grooming rituals ay hindi lamang hygiene concern kundi mahalagang aspect din ng self-esteem at identity retention kay Lola, at ang importance ng balancing assistance with preservation ng kanyang dignity at independence."
            ],
            [
                "assessment" => "Si Tatay ay nakakaranas ng lumalaking kahirapan sa pag-manage ng kanyang urinary incontinence na nagsimula halos anim na buwan na ang nakakalipas at lumalala sa mga nakaraang linggo. Sa aking assessment visits, nakita ko ang multiple signs ng urinary leakage sa kanyang damit, bed linens, at upholstered furniture sa living room. Ayon sa kanyang anak, ang mga 'accidents' ay naging mas frequent at unpredictable—dating nagkakaroon lang ng occasional night-time leakage, pero ngayon ay nangyayari na rin sa araw, minsan na walang apparent warning o urge. Napansin ko na nagbuo na si Tatay ng coping mechanisms tulad ng pagkukulong sa sarili sa bahay, pag-iwas sa mga social gatherings, at pagsusuot ng makakapal at maitim na pantalon para itago ang possible leakage. May mga instances din na nagre-refuse siyang uminom ng tubig o fluid, lalo na bago lumabas ng bahay o matulog sa gabi, sa paraang nakaka-increase ng risk ng dehydration. Nag-obserbahan akong nagkaroon din ng skin irritation at redness sa kanyang perineal area dahil sa moisture at friction. Bukod dito, nakita ko ang psychological impact ng condition kay Tatay—napansin kong nagkakaroon siya ng embarrassment at frustration kapag napag-uusapan ang issue, at minsan ay defensive o galit kapag nagiging topic ito. Napag-alaman ko rin na tumanggi siyang sumailalim sa medical evaluation para rito, sinasabing ito ay 'normal na parte ng pagtanda' at walang magagawa tungkol dito. Kapag tinanong tungkol sa medications, nabanggit ng pamilya na umiinom si Tatay ng diuretic para sa kanyang hypertension, usually sa gabi, at ang kanyang fluid intake ay mostly concentrated sa second half ng araw.",
                "evaluation" => "Ang urinary incontinence ni Tatay ay isang complex na isyu na may physical, psychological, at social dimensions na kailangang ma-address sa comprehensive na paraan. Higit sa lahat, mahalagang maintindihan na hindi ito normal na bahagi ng pagtanda at kadalasang may underlying causes na maaaring ma-manage. Iminumungkahi ko ang sensitibong pag-alalay sa kanya para ma-encourage na magkaroon ng medical evaluation sa urologist o continence specialist para matukoy ang specific type at causes ng kanyang incontinence. Habang hinihintay ang medical consultation, maraming practical interventions na maaaring ipatupad kaagad. Una, inirerekomenda ko ang pag-establish ng timed voiding schedule—regular na pagpunta sa bathroom every 2-3 hours regardless kung may urge o wala, para maprevent ang bladder over-distention. Tinuruan ko rin ang pamilya ng pelvic floor muscle exercises na maaaring subukan ni Tatay para mapalakas ang kanyang bladder control. Para sa medication concerns, nakipag-usap ako sa kanila tungkol sa kahalagahan ng pag-consult sa doktor tungkol sa kanyang diuretic timing—shifting ito from evening sa morning para mabawasan ang nighttime urination. Para sa fluid management, binigyang-diin ko na ang fluid restriction ay hindi recommended at maaari pang magpalala ng problema. Sa halip, iminumungkahi ko ang balanced fluid distribution throughout the day, avoiding large amounts 2-3 hours before bedtime. Para sa containment at hygiene, binigyan ko sila ng information tungkol sa modern incontinence products na discrete, effective at comfortable—hindi lang adult diapers kundi pati specialized male guards at shields na less bulky at designed specifically para sa male anatomy. Nagdevelop din ako ng proper skin care protocol para ma-maintain ang skin integrity: paggamit ng pH-balanced cleansers instead of regular soap, thorough but gentle drying, at application ng moisture barrier cream sa vulnerable areas. Para sa environmental modifications, nag-recommend ako ng pagkakaroon ng waterproof mattress protectors, discreet waterproof seating pads para sa kanyang favorite chairs, at ang pagsisiguro na may accessible toilet facilities sa lahat ng areas ng bahay, including potential na pagkabit ng bedside commode kung nahihirapan siyang makarating sa toilet sa gabi. Sa psychological aspect naman, ipinaliwanag ko sa pamilya ang kahalagahan ng normalization ng condition at pag-iwas sa stigmatizing language. Para sa social reintegration, nakipag-brainstorm ako sa pamilya tungkol sa mga paraan para matulungan si Tatay na magkaroon muli ng confidence sa paglabas—halimbawa, pre-planning ng trips with knowledge of toilet locations, pagdadala ng emergency supplies sa discrete bag, at pag-schedule ng social activities after na-void na ang bladder. Lastly, gumawa ako ng discrete method para kay Tatay para ma-track ang kanyang voiding patterns, accidents, at potential triggers, na magiging valuable information para sa kanyang future medical consultation."
            ],
            [
                "assessment" => "Si Nanay ay nagpapakita ng increasing challenges sa pagpapanatili ng kanyang hand hygiene at infection prevention practices, na naging concerning sa panahon ng patuloy na health risks sa komunidad. Sa aking mga pagbisita sa nakaraang tatlong linggo, naobserbahan ko ang inconsistent handwashing practices—napansin kong hindi siya regular na naghuhugas ng kamay bago kumain, pagkatapos gumamit ng banyo, o pagkatapos umubo o bumahing. Minsan, kapag naghuhugas man siya, nagmamadali siya at ginagawa ito sa loob ng 5-10 seconds lang, nang walang sabon, at hindi natatanggal ang dumi sa pagitan ng mga daliri at sa ilalim ng kuko. May ilang beses na nakita kong hinahawakan niya ang kanyang face, eyes at bibig nang walang prior handwashing, kahit pagkatapos hawakan ang potentially contaminated surfaces tulad ng door knobs at hand rails. Kapag tinanong kung bakit, sinabi niyang 'hindi naman ako lumalabas ng bahay kaya hindi ako makakakuha ng sakit' at minsan ay sinasabi rin niyang masakit ang kanyang mga kamay dahil sa arthritis kaya nahihirapan siyang gumamit ng sabon at mag-scrub nang matagal. Napansin ko rin na limited ang access niya sa handwashing facilities—ang banyo ay nasa second floor habang madalas siyang nasa ground floor, at walang hand sanitizer o hygiene products na readily available sa common areas ng bahay. Sa usapin naman ng personal protective equipment, mahirap para sa kanya ang pagsuot ng face mask nang maayos dahil sa kanyang eyeglasses na nagfo-fog up at nahihirapan siyang i-secure ito sa kanyang ears. Bukod dito, napansin ko na may misconceptions siya tungkol sa infection transmission at prevention, at minsan ay naniniwala sa mga hindi siyentipikong preventive measures habang nini-neglect ang evidence-based practices.",
                "evaluation" => "Ang hand hygiene at infection prevention challenges ni Nanay ay nangangailangan ng multifaceted approach na naka-focus sa education, environmental modifications, at practical adaptations para sa kanyang physical limitations. Una sa lahat, iminumungkahi ko ang provision ng clear at simplified education tungkol sa germ theory at infection transmission pathways sa paraang madaling maintindihan para sa kanyang age at educational background. Gumamit ako ng visual aids at demonstrations para ipakita kung paano kumakapit ang germs sa hands at surfaces, at kung paano ang proper handwashing technique ay nakakaalis ng mga ito. Para sa kanyang accessibility concerns, inirerekomenda ko ang strategic placement ng hand hygiene stations sa key areas ng bahay—portable sink o dedicated handwashing basin sa ground floor, pump bottles ng liquid soap na madaling gamitin kahit may arthritis, at multiple bottles ng alcohol-based hand sanitizer sa high-traffic areas at sa kanyang regular sitting areas. Para sa kanyang arthritic hands, nagbigay ako ng specific adaptations: paggamit ng foaming soap na hindi kailangang maraming scrubbing, installation ng lever-type faucets imbes na knobs para madaling buksan kahit may limited hand strength, at hooks para sa towels na nakalagay sa accessible height. Binigyan din namin siya ng hand lotion na non-greasy para i-apply after handwashing para maiwasan ang skin dryness at cracking. Para sa mask-wearing challenges, sinubukan namin ang different styles ng masks hanggang makahanap ng comfortable fit para sa kanya—nakita naming mas effective ang masks na may adjustable nose bridge at ear loops, at ang paggamit ng anti-fog spray para sa kanyang glasses. Upang ma-reinforce ang proper timing ng handwashing, nagdisenyo ako ng simple reminder system—visual cues tulad ng colorful signs sa strategic locations at gentle verbal reminders mula sa family members sa critical times (before meals, after toilet use). Para sa behavioral aspect, iminungkahi ko ang positive reinforcement strategy sa halip na criticism kapag nakaligtaan niya ang handwashing. Nagbigay din ako ng update tungkol sa current infectious disease situation sa kanilang locality para mabigyang-diin ang continued importance ng infection prevention kahit 'hindi lumalabas ng bahay.' Tinuruan ko rin ang pamilya ng proper cleaning at disinfection ng high-touch surfaces sa bahay tulad ng door handles, light switches, at remote controls. Lastly, nag-discuss kami ng red flag symptoms that would warrant medical attention, at ng kahalagahan ng vaccination para sa preventable diseases tulad ng flu at pneumonia appropriate sa kanyang age at health condition."
            ],

            // Nutrition and hydration assessments
            [
                "assessment" => "Si Lolo ay nagpapakita ng lumalalang signs ng dehydration at inadequate fluid intake na naobserbahan ko sa nakaraang apat na linggo. Sa aking mga regular na pagbisita, nakita kong limited ang kanyang fluid consumption—umiinom lang siya ng humigit-kumulang 2-3 small cups (estimated 400-600ml) ng tubig sa buong araw, significantly below ang recommended intake para sa kanyang edad at timbang. Kapag tinanong kung bakit hindi siya umiinom ng sapat na tubig, madalas niyang sinasabi na 'hindi ako nauuhaw' o 'ayaw kong laging pumunta sa banyo.' Nakikita ko rin ang physical manifestations ng chronic mild dehydration: dry at flaky lips, reduced skin turgor lalo na sa back of hands, dry oral mucosa, at concentrated dark-yellow urine na may strong odor. Sa pakikipag-usap sa pamilya, nalaman ko na nagkaroon ng progressive decline sa kanyang fluid intake sa nakaraang 3 buwan. Dating mahilig siya sa sabaw at mga soup-based dishes, pero nawalan na siya ng interes sa mga ito. Bukod dito, napagmasdan ko na nahihirapan siyang uminom mula sa regular na baso dahil sa kanyang hand tremors, at minsan ay natatakot siyang masamid kaya umiiwas na lang sa pag-inom. Naobserbahan ko rin ang cognitive symptoms na maaaring related sa inadequate hydration: increased confusion sa hapon, irritability, at occasional headaches na nare-resolve after drinking adequate amounts of fluid. Ayon sa kanyang asawa, nagkakaroon din si Lolo ng urinary tract infections more frequently kaysa dati—nagkaroon siya ng 3 UTIs sa nakaraang 5 buwan. Sa pagsusuri ng kanyang medication list, nakita kong may diuretic siya para sa kanyang heart condition at nagte-take din siya ng constipation medications na may diuretic effect.",
                "evaluation" => "Ang chronic mild dehydration ni Lolo ay isang seryosong concern na nangangailangan ng immediate at structured intervention dahil ito'y nagdudulot ng significant health risks tulad ng increased UTIs, cognitive changes, at constipation. Una sa lahat, nagtakda kami ng clear hydration target: sa kanyang timbang at condition, ideally 1,800-2,000ml ng fluids ang kailangan daily. Para maabot ito nang maayos, gumawa kami ng personalized hydration schedule na naka-distribute throughout the day—small, frequent sips rather than large amounts at one time para maiwasan ang feeling of fullness at frequent urination sa iisang punto ng araw. Para sa concern niya sa banyo trips, iminungkahi ko ang concentration ng fluid intake sa morning at early afternoon para mabawasan ang nighttime urination at sleep disruption. Sa practical aspect, binigyan namin siya ng specialized cups at containers: two-handled mug na madaling hawakan kahit may tremors, spill-proof cups na may built-in straw, at colorful water bottle na may measurements para ma-track ang intake. Para sa palatability at variety concerns, nagbigay kami ng lista ng hydrating alternatives to plain water—diluted fruit juices, herbal teas, flavored water with fresh fruit slices, clear soups, at high-water content foods tulad ng watermelon at cucumber. Binigyan din namin ng training ang kanyang caregivers sa proper assistance techniques para sa drinking—proper positioning (upright at slightly forward), pacing, at supervision para maiwasan ang choking hazards. Para sa monitoring at motivation, gumawa kami ng simple tracking system: hydration chart na may stickers o check marks para sa bawat glass na naiinom, at weekly review ng progress. Nag-develop din kami ng cues at reminders—placing filled water containers in visible locations, at gentle verbal reminders every 1-2 hours. Kinonsulta rin namin ang kanyang doktor tungkol sa kanyang diuretic schedule para ma-optimize ito, at para ma-evaluate kung kailangan ng adjustment based sa kanyang hydration status. Nakipag-coordinate din ako sa physical therapist para sa exercises para mapalakas ang kanyang swallowing muscles at mabawasan ang risk of choking. Sa kanyang skin care, iminungkahi ko ang paggamit ng gentle moisturizers para sa kanyang dry skin habang nagwo-work kami sa pag-improve ng hydration from within. Ipinaliwanag ko rin sa pamilya ang early warning signs ng severe dehydration na nangangailangan ng medical attention, at binigyan sila ng guidelines para sa fluid needs during hot weather o kapag may fever si Lolo. Sa susunod kong pagbisita, plano kong i-evaluate ang improvement sa kanyang hydration status at urinary output, at i-assess kung kailangan ng further adjustments sa hydration plan."
            ],
            [
                "assessment" => "Si Nanay ay nagpapakita ng matinding signs ng malnutrition at significant na pagbaba ng timbang na unti-unting lumalala sa nakaraang tatlong buwan. Sa pag-monitor namin sa kanyang weight, nakita na bumaba siya ng 6.2 kilograms (13.6 pounds) mula sa aming baseline assessment, representing approximately 11% ng kanyang starting body weight. Sa aking visual assessment, napansin ko ang visible muscle wasting sa kanyang temple area, upper arms, at quadriceps. Ang kanyang damit ay halatang maluwag na, at kapansin-pansin ang kanyang prominent collar bones at sunken cheeks. Ayon sa food diary na kinumpleto ng kanyang pamilya sa nakaraang linggo, ang kanyang average daily intake ay significantly below sa estimated caloric at protein requirements—kumakain lang siya ng approximately 850-950 calories at 25-30 grams ng protein daily, substantially lower sa recommended 1,500 calories at 60 grams ng protein para sa isang babaeng kanyang edad at build. Sa aking observation ng kanyang meal times, nakita kong mabagal siyang kumain, madalas na nagpapahinga sa pagitan ng mga kagat, at iniiwanan niya ang kalahati o mahigit pa ng serving. Kapag tinatanong kung bakit, sinasabi niyang 'busog na ako' o 'wala akong ganang kumain.' Bukod sa reduced quantity, napansin ko rin ang poor variety sa kanyang diet—umiiwas siya sa mga meat products, fresh fruits, at vegetables, at mas pinipili niya ang mga soft, carbohydrate-rich foods tulad ng lugaw, white bread, at instant noodles. Ikinuwento ng kanyang anak na dumanas si Nanay ng malubhang COVID-19 infection limang buwan ang nakalipas, at mula noon ay hindi na bumalik ang kanyang normal na appetite. Sa aking assessment ng possible contributing factors, napansin ko ang kanyang altered taste sensation ('lahat ng pagkain ay pareho ang lasa'), early satiety, at occasional dysphagia lalo na sa mga dry at solid foods. Mayroon din siyang ill-fitting dentures na nagdudulot ng discomfort kapag kumakain.",
                "evaluation" => "Ang malnutrition at significant weight loss ni Nanay ay nangangailangan ng comprehensive at multidisciplinary approach. Inirerekomenda ko ang medical assessment para ma-evaluate ang underlying causes ng altered taste at reduced appetite, particularly post-COVID effects at potential medication side effects. Habang hinihintay ang medical evaluation, maaari nating simulan ang nutritional rehabilitation plan na naka-focus sa nutrient-dense at calorie-dense meals na naka-customize para sa kanyang preferences at eating capabilities. Una, ipinropeso ko ang 'food first' approach—fortifying ang regular meals niya gamit ang high-calorie at high-protein additions: full cream milk powder sa lugaw at soups, nut butters sa bread, at olive oil sa rice at vegetables. Binigyan ko ang family ng recipes para sa nutrient-dense smoothies at shakes na madaling inumin kahit may reduced appetite, incorporated with complete protein sources tulad ng milk protein, yogurt, at silken tofu. Para sa taste alterations, iminungkahi ko ang flavor enhancement strategies: paggamit ng herbs, spices, at natural flavor enhancers tulad ng calamansi at garlic; experimenting with temperature variations dahil minsan mas pronounced ang flavors sa cold o room temperature foods; at pagsubok ng varying textures para ma-improve ang sensory experience. Para sa meal structure, inirerekomenda ko ang multiple small meals (5-6 times daily) instead of three large meals, with protein-containing foods prioritized at the beginning of each meal when appetite is strongest. Binigyang-emphasis ko ang kahalagahan ng pleasant dining environment—hindi kumakain habang nanonood ng distressing news, pagkakaroon ng colorful food presentation, at social eating with family members whenever possible. Para sa oral health concerns, inirerekomenda ko ang immediate dental consult para ma-evaluate at ma-adjust ang kanyang dentures para mabawasan ang discomfort habang kumakain. Nakipag-coordinate din ako sa speech therapist para sa swallowing evaluation at training kung kinakailangan. Sa aspect ng monitoring, binigyan ko ang pamilya ng simple tool para i-track ang kanyang food intake, including ang calorie at protein count ng common foods sa kanyang diet, at regular weight monitoring gamit ang consistent weighing protocol (same time of day, similar clothing). Iminungkahi ko rin ang supplementation with oral nutritional supplements (commercial nutrition drinks) sa pagitan ng meals, hindi as meal replacements. Para sa specific nutrient concerns, nakipag-consult ako sa primary physician tungkol sa appropriateness ng vitamin D, calcium, at B vitamins supplementation based sa kanyang specific deficiencies. Hinggil sa psychological aspect, kinausap ko ang mental health provider para tulungan si Nanay sa kanyang possible depression at anxiety na maaaring nagko-contribute sa kanyang poor appetite. Binigyang-diin ko sa pamilya na ang refeeding process ay dapat gradual para maiwasan ang refeeding syndrome, at sinabi ko ang mga warning signs na nangangailangan ng urgent medical attention."
            ],
            [
                "assessment" => "Si Lola ay nagpapakita ng lumalala at concerning symptoms ng dysphagia (difficulty swallowing) na naging apparent sa nakaraang dalawang buwan. Sa aking mga pagbisita, naobserbahan ko multiple incidents ng pagkabilaok, umuubo, at nahihirapan habang kumakain o umiinom. Partikular na napansin ko na nahihirapan siya sa mga thin liquids tulad ng tubig at juice—kapag umiinom siya, may instances na naiiwan ang liquid sa kanyang mouth momentarily bago nya malulon, at may episodes ng coughing after swallowing. Sa mga solid foods, mas nahihirapan siya sa mga dry at crumbly textures tulad ng kanin at tinapay, at napansin ko na madalas siyang gumagamit ng tubig para 'itulak' ang pagkain pababa. Sinabi ng kanyang anak na dumaan si Lola sa mild stroke anim na buwan ang nakalipas, at mula noon ay unti-unting lumalala ang kanyang swallowing difficulties. Dahil sa mga challenges na ito, nabawasan ang kanyang food at fluid intake—tumatagal na ang kanyang meals ng 45-60 minuto, at madalas ay tinatanggihan na niya ang kanyang dating favorite foods dahil sa takot na mabilaukan. Napansin ko na naging selective na siya sa kanyang pagkain, focusing primarily sa mga soft foods tulad ng lugaw, sopas, at mashed vegetables. Maliban dito, nakita ko na may subtle changes sa kanyang vocal quality, minsan ay medyo 'wet' o 'gurgling' ang tunog ng kanyang voice lalo na after eating or drinking. In recent weeks, ayon sa pamilya, nagkakaroon na rin siya ng occasional low-grade fever at may questionable episodes ng aspiration pneumonia na kinailangang i-treat ng antibiotics. Bukod sa physical symptoms, kita ko rin ang psychological impact ng condition kay Lola—ilang beses na niyang sinabing nahihiya siyang kumain sa harap ng ibang tao at tumatangging sumali sa family meals dahil sa kanyang condition.",
                "evaluation" => "Ang dysphagia symptoms ni Lola ay nangangailangan ng urgent evaluation at intervention dahil ang aspiration at malnutrition ay seryosong risks. Una sa lahat, inirerekomenda ko ang immediate referral sa speech-language pathologist na specialized sa dysphagia para sa comprehensive swallowing assessment, possibly kasama ang instrumental evaluations tulad ng modified barium swallow study kung available. Habang naghihintay ng professional assessment, binigyan ko ang pamilya ng emergency dysphagia management strategies at techniques para mabawasan ang aspiration risk. Sa aspect ng positioning, tinuruan ko sila ng proper positioning during meals: fully upright at 90-degree angle, slight chin tuck position, at pananatili sa upright position for at least 30 minutes after eating para maiwasan ang reflux at post-meal aspiration. Para sa texture modifications, gumawa ako ng specific guidelines based sa aking initial observations: thickening ng thin liquids gamit ang commercial thickeners o natural thickeners tulad ng gelatinized rice o saging; moist, soft food preparation; at pag-iwas sa high-risk foods tulad ng dry, crumbly, o sticky textures. Nagbigay din ako ng demonstration sa compensatory swallowing techniques: multiple swallows per bite/sip; alternating solids at liquids; at targeted throat clearing. Para sa mealtime management, binigyan ko ang pamilya ng practical strategies: scheduling meals during peak energy times; smaller, more frequent meals; minimizing distractions during mealtimes; at proper pacing (small bites, complete swallow before next bite). Sa usapin ng oral hygiene, binigyang-diin ko ang kahalagahan ng thorough oral care after meals para maiwasan ang bacterial growth at reduce pneumonia risk. Nakipag-coordinate din ako sa kanyang primary care provider tungkol sa posible pagbabago ng kanyang medication formulations (e.g., liquid instead of pills) at para i-assess kung mayroong medications na nakakaapekto sa kanyang swallowing. Para sa hydration concerns, gumawa ako ng hydration schedule with appropriate thickened liquids, at nagmungkahi ng alternative ways para ma-meet ang fluid needs, tulad ng high-moisture foods. Sa nutritional aspect, nakipag-consult ako sa dietitian para sa dysphagia-appropriate meal plan na nutritionally complete pero still manageable considering her swallowing limitations. Binigyan ko rin ng training ang pamilya kung paano mag-identify ng aspiration signs at kung kailan tumawag para sa emergency help. Sa psychological component, kinausap ko si Lola at ang kanyang pamilya tungkol sa importance ng maintaining dignity during meals at strategies para comfortable pa rin siya sa family gatherings kahit may dysphagia. Para sa ongoing monitoring, gumawa ako ng simple tracking system para sa meal tolerance, any choking incidents, voice quality changes, at respiratory symptoms na potential signs ng silent aspiration."
            ],
            [
                "assessment" => "Si Tatay ay nagpapakita ng malaking pakikibaka sa pagbalanse ng kanyang diabetes management at traditional Filipino food preferences, na naging significant challenge sa nakaraang dalawang buwan mula nang ma-diagnose siya ng Type 2 diabetes. Sa aking pagsusuri ng kanyang weekly blood glucose readings, nakita ko ang roller-coaster pattern ng significant fluctuations—madalas na lumampas sa 200 mg/dL ang kanyang post-meal readings, particularly after consuming traditional Filipino dishes na high in refined carbohydrates at sweets. Sa pagmamasid ko sa kanyang eating patterns at food choices, napansin ko ang persistent attachment niya sa traditional staples tulad ng kanin (3 cups per meal), white bread, matatamis na kakanin, at carbohydrate-rich ulam na may sawsawan at mga fried dishes. Kapag nakikipag-usap tungkol sa kanyang condition, paulit-ulit niyang binabanggit na 'hindi ko mabubuhay nang walang kanin' at 'masyadong bland ang diet na ibinibigay sa akin.' Ayon sa kanyang asawa, tumanggi si Tatay na sundin ang meal plan na binigay ng hospital dietitian dahil masyadong malayo ito sa kanyang usual diet at cultural food preferences. Ang mga attempt sa pag-introduce ng brown rice, increased vegetables, at lean proteins ay nakatagpo ng strong resistance. Sa halip, sinusubukan ni Tatay ang 'feast-and-fast' approach—kumakain siya ng regular Filipino meals nang walang restrictions, tapos nagsa-skip ng meals o drastically nag-reduce ng food intake kapag nakitang mataas ang kanyang blood sugar readings. Bukod sa inappropriate meal compositions, nakita ko rin ang inconsistent meal timing na further contributing sa glucose fluctuations—minsan ay 6-7 hours ang pagitan ng meals, tapos mabibigat na meals sa gabi bago matulog. Nabanggit din ng pamilya na naging mahirap ang grocery shopping at meal preparation dahil nangangailangan ng separate meals para kay Tatay o radical changes sa family recipes na sinasalungat niya.",
                "evaluation" => "Ang struggle ni Tatay sa pagbalanse ng kanyang diabetes management at cultural food preferences ay nangangailangan ng culturally-sensitive approach na hindi completely restricting ang traditional foods na may emotional at cultural significance sa kanya. Imbis na complete elimination, iminumungkahi ko ang strategy ng modification at portion control. Una sa lahat, nakipag-ugnayan ako sa registered dietitian na familiar sa Filipino cuisine para gumawa ng culturally-appropriate meal plan na nire-retain ang elements ng traditional meals pero with diabetes-friendly adaptations. Para sa rice consumption na very important kay Tatay, iminungkahi ko ang gradual transition approach: unti-unting pagbabawas ng serving size (mula 3 cups to 1 cup per meal); mixing white rice with brown rice or adlai sa gradually increasing proportions; at pagshift ng carbohydrate distribution throughout the day, with smaller portions sa dinner at larger sa breakfast at lunch. Binigyan ko sila ng practical recipe modifications para sa Filipino favorites: paggamit ng lean cuts ng meat para sa adobo; modification ng cooking techniques (baking or steaming instead of frying); reduction ng asukal sa mga traditional desserts at paggamit ng artificial sweeteners o natural alternatives tulad ng cinnamon para sa flavor. Para sa sawsawan at condiments na important sa Filipino cuisine, nagbigay ako ng guidelines sa reduced-sodium soy sauce, vinegar-based options instead of sweet sauces, at smaller portions ng traditional sawsawan. Sa aspect ng meal timing at structure, binigyan ko sila ng fixed schedule na culturally appropriate pero aligned with diabetes management principles: three consistent main meals with carefully planned merienda, ensuring no more than 4-5 hours between eating occasions. Nagbigay din ako ng guidance sa grocery shopping at meal planning—specific brands ng healthier Filipino food products, strategies para sa efficient preparation ng diabetes-friendly Filipino meals, at tips para sa eating out sa Filipino restaurants or family gatherings. Para sa blood glucose monitoring, binigyan ko si Tatay ng personalized testing schedule para matutukan ang effect ng specific Filipino foods sa kanyang glucose levels, para matulungan siyang makita kung aling traditional foods ang relatively safe at alin ang may highest impact. Upang ma-address ang psychological aspect, nakipag-usap ako kay Tatay tungkol sa cultural significance ng food at nakinig sa kanyang concerns, then worked on finding acceptable compromises rather than imposing rigid restrictions. Minungkahi ko rin ang pagbuo ng support group with other Filipino seniors with diabetes para magkaroon siya ng community na nagda-navigate ng same cultural challenges. Para sa pamilya, ibinigay ko ang strategies para sa supportive approach: avoiding food policing, celebrating small victories, at participating in the dietary changes as a family para hindi ma-isolate si Tatay. Sa follow-up, plano kong i-monitor ang kanyang glucose patterns, satisfaction level sa adapted diet, at overall compliance, adjusting our approach based sa findings."
            ],
            [
                "assessment" => "Si Lola ay nagpapakita ng matinding kahirapan sa pagkumpleto ng kanyang meals at hindi consistent na meal structure na naobservahan ko sa nakaraang tatlong linggo. Sa pag-monitor ko ng kanyang food intake patterns, nakita ko na hindi siya nakakakumpleto ng standard three full meals daily—madalas ay kinukuha lang niya ang ilang kagat mula sa bawat meal tapos iniiwanan na ang natitirang pagkain. Sa pagsusuri ng kanyang plate pagkatapos kumain, napapansin ko na kadalasan ay halos hindi nagalaw ang mga protein sources tulad ng karne at isda, at mostly ang carbohydrates at small amounts ng gulay lang ang nakakain. Sinabi ng kanyang tagapag-alaga na simula nang mamatay ang kanyang asawa dalawang taon na ang nakalipas, naging inconsistent ang kanyang eating patterns—minsan ay kumakain siya ng one full meal lang sa buong araw, at sa ibang pagkakataon ay multiple small snacks ang kinakain niya throughout the day pero hindi formal meals. Napansin ko rin na nagkakaroon siya ng difficulty sa physical aspects ng eating—napapagod ang kanyang arms kapag ginagamit ang utensils nang matagal, at nahihirapan siyang i-cut ang mga food items dahil sa kanyang arthritis. Bukod dito, observed ko na kapag mag-isa siyang kumakain, mas mabababa ang kanyang food intake at mas mabilis niyang tinatapos ang meal, compared sa kapag may kasama siyang kumakain. Ayon sa pamilya, naging vocal si Lola tungkol sa 'kawalan ng kasiyahan sa pagkain' at madalas na sinasabi na 'sayang lang ang pagkain' dahil 'maliit na ang kinakain ko.' Nag-aalala rin ang pamilya na marami sa kanyang dating favorite foods ay hindi na niya kinakain, at nahihirapan silang i-determine kung anong pagkain ang ise-serve para ma-maximize ang kanyang intake. May instances din na nag-request si Lola ng specific foods pero pagdating ng meal time, ay hindi niya ito kakainin at sasabihing 'hindi ko pala gusto.'",
                "evaluation" => "Ang meal completion at structure challenges ni Lola ay complex issue na may physical, psychological, at social components na kailangang ma-address ng comprehensive na paraan. Una sa lahat, inirerekomenda ko ang shift sa 'quality over quantity' approach—focusing on nutrient-dense smaller meals rather than standard-sized plates na overwhelming para sa kanya. Para sa practical meal structure, binuo ako ng 'mini-meal' plan na naka-based sa principles ng 5-6 small meals daily instead of three large ones, each containing balanced nutrition pero sa manageable volume. Sa physical challenges ng self-feeding, tinulungan ko ang pamilya sa pag-identify ng appropriate adaptive utensils para sa arthritis—built-up handles, lightweight utensils, plate guards, at non-slip mats. Nagbigay din ako ng demonstration ng proper pre-cutting ng food sa kitchen para hindi na kailangang i-cut ni Lola ang food sa plate. Para sa food preferences at appeal, iminungkahi ko ang systematic exploration ng current food preferences through a 'food diary' approach—regular documentation ng foods na kinakain at tinatanggihan, pati na ang environmental factors at mood during meals. Binigyan ko rin sila ng strategies para enhancer sensory appeal ng meals—increased use ng herbs at spices na preferred ni Lola, variety ng textures at colors sa plate, at proper temperature ng food (ensuring na hindi lukewarm ang dating hot foods). Sa social aspect ng dining, nag-recommend ako ng scheduled 'social meals' kahit once a day, kung saan guaranteed na may kasama si Lola sa pagkain, dahil nakita na mas mahusay ang intake niya sa social setting. Binuo rin namin ang 'comfort food inventory'—list ng foods na associated sa positive memories at experiences para kay Lola, especially mula sa mga panahong kasama pa niya ang kanyang asawa, para regular na ma-incorporate sa meal planning. Para sa nutritional density, ginawa ko ang recommendations para sa nutrient fortification ng foods na consistently kinakain niya—adding milk powder sa soups, healthy fats sa vegetables, at hidden protein sources sa carbohydrate foods na preferred niya. Inirerekomenda ko rin ang pagkakaroon ng consistent meal environment—same place, similar time, proper seating position, at removal ng distractions during eating. Para sa psychological aspect, kinausap ko ang pamilya tungkol sa importance ng zero pressure approach sa meals—avoiding comments about how much she's eating at paggamit ng positive reinforcement instead. Iminumungkahi ko rin ang involvement ni Lola sa aspects ng meal planning at preparation kung physical na kaya, para magkaroon siya ng sense of control at ownership sa kanyang nutrition. Para sa long-term monitoring, tinuruan ko ang family kung paano mag-implement ng simple food intake record at weekly weighing para ma-track ang nutritional adequacy at any concerning trends, with guidance kung kailan kailangan ng medical intervention para sa significant weight loss o nutritional deficiencies."
            ]
        ];
    }

    /**
     * Helper function to create interventions for a weekly care plan
     */
    private function createWeeklyCarePlanInterventions($weeklyCarePlan, $planDate, &$interventionCount)
    {
        try {
            // Create 2-5 interventions per weekly care plan
            $interventionsToCreate = $this->faker->numberBetween(2, 5);
            
            for ($j = 0; $j < $interventionsToCreate; $j++) {
                // Get a random care category first
                $careCategory = CareCategory::inRandomOrder()->first();
                
                // If no care category found, skip this intervention
                if (!$careCategory) {
                    continue;
                }
                
                // Decide if this will be a custom intervention (30% chance)
                $isCustom = $this->faker->boolean(30);
                
                if ($isCustom) {
                    // Create a CUSTOM intervention - only intervention_description, no intervention_id
                    $customDescription = $this->getSimpleCustomDescription($careCategory->care_category_id);
                    
                    WeeklyCarePlanInterventions::create([
                        'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                        'intervention_id' => null, // No intervention_id for custom
                        'care_category_id' => $careCategory->care_category_id,
                        'intervention_description' => $customDescription, // Custom description
                        'duration_minutes' => $this->faker->randomFloat(2, 10, 60),
                        'implemented' => $planDate->lt(Carbon::now()) ? $this->faker->boolean(80) : false
                    ]);
                } else {
                    // Create a NON-CUSTOM intervention - only intervention_id, no intervention_description
                    $intervention = Intervention::where('care_category_id', $careCategory->care_category_id)
                        ->inRandomOrder()->first();
                    
                    // If no intervention found for this category, get any intervention
                    if (!$intervention) {
                        $intervention = Intervention::inRandomOrder()->first();
                        if ($intervention) {
                            $careCategory = CareCategory::find($intervention->care_category_id);
                        }
                    }
                    
                    // Only create if we have a valid intervention
                    if ($intervention && $careCategory) {
                        WeeklyCarePlanInterventions::create([
                            'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                            'intervention_id' => $intervention->intervention_id, // Database intervention ID
                            'care_category_id' => $careCategory->care_category_id,
                            'intervention_description' => null, // No custom description for DB interventions
                            'duration_minutes' => $this->faker->randomFloat(2, 10, 60),
                            'implemented' => $planDate->lt(Carbon::now()) ? $this->faker->boolean(80) : false
                        ]);
                    }
                }
                
                $interventionCount++;
            }
        } catch (\Exception $e) {
            \Log::error("Failed creating interventions for weekly care plan {$weeklyCarePlan->weekly_care_plan_id}: " . $e->getMessage());
        }
    }

    /**
     * Helper function to add acknowledgement to a care plan
     */
    private function addCareplanAcknowledgement($weeklyCarePlan, $beneficiary, $planDate) 
    {
        if ($this->faker->boolean(70)) {
            // Beneficiary acknowledgement
            $weeklyCarePlan->acknowledged_by_beneficiary = $beneficiary->beneficiary_id;
            
            // Add acknowledgement signature JSON
            $acknowledgementData = [
                "acknowledged_by" => "Beneficiary",
                "user_id" => $beneficiary->beneficiary_id,
                "name" => $beneficiary->name,
                "date" => $planDate->copy()->addDays(rand(1, 3))->format('Y-m-d H:i:s'),
                "ip_address" => $this->faker->ipv4,
                "user_agent" => $this->faker->userAgent
            ];
            $weeklyCarePlan->acknowledgement_signature = json_encode($acknowledgementData);
            $weeklyCarePlan->save();
        } else {
            // Try to get a family member for this beneficiary
            $familyMember = FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)
                ->inRandomOrder()
                ->first();
                
            if ($familyMember) {
                $weeklyCarePlan->acknowledged_by_family = $familyMember->family_member_id;
                
                // Add acknowledgement signature JSON
                $acknowledgementData = [
                    "acknowledged_by" => "Family Member",
                    "user_id" => $familyMember->family_member_id,
                    "name" => $familyMember->name,
                    "date" => $planDate->copy()->addDays(rand(1, 3))->format('Y-m-d H:i:s'),
                    "ip_address" => $this->faker->ipv4,
                    "user_agent" => $this->faker->userAgent
                ];
                $weeklyCarePlan->acknowledgement_signature = json_encode($acknowledgementData);
                $weeklyCarePlan->save();
            }
        }
    }

}

