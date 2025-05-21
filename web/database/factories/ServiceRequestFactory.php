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
            'service_type_id' => ServiceRequestType::inRandomOrder()->first()->service_type_id,
            'care_worker_id' => $care_worker_id,
            'service_date' => $service_date,
            'service_time' => $service_time,
            'message' => $this->faker->paragraph(),
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
}