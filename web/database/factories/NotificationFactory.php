<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\database\factories\NotificationFactory.php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Randomly choose a user type
        $userTypes = ['beneficiary', 'family_member', 'cose_staff'];
        $userType = $this->faker->randomElement($userTypes);
        
        // Get a random ID based on the user type
        $userId = $this->getUserIdByType($userType);
        
        return [
            'user_id' => $userId,
            'user_type' => $userType,
            'message_title' => $this->faker->sentence(4),
            'message' => $this->faker->paragraph(2),
            'date_created' => Carbon::now()->subDays(rand(0, 30)),
            'is_read' => $this->faker->boolean(30), // 30% chance of being read
        ];
    }
    
    /**
     * Get a random user ID based on the user type.
     *
     * @param string $userType
     * @return int
     */
    private function getUserIdByType($userType)
    {
        switch ($userType) {
            case 'beneficiary':
                return Beneficiary::inRandomOrder()->first()->beneficiary_id ?? 1;
            case 'family_member':
                return FamilyMember::inRandomOrder()->first()->family_member_id ?? 1;
            case 'cose_staff':
                return User::inRandomOrder()->first()->id ?? 1;
            default:
                return 1;
        }
    }
    
    /**
     * State for beneficiary notifications
     */
    public function forBeneficiary($beneficiaryId = null)
    {
        return $this->state(function (array $attributes) use ($beneficiaryId) {
            $id = $beneficiaryId ?? Beneficiary::inRandomOrder()->first()->beneficiary_id ?? 1;
            
            return [
                'user_id' => $id,
                'user_type' => 'beneficiary',
            ];
        });
    }
    
    /**
     * State for family member notifications
     */
    public function forFamilyMember($familyMemberId = null)
    {
        return $this->state(function (array $attributes) use ($familyMemberId) {
            $id = $familyMemberId ?? FamilyMember::inRandomOrder()->first()->family_member_id ?? 1;
            
            return [
                'user_id' => $id,
                'user_type' => 'family_member',
            ];
        });
    }
    
    /**
     * State for cose staff notifications
     */
    public function forCoseStaff($staffId = null)
    {
        return $this->state(function (array $attributes) use ($staffId) {
            $id = $staffId ?? User::inRandomOrder()->first()->id ?? 1;
            
            return [
                'user_id' => $id,
                'user_type' => 'cose_staff',
            ];
        });
    }
}