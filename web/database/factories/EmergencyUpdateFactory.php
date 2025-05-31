<?php

namespace Database\Factories;

use App\Models\EmergencyUpdate;
use App\Models\EmergencyNotice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmergencyUpdateFactory extends Factory
{
    protected $model = EmergencyUpdate::class;

    // Type-specific emergency update messages
    protected $emergencyUpdateMessages = [
        'response' => [
            // Medical Emergency
            1 => [
                "Emergency response team dispatched to location. ETA 15 minutes.",
                "Nurse has been contacted and is providing guidance over the phone while team is en route.",
                "Instructed family to place beneficiary in recovery position while waiting for medical assistance.",
                "Contacted local health center for immediate medical support. Dr. Santos is on call.",
                "Ambulance has been dispatched. Please ensure clear access to the home."
            ],
            // Fall Incident
            2 => [
                "Care worker en route to assess the fall. Do not move the beneficiary until assessment.",
                "Asked family to check for bleeding or obvious injuries without moving beneficiary.",
                "Instructed to apply cold compress to visible bruising. Care worker will arrive shortly.",
                "Urgent response team dispatched with mobility equipment for safe transfer.",
                "Sent care worker with first aid kit to assess and treat minor injuries."
            ],
            // Medication Issue
            3 => [
                "Contacting beneficiary's physician for guidance on medication issue.",
                "Nurse will call within 15 minutes to provide instructions for medication error.",
                "Care worker en route with medication chart to verify current prescriptions.",
                "On-call doctor has been notified about adverse reaction. Advising monitoring vital signs.",
                "Contacted pharmacy to verify medication instructions. Will call back shortly."
            ],
            // Mental Health Crisis  
            4 => [
                "Mental health crisis team has been notified and is responding to the situation.",
                "Care worker with mental health training dispatched to assess the situation.",
                "Providing family with de-escalation techniques via phone while team is en route.",
                "Emergency psychiatric consultation requested. Please keep environment calm and quiet.",
                "Crisis intervention specialist responding. Advised to remove potential hazards from area."
            ],
            // Other Emergency
            5 => [
                "Technical team notified about power outage affecting medical equipment.",
                "Local emergency services informed about the situation. Help is on the way.",
                "Care coordinator dispatched to address immediate needs and arrange temporary shelter.",
                "Emergency food package being prepared for delivery within the hour.",
                "Temporary caregiver has been assigned and is en route to the location."
            ]
        ],
        'status_change' => [
            "Status updated to In Progress. Response team has been mobilized.",
            "Escalating to high priority based on assessment. Additional resources assigned.",
            "Changing status to urgent priority. Supervisor has been notified.",
            "De-escalating to routine follow-up based on initial assessment by first responders.",
            "Status updated to requiring specialized response. Coordinating with external agencies."
        ],
        'assignment' => [
            "Assigned to Nurse Maria Santos for immediate response and assessment.",
            "Case transferred to emergency response team with medical equipment.",
            "Assigned to Care Worker Juan Cruz who is familiar with this beneficiary.",
            "Reassigned to on-call medical team for specialized intervention.",
            "Case assigned to Care Manager Ana Reyes for coordination of multiple resources."
        ],
        'resolution' => [
            "Emergency resolved. Beneficiary has been stabilized and is comfortable.",
            "Medical issue addressed successfully. Follow-up visit scheduled for tomorrow.",
            "Situation resolved with assistance from family members and care worker.",
            "Emergency addressed. Beneficiary now stable with vital signs in normal range.",
            "Resolution complete. Care plan updated to prevent similar emergencies."
        ],
        'note' => [
            "Family members have been notified about the situation.",
            "Beneficiary's regular doctor has been updated about the emergency.",
            "Temperature and blood pressure readings now within normal range.",
            "Medications verified and organized to prevent future confusion.",
            "Home environment assessed for safety hazards during response."
        ]
    ];

    public function definition(): array
    {
        $update_types = ['response', 'status_change', 'assignment', 'resolution', 'note'];
        $update_type = $this->faker->randomElement($update_types);

        // Find an emergency notice and get its type for context-specific messages
        $notice = EmergencyNotice::with('emergencyType')->inRandomOrder()->first();
        $emergency_type_id = $notice ? $notice->emergency_type_id : 1;

        // Get appropriate message based on update type and emergency type
        $message = $this->getUpdateMessage($update_type, $emergency_type_id);

        $status_change_to = null;
        if ($update_type === 'status_change') {
            $status_change_to = $this->faker->randomElement(['new', 'in_progress', 'resolved', 'archived']);
        }

        return [
            'notice_id' => $notice->notice_id,
            'message' => $message,
            'update_type' => $update_type,
            'status_change_to' => $status_change_to,
            'updated_by' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id,
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }

    /**
     * Get an appropriate update message based on type and emergency context
     */
    protected function getUpdateMessage($update_type, $emergency_type_id)
    {
        if ($update_type === 'response' && isset($this->emergencyUpdateMessages[$update_type][$emergency_type_id])) {
            // For response type, use emergency-type specific messages
            return $this->faker->randomElement($this->emergencyUpdateMessages[$update_type][$emergency_type_id]);
        } elseif (isset($this->emergencyUpdateMessages[$update_type])) {
            // For other types, use general messages for that update type
            return $this->faker->randomElement($this->emergencyUpdateMessages[$update_type]);
        }
        
        // Fallback
        return "Update on emergency situation: staff has been notified and response is in progress.";
    }
}