<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'sender_id' => User::factory(),
            'sender_type' => 'cose_staff',
            'content' => $this->faker->paragraph,
            'is_unsent' => false, // Add this line
            'message_timestamp' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    /**
     * Configure the message to be from a COSE staff member.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function fromStaff(int $userId): Factory
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'sender_id' => $userId,
                'sender_type' => 'cose_staff',
            ];
        });
    }
    
    /**
     * Configure the message to be from a beneficiary.
     *
     * @param int $beneficiaryId
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function fromBeneficiary(int $beneficiaryId): Factory
    {
        return $this->state(function (array $attributes) use ($beneficiaryId) {
            return [
                'sender_id' => $beneficiaryId,
                'sender_type' => 'beneficiary',
            ];
        });
    }
    
    /**
     * Configure the message to be from a family member.
     *
     * @param int $familyMemberId
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function fromFamilyMember(int $familyMemberId): Factory
    {
        return $this->state(function (array $attributes) use ($familyMemberId) {
            return [
                'sender_id' => $familyMemberId,
                'sender_type' => 'family_member',
            ];
        });
    }
}