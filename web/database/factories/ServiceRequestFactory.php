<?php

namespace Database\Factories;

use App\Models\ServiceRequest;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\User;
use App\Models\ServiceRequestType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRequestFactory extends Factory
{
    protected $model = ServiceRequest::class;

    // Type-specific service request messages
    protected $serviceRequestMessages = [
        // Home Care Visit (ID 1)
        1 => [
            "Requesting additional home care visit this week. Beneficiary has been feeling unwell and needs extra support.",
            "Need assistance with personal care as regular family caregiver will be away for 3 days.",
            "Requesting home visit for wound dressing change. Unable to do it properly ourselves.",
            "Need help with medication organization for the coming week. Prescription was changed by doctor.",
            "Requesting additional bath assistance visit as beneficiary had a minor accident."
        ],
        // Transportation (ID 2)
        2 => [
            "Need transportation to medical appointment at Northern Samar Provincial Hospital on the scheduled date.",
            "Requesting assistance with transportation to pharmacy to pick up new medication.",
            "Need transportation to local health center for scheduled vaccination.",
            "Transportation needed to attend senior citizen gathering at municipal hall.",
            "Requesting transportation assistance to visit family in neighboring barangay for important family event."
        ],
        // Medical Appointments (ID 3)
        3 => [
            "Need assistance attending doctor's appointment. Beneficiary needs help with mobility and understanding instructions.",
            "Requesting accompaniment to eye specialist appointment. Beneficiary will need help getting home after pupil dilation.",
            "Need assistance with upcoming physical therapy appointment. Transportation and in-clinic support needed.",
            "Requesting help with dental appointment. Beneficiary needs someone to explain dental work needed.",
            "Need assistance attending quarterly diabetes check-up. Will need note-taking and question-asking support."
        ],
        // Meal Delivery (ID 4)
        4 => [
            "Requesting meal delivery for 3 days due to temporary difficulty cooking after minor hand injury.",
            "Need meal assistance as cooking gas supply ran out and replacement delayed until next week.",
            "Beneficiary has special dietary needs after recent hospital discharge. Requesting meal support for 5 days.",
            "Regular meal provider (family member) has emergency. Need temporary meal delivery.",
            "Requesting softer food options for meal delivery as beneficiary has new dental issues."
        ],
        // Other Service (ID 5)
        5 => [
            "Need assistance reading and responding to important government letter received yesterday.",
            "Requesting help reorganizing bedroom furniture to make mobility easier for beneficiary.",
            "Need assistance with phone setup to enable video calls with family members abroad.",
            "Requesting help acquiring and setting up a raised toilet seat for easier bathroom use.",
            "Need assistance with small home repairs to prevent water leakage during rainy season."
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

        // Get random service type
        $serviceType = ServiceRequestType::inRandomOrder()->first();
        $service_type_id = $serviceType->service_type_id;
        
        // Get message based on service type
        $message = $this->getServiceRequestMessage($service_type_id);

        // Random status with weighted distribution
        $statusChoices = [
            'new' => 20,
            'approved' => 25,
            'rejected' => 25, 
            'completed' => 30
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
        $created_at = $this->faker->dateTimeBetween('-6 months', 'now');
        
        // Determine read status and action details based on status
        $read_status = false;
        $read_at = null;
        $action_type = null;
        $action_taken_by = null;
        $action_taken_at = null;
        $care_worker_id = null;
        $service_date = $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d');
        $service_time = $this->faker->time('H:i:s');

        if ($status !== 'new') {
            $read_status = true;
            $read_at = $this->faker->dateTimeBetween($created_at, 'now');
        }
        
        if ($status === 'approved' || $status === 'completed') {
            $action_type = 'approved';
            $care_worker_id = User::where('role_id', 3)->inRandomOrder()->first()->id; // Role 3 is care worker
            $action_taken_by = User::whereIn('role_id', [1, 2])->inRandomOrder()->first()->id; // Admin or care manager
            $action_taken_at = $this->faker->dateTimeBetween($read_at ?? $created_at, 'now');
        } elseif ($status === 'rejected') {
            $action_type = 'rejected';
            $action_taken_by = User::whereIn('role_id', [1, 2])->inRandomOrder()->first()->id; // Admin or care manager
            $action_taken_at = $this->faker->dateTimeBetween($read_at ?? $created_at, 'now');
        }

        return [
            'sender_id' => $sender_id,
            'sender_type' => $sender_type,
            'beneficiary_id' => $beneficiary->beneficiary_id,
            'service_type_id' => $service_type_id,
            'care_worker_id' => $care_worker_id,
            'service_date' => $service_date,
            'service_time' => $service_time,
            'message' => $message,
            'status' => $status,
            'read_status' => $read_status,
            'read_at' => $read_at,
            'action_type' => $action_type,
            'action_taken_by' => $action_taken_by,
            'action_taken_at' => $action_taken_at,
            'created_at' => $created_at,
            'updated_at' => $this->faker->dateTimeBetween($created_at, 'now'),
        ];
    }
    
    /**
     * Get a realistic service request message based on the service type
     */
    protected function getServiceRequestMessage($service_type_id)
    {
        if (isset($this->serviceRequestMessages[$service_type_id])) {
            return $this->faker->randomElement($this->serviceRequestMessages[$service_type_id]);
        }
        
        // Fallback to generic message if type doesn't match
        return "Requesting assistance with care services. Please contact to discuss details.";
    }
    
    public function asNew(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'new',
                'read_status' => false,
                'read_at' => null,
                'action_type' => null,
                'action_taken_by' => null,
                'action_taken_at' => null,
                'created_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
                'service_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            ];
        });
    }

    public function approved(): Factory
    {
        $faker = $this->faker; // Capture faker
        
        return $this->state(function (array $attributes) use ($faker) {
             $created_at = $faker->dateTimeBetween('-3 months', '-1 week');
            // Convert DateTime to Carbon to use copy() method
            $carbonCreatedAt = \Carbon\Carbon::instance($created_at);
            
            $read_at = $faker->dateTimeBetween($created_at, $carbonCreatedAt->copy()->addDays(2));
            $carbonReadAt = \Carbon\Carbon::instance($read_at);
            
            $action_taken_at = $faker->dateTimeBetween($read_at, $carbonReadAt->copy()->addDays(1));
            
            return [
                'status' => 'approved',
                'read_status' => true,
                'read_at' => $read_at,
                'action_type' => 'approved',
                'action_taken_by' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id,
                'action_taken_at' => $action_taken_at,
                'created_at' => $created_at,
                'updated_at' => $action_taken_at
            ];
        });
    }

    public function rejected(): Factory
    {
        $faker = $this->faker;
        
        return $this->state(function (array $attributes) use ($faker) {
            $created_at = $faker->dateTimeBetween('-3 months', '-1 week');
            // Convert DateTime to Carbon to use copy() method
            $carbonCreatedAt = \Carbon\Carbon::instance($created_at);
            
            $read_at = $faker->dateTimeBetween($created_at, $carbonCreatedAt->copy()->addDays(2));
            $carbonReadAt = \Carbon\Carbon::instance($read_at);
            
            $action_taken_at = $faker->dateTimeBetween($read_at, $carbonReadAt->copy()->addDays(1));
                
            return [
                'status' => 'rejected',
                'read_status' => true,
                'read_at' => $read_at,
                'action_type' => 'rejected',
                'action_taken_by' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id,
                'action_taken_at' => $action_taken_at,
                'created_at' => $created_at,
                'updated_at' => $action_taken_at
            ];
        });
    }

    public function completed(): Factory
    {
        $faker = $this->faker;
        
        return $this->state(function (array $attributes) use ($faker) {
            $created_at = $faker->dateTimeBetween('-3 months', '-2 weeks');
            // Convert DateTime to Carbon to use copy() method
            $carbonCreatedAt = \Carbon\Carbon::instance($created_at);
            
            $read_at = $faker->dateTimeBetween($created_at, $carbonCreatedAt->copy()->addDays(2));
            $carbonReadAt = \Carbon\Carbon::instance($read_at);
            
            $action_taken_at = $faker->dateTimeBetween($read_at, $carbonReadAt->copy()->addDays(1));
            $service_date = $faker->dateTimeBetween('-2 weeks', '-2 days')->format('Y-m-d');
            
            return [
                'status' => 'completed',
                'read_status' => true,
                'read_at' => $read_at,
                'action_type' => 'completed',
                'action_taken_by' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id,
                'action_taken_at' => $action_taken_at,
                'service_date' => $service_date, 
                'created_at' => $created_at,
                'updated_at' => $action_taken_at
            ];
        });
    }
}