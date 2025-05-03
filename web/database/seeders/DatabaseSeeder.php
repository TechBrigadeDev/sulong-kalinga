<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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

        // 6. Generate vital signs and weekly care plans
        foreach (range(1, 10) as $index) {
            // Get a random care worker to be the creator of both records
            $careWorkerId = $careWorkers[array_rand($careWorkers)]->id;
            
            // Create vital signs with the care worker as creator
            $vitalSigns = VitalSigns::factory()->create([
                'created_by' => $careWorkerId,
            ]);
            
            // Create weekly care plan with the same care worker as creator and reference the vital signs
            $weeklyCarePlan = WeeklyCarePlan::factory()->create([
                'care_worker_id' => $careWorkerId,
                'vital_signs_id' => $vitalSigns->vital_signs_id,
                'created_by' => $careWorkerId,
            ]);
            
            // Create weekly care plan interventions for each category
            foreach (range(1, 7) as $careCategoryId) {
                WeeklyCarePlanInterventions::factory()->create([
                    'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                    'intervention_id' => $careCategoryId,
                ]);
            }
        }

        // 7. Generate notifications
        $this->generateNotifications();

        // 8. Generate conversations and messages
        $this->generateConversations();

        // // This is way below, please adjust gcp ID in the loop above
        // // 9. Manually create a portal account and a beneficiary linked to it for testing
        // $testPortal = \App\Models\PortalAccount::create([
        //     'portal_email' => 'test.portal@example.com',
        //     'portal_password' => '$2a$12$ukKcTdRuiedybx.jLcsfLew1ZiWUhy0b/x1lsb.f6NNQvs2SpjJau', // already hashed
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // \App\Models\Beneficiary::create([
        //     'first_name' => 'Test',
        //     'last_name' => 'Beneficiary',
        //     'civil_status' => 'Single',
        //     'gender' => 'Male',
        //     'birthday' => '1950-01-01',
        //     'primary_caregiver' => 'Jane Doe',
        //     'mobile' => '+639123456789',
        //     'landline' => '1234567',
        //     'street_address' => '123 Test St',
        //     'barangay_id' => 1,
        //     'municipality_id' => 1,
        //     'category_id' => 1,
        //     'emergency_contact_name' => 'John Doe',
        //     'emergency_contact_relation' => 'Brother',
        //     'emergency_contact_mobile' => '+639987654321',
        //     'emergency_contact_email' => 'emergency@example.com',
        //     'emergency_procedure' => 'Call 911',
        //     'beneficiary_status_id' => 1,
        //     'status_reason' => 'N/A',
        //     'general_care_plan_id' => 16, // hard coded ID from the last gcp id from above
        //     'portal_account_id' => $testPortal->id,
        //     'created_by' => 1,
        //     'updated_by' => 1,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);
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
     * Generate conversations and messages between users
     */
    private function generateConversations()
    {
        // Get all COSE staff users
        $staffUsers = User::where('role_id', '<=', 3)->get(); // Admins, Care Managers, Care Workers
        
        // Get some beneficiaries and family members for conversations
        $beneficiaries = Beneficiary::take(5)->get();
        $familyMembers = FamilyMember::take(5)->get();
        
        // Create 2 private conversations and 1 group chat for each staff member
        foreach ($staffUsers as $staffUser) {
            // For each staff member, create 2 private conversations
            for ($i = 0; $i < 2; $i++) {
                // Create a private conversation
                $conversation = Conversation::factory()->privateChat()->create();
                
                // Add the current staff user as a participant
                ConversationParticipant::create([
                    'conversation_id' => $conversation->conversation_id,
                    'participant_id' => $staffUser->id,
                    'participant_type' => 'cose_staff',
                    'joined_at' => now()->subDays(rand(1, 30)),
                ]);
                
                // Add another random staff user as participant
                $otherStaffUser = $staffUsers->where('id', '!=', $staffUser->id)->random();
                ConversationParticipant::create([
                    'conversation_id' => $conversation->conversation_id,
                    'participant_id' => $otherStaffUser->id,
                    'participant_type' => 'cose_staff',
                    'joined_at' => now()->subDays(rand(1, 30)),
                ]);
                
                // Create messages in this conversation from both participants
                $messageCount = rand(3, 10);
                
                $lastMessage = null;
                for ($j = 0; $j < $messageCount; $j++) {
                    // Alternate between the two participants
                    $senderId = ($j % 2 == 0) ? $staffUser->id : $otherStaffUser->id;
                    
                    $isUnsent = (rand(1, 20) === 1); // 5% chance of being unsent
                    $message = Message::create([
                        'conversation_id' => $conversation->conversation_id,
                        'sender_id' => $senderId,
                        'sender_type' => 'cose_staff',
                        'content' => \Faker\Factory::create()->sentence(rand(3, 15)),
                        'is_unsent' => $isUnsent, // Add this line
                        'message_timestamp' => now()->subDays(5)->addMinutes($j * 30),
                    ]);
                    
                    $lastMessage = $message;
                    
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
                    
                    // Mark some messages as read
                    if (rand(0, 1) == 1) { // 50% chance for each message
                        // Message is marked as read by the receiver
                        $readerId = ($j % 2 == 0) ? $otherStaffUser->id : $staffUser->id;
                        
                        MessageReadStatus::create([
                            'message_id' => $message->message_id,
                            'reader_id' => $readerId,
                            'reader_type' => 'cose_staff',
                            'read_at' => now()->subMinutes(rand(1, 60)),
                        ]);
                    }
                }
                
                // Update the conversation with the last message ID
                if ($lastMessage) {
                    $conversation->last_message_id = $lastMessage->message_id;
                    $conversation->save();
                }
            }
            
            // Create 1 group chat for each staff member with other staff and beneficiaries/family members
            $groupChat = Conversation::factory()->groupChat()->create([
                'name' => 'Team ' . \Faker\Factory::create()->word . ' Chat',
            ]);
            
            // Add the current staff user as a participant
            ConversationParticipant::create([
                'conversation_id' => $groupChat->conversation_id,
                'participant_id' => $staffUser->id,
                'participant_type' => 'cose_staff',
                'joined_at' => now()->subDays(rand(1, 30)),
            ]);
            
            // Add 2-4 other staff users as participants
            $otherStaffParticipants = $staffUsers->where('id', '!=', $staffUser->id)->random(rand(2, 4));
            foreach ($otherStaffParticipants as $participant) {
                ConversationParticipant::create([
                    'conversation_id' => $groupChat->conversation_id,
                    'participant_id' => $participant->id,
                    'participant_type' => 'cose_staff',
                    'joined_at' => now()->subDays(rand(1, 30)),
                ]);
            }
            
            // Randomly add 1-2 beneficiaries or family members
            if (rand(0, 1) == 1 && $beneficiaries->count() > 0) {
                $beneficiary = $beneficiaries->random();
                ConversationParticipant::create([
                    'conversation_id' => $groupChat->conversation_id,
                    'participant_id' => $beneficiary->beneficiary_id,
                    'participant_type' => 'beneficiary',
                    'joined_at' => now()->subDays(rand(1, 30)),
                ]);
            }
            
            if (rand(0, 1) == 1 && $familyMembers->count() > 0) {
                $familyMember = $familyMembers->random();
                ConversationParticipant::create([
                    'conversation_id' => $groupChat->conversation_id,
                    'participant_id' => $familyMember->family_member_id,
                    'participant_type' => 'family_member',
                    'joined_at' => now()->subDays(rand(1, 30)),
                ]);
            }
            
            // Generate 5-15 messages in the group chat from various participants
            $messageCount = rand(5, 15);
            $groupParticipants = ConversationParticipant::where('conversation_id', $groupChat->conversation_id)->get();
            
            $lastMessage = null;
            for ($j = 0; $j < $messageCount; $j++) {
                // Choose a random participant to send the message
                $randomParticipant = $groupParticipants->random();
                
                $message = Message::create([
                    'conversation_id' => $groupChat->conversation_id,
                    'sender_id' => $randomParticipant->participant_id,
                    'sender_type' => $randomParticipant->participant_type,
                    'content' => \Faker\Factory::create()->sentence(rand(3, 15)),
                    'message_timestamp' => now()->subDays(5)->addMinutes($j * 30),
                ]);
                
                $lastMessage = $message;
                
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
                
                // For each message, randomly mark it as read by some participants 
                // (except the sender who sent it)
                foreach ($groupParticipants as $participant) {
                    // Skip the sender (they've already seen their own message)
                    if ($participant->participant_id == $randomParticipant->participant_id && 
                        $participant->participant_type == $randomParticipant->participant_type) {
                        continue;
                    }
                    
                    // 70% chance this participant has read the message
                    if (rand(1, 10) <= 7) {
                        MessageReadStatus::create([
                            'message_id' => $message->message_id,
                            'reader_id' => $participant->participant_id,
                            'reader_type' => $participant->participant_type,
                            'read_at' => now()->subMinutes(rand(1, 60)),
                        ]);
                    }
                }
            }
            
            // Update the conversation with the last message ID
            if ($lastMessage) {
                $groupChat->last_message_id = $lastMessage->message_id;
                $groupChat->save();
            }
        }
        
        // Log how many conversations were created
        $totalConversations = Conversation::count();
        $totalMessages = Message::count();
        $totalAttachments = MessageAttachment::count();
        
        \Log::info("Generated {$totalConversations} conversations with {$totalMessages} messages and {$totalAttachments} attachments.");
    
    }

    /**
     * Populate the consolidated users table after all other data has been seeded
     */
    private function populateConsolidatedUsersTable()
    {
        // First, ensure the users table is empty to avoid duplicates
        \DB::table('users_consolidated')->truncate();
        
        $this->command->info('Populating consolidated users table...');
        
        // 1. Add COSE staff users
        $coseStaff = \App\Models\User::all();
        $this->command->info("Adding {$coseStaff->count()} COSE staff users");
        
        foreach ($coseStaff as $staff) {
            \DB::table('users_consolidated')->insert([
                'email' => $staff->email,
                'password' => $staff->password, // Already hashed
                'first_name' => $staff->first_name,
                'last_name' => $staff->last_name,
                'mobile' => $staff->mobile,
                'role_id' => $staff->role_id,
                'status' => $staff->status ?? 'Active',
                'user_type' => 'cose',
                'cose_user_id' => $staff->id,
                'portal_account_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // 2. Add beneficiaries
        $beneficiaries = \App\Models\Beneficiary::all();
        $this->command->info("Adding {$beneficiaries->count()} beneficiaries");
        
        foreach ($beneficiaries as $beneficiary) {
            // Get portal account information
            $portalAccount = \App\Models\PortalAccount::find($beneficiary->portal_account_id);
            
            if (!$portalAccount) {
                $this->command->warn("Portal account not found for beneficiary ID: {$beneficiary->beneficiary_id}. Skipping.");
                continue;
            }
            
            // Determine status based on beneficiary_status_id
            $status = $beneficiary->beneficiary_status_id == 1 ? 'Active' : 'Inactive';
            
            \DB::table('users_consolidated')->insert([
                'email' => $portalAccount->portal_email,
                'password' => $portalAccount->portal_password, // Already hashed
                'first_name' => $beneficiary->first_name,
                'last_name' => $beneficiary->last_name,
                'mobile' => $beneficiary->mobile,
                'role_id' => 4, // Beneficiary role
                'status' => $status,
                'user_type' => 'portal',
                'cose_user_id' => null,
                'portal_account_id' => $beneficiary->portal_account_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // 3. Add family members
        $familyMembers = \App\Models\FamilyMember::all();
        $this->command->info("Adding {$familyMembers->count()} family members");
        
        foreach ($familyMembers as $familyMember) {
            // Get related beneficiary to determine status
            $relatedBeneficiary = \App\Models\Beneficiary::find($familyMember->related_beneficiary_id);
            
            if (!$relatedBeneficiary) {
                $this->command->warn("Related beneficiary not found for family member ID: {$familyMember->family_member_id}. Skipping.");
                continue;
            }
            
            // Get portal account information
            $portalAccount = \App\Models\PortalAccount::find($familyMember->portal_account_id);
            
            if (!$portalAccount) {
                $this->command->warn("Portal account not found for family member ID: {$familyMember->family_member_id}. Skipping.");
                continue;
            }
            
            // Determine status based on related beneficiary
            $status = $relatedBeneficiary->beneficiary_status_id == 1 ? 'Active' : 'Inactive';
            
            \DB::table('users_consolidated')->insert([
                'email' => $portalAccount->portal_email,
                'password' => $portalAccount->portal_password, // Already hashed
                'first_name' => $familyMember->first_name,
                'last_name' => $familyMember->last_name,
                'mobile' => $familyMember->mobile,
                'role_id' => 5, // Family member role
                'status' => $status,
                'user_type' => 'portal',
                'cose_user_id' => null,
                'portal_account_id' => $familyMember->portal_account_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Count and report the results
        $totalConsolidatedUsers = \DB::table('users_consolidated')->count();
        $this->command->info("Successfully added {$totalConsolidatedUsers} users to the consolidated table");
    }
}