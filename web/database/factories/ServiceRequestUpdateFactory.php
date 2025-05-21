<?php

namespace Database\Factories;

use App\Models\ServiceRequestUpdate;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRequestUpdateFactory extends Factory
{
    protected $model = ServiceRequestUpdate::class;

    public function definition(): array
    {
        $update_types = ['approval', 'rejection', 'assignment', 'completion', 'note'];
        $update_type = $this->faker->randomElement($update_types);

        $messages = [
            'approval' => [
                "Request approved. Service will be provided as requested.",
                "Service request has been approved. Care worker will be assigned shortly.",
                "Approved the request. Additional assistance may be provided if needed.",
                "Service approved based on beneficiary's care plan and current needs."
            ],
            'rejection' => [
                "Request declined due to scheduling conflicts. Please resubmit for a different date.",
                "Unable to approve service request at this time due to staff limitations.",
                "Service request not approved. Alternative solution recommended below.",
                "Request cannot be fulfilled as specified. Please see notes for recommendations."
            ],
            'assignment' => [
                "Assigned to Care Worker Maria Santos for service delivery.",
                "Care Worker Juan Dela Cruz will handle this request on the scheduled date.",
                "Request assigned to the district care team and scheduled for completion.",
                "Care Worker Ana Reyes has been assigned and will contact the beneficiary to confirm details."
            ],
            'completion' => [
                "Service has been successfully completed. Beneficiary reported satisfaction.",
                "Request fulfilled as scheduled. No follow-up required at this time.",
                "Service completed with additional notes for future reference.",
                "Completed the requested service. Care worker recommends follow-up in 2 weeks."
            ],
            'note' => [
                "Called beneficiary to confirm service details.",
                "Rescheduled to a more convenient time upon beneficiary's request.",
                "Family member will be present during service delivery.",
                "Special equipment required for this service has been arranged."
            ]
        ];

        $status_change_to = null;
        if ($update_type === 'approval') {
            $status_change_to = 'approved';
        } elseif ($update_type === 'rejection') {
            $status_change_to = 'rejected';
        } elseif ($update_type === 'completion') {
            $status_change_to = 'completed';
        }

        return [
            'service_request_id' => ServiceRequest::inRandomOrder()->first()->service_request_id,
            'message' => $this->faker->randomElement($messages[$update_type]),
            'update_type' => $update_type,
            'status_change_to' => $status_change_to,
            'updated_by' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id,
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }
}