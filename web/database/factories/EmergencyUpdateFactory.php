<?php

namespace Database\Factories;

use App\Models\EmergencyUpdate;
use App\Models\EmergencyNotice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmergencyUpdateFactory extends Factory
{
    protected $model = EmergencyUpdate::class;

    public function definition(): array
    {
        $update_types = ['response', 'status_change', 'assignment', 'resolution', 'note'];
        $update_type = $this->faker->randomElement($update_types);

        $messages = [
            'response' => [
                "Emergency team has been dispatched and is en route to the location.",
                "Nurse has been notified and will call the beneficiary shortly.",
                "Emergency services contacted and situation assessed as non-critical.",
                "Responding to the emergency call now. Will provide updates as available."
            ],
            'status_change' => [
                "Status updated to In Progress. Care team mobilized.",
                "Escalating to high priority. Requesting additional medical support.",
                "De-escalating to routine follow-up based on initial assessment.",
                "Case has been prioritized and assigned to emergency response team."
            ],
            'assignment' => [
                "Assigning to Nurse Maria for immediate follow-up.",
                "Case transferred to Dr. Santos for specialized assessment.",
                "Reassigned to the on-call care worker for this district.",
                "Care Manager Juan will coordinate the response for this emergency."
            ],
            'resolution' => [
                "Situation resolved. Beneficiary is now stable and comfortable.",
                "Emergency addressed successfully. Follow-up visit scheduled for tomorrow.",
                "Issue has been fully resolved. Case closed with no further action needed.",
                "Resolution complete. Beneficiary has confirmed satisfaction with the response."
            ],
            'note' => [
                "Beneficiary's daughter has been notified about the situation.",
                "Medication adjustment may be needed - flagging for doctor review.",
                "Home environment assessed as safe, no additional hazards identified.",
                "Similar incident occurred last month - recommend care plan review."
            ]
        ];

        $status_change_to = null;
        if ($update_type === 'status_change') {
            $status_change_to = $this->faker->randomElement(['new', 'in_progress', 'resolved', 'archived']);
        }

        return [
            'notice_id' => EmergencyNotice::inRandomOrder()->first()->notice_id,
            'message' => $this->faker->randomElement($messages[$update_type]),
            'update_type' => $update_type,
            'status_change_to' => $status_change_to,
            'updated_by' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id,
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }
}