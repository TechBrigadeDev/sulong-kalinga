<?php
// database/factories/AppointmentParticipantFactory.php
namespace Database\Factories;

use App\Models\AppointmentParticipant;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentParticipantFactory extends Factory
{
    protected $model = AppointmentParticipant::class;

    public function definition()
    {
        $participantType = $this->faker->randomElement(['cose_user', 'beneficiary', 'family_member']);
        $participantId = null;
        
        switch ($participantType) {
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
            'is_organizer' => $this->faker->boolean(10), // 10% chance of being organizer
        ];
    }
}