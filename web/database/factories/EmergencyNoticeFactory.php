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
            'in_progress' => 30,
            'resolved' => 40, 
            'archived' => 10
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
            'emergency_type_id' => EmergencyType::inRandomOrder()->first()->emergency_type_id,
            'message' => $this->faker->realText(150),
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
    
    public function new(): Factory
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