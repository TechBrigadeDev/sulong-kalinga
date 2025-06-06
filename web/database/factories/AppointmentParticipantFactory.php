<?php

namespace Database\Factories;

use App\Models\AppointmentParticipant;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentParticipantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AppointmentParticipant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $participantType = $this->faker->randomElement(['cose_user', 'beneficiary', 'family_member']);
        $participantId = null;
        
        switch($participantType) {
            case 'cose_user':
                $participantId = User::inRandomOrder()->first()->id ?? 1;
                break;
            case 'beneficiary':
                $participantId = Beneficiary::inRandomOrder()->first()->beneficiary_id ?? 1;
                break;
            case 'family_member':
                $participantId = FamilyMember::inRandomOrder()->first()->family_member_id ?? 1;
                break;
        }

        return [
            'appointment_id' => Appointment::factory(),
            'participant_type' => $participantType,
            'participant_id' => $participantId,
            'is_organizer' => $this->faker->boolean(20) // 20% chance of being organizer
        ];
    }

    /**
     * Indicate that the participant is a staff member.
     */
    public function staff()
    {
        return $this->state(function (array $attributes) {
            return [
                'participant_type' => 'cose_user',
                'participant_id' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id ?? 1
            ];
        });
    }

    /**
     * Indicate that the participant is an organizer.
     */
    public function organizer()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_organizer' => true
            ];
        });
    }
}