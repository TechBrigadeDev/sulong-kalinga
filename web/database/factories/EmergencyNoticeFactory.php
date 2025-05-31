<?php

namespace Database\Factories;

use App\Models\EmergencyNotice;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\User;
use App\Models\EmergencyType;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmergencyNoticeFactory extends Factory
{
    protected $model = EmergencyNotice::class;

    // Type-specific emergency messages
    protected $emergencyMessages = [
        // Medical Emergency (ID 1)
        1 => [
            "Beneficiary experiencing chest pain and difficulty breathing. Needs immediate assistance.",
            "Blood pressure dangerously high (190/110). Beneficiary feeling dizzy and confused.",
            "Beneficiary fell unconscious while eating. Need emergency assistance now.",
            "Severe allergic reaction with swelling and difficulty breathing. Requires immediate medical attention.",
            "Beneficiary showing signs of stroke - facial drooping and slurred speech. Please send help urgently."
        ],
        // Fall Incident (ID 2)
        2 => [
            "Beneficiary fell in the bathroom and cannot get up. No visible injuries but in pain.",
            "Fell while trying to get out of bed. Small cut on forehead and complaining of hip pain.",
            "Slipped on wet floor and fell. Cannot put weight on right leg. Please send assistance.",
            "Found beneficiary on floor this morning. Appears to have fallen during the night. Confused and in pain.",
            "Fell while walking to the kitchen. Has bruising on arm and can't move without severe pain."
        ],
        // Medication Issue (ID 3)
        3 => [
            "Beneficiary took double dose of blood pressure medication by mistake. What should we do?",
            "Cannot find insulin medication. Beneficiary's blood sugar reading is 310. Need assistance.",
            "Adverse reaction to new medication - severe rash and itching. Needs medical advice urgently.",
            "Beneficiary refusing to take prescribed medications for the past two days. Becoming agitated.",
            "Mixed up morning and evening medications. Concerned about potential interaction effects."
        ],
        // Mental Health Crisis (ID 4)
        4 => [
            "Beneficiary extremely disoriented and agitated. Does not recognize family members. Need help.",
            "Showing signs of extreme anxiety and panic. Breathing rapidly and unable to calm down.",
            "Expressing suicidal thoughts and very depressed. Need immediate mental health support.",
            "Hallucinating and showing paranoid behavior. Family cannot manage the situation.",
            "Severe confusion and agitation since yesterday. Not sleeping and becoming combative."
        ],
        // Other Emergency (ID 5)
        5 => [
            "Power outage in the area. Beneficiary uses oxygen concentrator and backup battery low.",
            "Water leak in home, floor slippery and dangerous. Beneficiary unable to leave safely.",
            "Beneficiary locked out of house in rainy weather. No shelter and getting cold.",
            "No food in the house for past two days. Beneficiary unable to go shopping due to mobility issues.",
            "Caregiver had emergency and left suddenly. Beneficiary needs immediate assistance."
        ]
    ];

    public function definition(): array
    {
        $sender_type = $this->faker->randomElement(['beneficiary', 'family_member']);
        $beneficiary = Beneficiary::inRandomOrder()->first();
        
        if ($sender_type === 'beneficiary') {
            $sender_id = $beneficiary->beneficiary_id;
        } else {
            // Try to get a family member related to the beneficiary
            $familyMember = FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)
                ->inRandomOrder()->first();
            
            // If no family member for this beneficiary, get any family member
            if (!$familyMember) {
                $familyMember = FamilyMember::inRandomOrder()->first();
            }
            
            $sender_id = $familyMember->family_member_id;
        }

        // Get random emergency type
        $emergencyType = EmergencyType::inRandomOrder()->first();
        $emergency_type_id = $emergencyType->emergency_type_id;
        
        // Get message based on emergency type
        $message = $this->getEmergencyMessage($emergency_type_id);

        // Random status with weighted distribution
        $statusChoices = [
            'new' => 20,
            'in_progress' => 30,
            'resolved' => 50  // Increased weight since we removed 'archived'
        ];
        
        $status = $this->faker->randomElement(
            $this->faker->randomElements(
                array_keys($statusChoices),
                1,
                true,
                array_values($statusChoices)
            )
        );
        
        // Base creation date
        $created_at = $this->faker->dateTimeBetween('-3 months', 'now');
        
        // Determine read status and action details based on status
        $read_status = false;
        $read_at = null;
        $action_type = null;
        $action_taken_by = null;
        $action_taken_at = null;
        $assigned_to = null;

        if ($status !== 'new') {
            $read_status = true;
            $read_at = $this->faker->dateTimeBetween($created_at, 'now');
            $assigned_to = User::where('role_id', '<=', 3)->inRandomOrder()->first()->id;
        }
        
        if ($status === 'in_progress') {
            $action_type = 'in_progress';
            $action_taken_by = User::where('role_id', '<=', 3)->inRandomOrder()->first()->id;
            $action_taken_at = $this->faker->dateTimeBetween($read_at ?? $created_at, 'now');
        } elseif ($status === 'resolved' || $status === 'archived') {
            $action_type = 'resolved';
            $action_taken_by = User::where('role_id', '<=', 3)->inRandomOrder()->first()->id;
            $action_taken_at = $this->faker->dateTimeBetween($read_at ?? $created_at, 'now');
        }

        return [
            'sender_id' => $sender_id,
            'sender_type' => $sender_type,
            'beneficiary_id' => $beneficiary->beneficiary_id,
            'emergency_type_id' => $emergency_type_id,
            'message' => $message,
            'status' => $status,
            'read_status' => $read_status,
            'read_at' => $read_at,
            'assigned_to' => $assigned_to,
            'action_type' => $action_type,
            'action_taken_by' => $action_taken_by,
            'action_taken_at' => $action_taken_at,
            'created_at' => $created_at,
            'updated_at' => $this->faker->dateTimeBetween($created_at, 'now'),
        ];
    }
    
    /**
     * Get a realistic emergency message based on the emergency type
     */
    protected function getEmergencyMessage($emergency_type_id)
    {
        if (isset($this->emergencyMessages[$emergency_type_id])) {
            return $this->faker->randomElement($this->emergencyMessages[$emergency_type_id]);
        }
        
        // Fallback to generic message if type doesn't match
        return "Emergency situation requiring immediate assistance. Please respond as soon as possible.";
    }
    
    public function asNew(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'new',
                'read_status' => false,
                'read_at' => null,
                'assigned_to' => null,
                'action_type' => null,
                'action_taken_by' => null,
                'action_taken_at' => null,
                'created_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            ];
        });
    }
    
    public function inProgress(): Factory
    {
        return $this->state(function (array $attributes) {
            $created_at = $this->faker->dateTimeBetween('-1 month', '-2 days');
            $read_at = $this->faker->dateTimeBetween($created_at, '-1 day');
            $action_taken_at = $this->faker->dateTimeBetween($read_at, 'now');
            
            return [
                'status' => 'in_progress',
                'read_status' => true,
                'read_at' => $read_at,
                'assigned_to' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id,
                'action_type' => 'in_progress',
                'action_taken_by' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id,
                'action_taken_at' => $action_taken_at,
                'created_at' => $created_at,
                'updated_at' => $action_taken_at,
            ];
        });
    }
}