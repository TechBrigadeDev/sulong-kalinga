<?php

namespace Database\Factories;

use App\Models\ServiceRequestUpdate;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRequestUpdateFactory extends Factory
{
    protected $model = ServiceRequestUpdate::class;

    // Type-specific service request update messages
    protected $serviceRequestUpdateMessages = [
        'approval' => [
            // Home Care Visit
            1 => [
                "Home care visit request approved. Care worker will be assigned shortly.",
                "Additional home visit approved. Will be scheduled on the requested date.",
                "Approved home care visit. Staff member with wound care training will be assigned.",
                "Request for additional home support approved based on recent assessment.",
                "Home care visit request approved and prioritized due to beneficiary's condition."
            ],
            // Transportation
            2 => [
                "Transportation request approved. Driver will arrive 45 minutes before appointment time.",
                "Approved transportation service. Vehicle with accessibility features will be provided.",
                "Transportation request approved. Confirmation of pickup time will be sent day before.",
                "Transportation assistance approved. Please have beneficiary ready 15 minutes early.",
                "Transportation service approved. Driver is familiar with beneficiary's mobility needs."
            ],
            // Medical Appointments
            3 => [
                "Medical appointment assistance approved. Care worker will help with the entire process.",
                "Approved medical appointment support. Care worker will bring necessary documents.",
                "Medical appointment assistance approved. Care worker will take notes during consultation.",
                "Request approved. Care worker will accompany beneficiary to the medical appointment.",
                "Medical appointment support approved. Transportation also arranged."
            ],
            // Meal Delivery
            4 => [
                "Meal delivery service approved for requested dates. Special dietary needs noted.",
                "Meal assistance approved. Delivery will begin tomorrow and continue as requested.",
                "Approved meal delivery service. Soft food options will be provided as requested.",
                "Meal support request approved. First delivery scheduled for tomorrow morning.",
                "Temporary meal assistance approved based on beneficiary's current situation."
            ],
            // Other Service
            5 => [
                "Service request approved. Staff with appropriate skills will be assigned.",
                "Request approved. Service will be provided on the date requested.",
                "Approved special assistance request. Coordinator will call to confirm details.",
                "Service request approved. Thank you for providing detailed information.",
                "Request approved based on care plan assessment. Will arrange appropriate staff."
            ]
        ],
        'rejection' => [
            // Home Care Visit
            1 => [
                "Unable to approve additional home visit due to staff limitations. Alternative days offered below.",
                "Home care request cannot be accommodated on requested date. Please see alternative options.",
                "Service request not approved for requested timeframe. Can offer limited support instead.",
                "Cannot provide specialized care requested. Referral to external service provided below.",
                "Unable to approve full request due to scheduling conflicts. Partial service offered."
            ],
            // Transportation
            2 => [
                "Transportation request cannot be fulfilled due to vehicle availability. Alternative suggested.",
                "Unable to provide transportation at requested time. Can offer service 2 hours earlier.",
                "Transportation request denied due to route limitations. Suggesting alternative solution.",
                "Cannot accommodate transportation request. Public transportation voucher offered instead.",
                "Unable to approve transportation to requested location outside service area."
            ],
            // Medical Appointments
            3 => [
                "Medical appointment assistance request denied due to staff shortage on that date.",
                "Unable to provide full support for medical appointment. Limited assistance offered.",
                "Cannot accompany to medical appointment at requested facility outside service area.",
                "Medical appointment support request denied. Family member assistance recommended.",
                "Request not approved for specified date. Alternative care worker scheduling offered."
            ],
            // Meal Delivery
            4 => [
                "Meal delivery service request denied. Service at capacity for requested dates.",
                "Unable to meet specialized dietary requirements requested. Alternative suggested.",
                "Cannot provide meal delivery for full requested duration. Shortened period offered.",
                "Meal delivery request not approved due to delivery route limitations.",
                "Unable to accommodate meal service request. Community meal program referral provided."
            ],
            // Other Service
            5 => [
                "Service request cannot be fulfilled as specified. Alternative solution suggested.",
                "Unable to approve request that falls outside our service parameters.",
                "Request denied as it requires specialized training our current staff does not possess.",
                "Cannot approve service request due to resource limitations. Partial solution offered.",
                "Service request not approved. Please see notes for recommendations and alternatives."
            ]
        ],
        'assignment' => [
            // Home Care Visit
            1 => [
                "Assigned to Care Worker Maria Santos who specializes in personal care assistance.",
                "Home care visit assigned to Juan Cruz who will contact you to confirm details.",
                "Request assigned to Care Worker Ana Reyes who has previously worked with this beneficiary.",
                "Home visit assigned to Pedro Bautista who has wound care certification.",
                "Assigned to emergency response team with scheduled arrival within 24 hours."
            ],
            // Transportation
            2 => [
                "Transportation assigned to driver Carlos Mendoza with accessible vehicle.",
                "Request assigned to transportation coordinator Gloria Torres for scheduling.",
                "Transportation service assigned to driver Manuel Garcia who knows the location well.",
                "Assigned to transportation team with medical equipment handling experience.",
                "Transportation request assigned to Rafael Santos who specializes in mobility assistance."
            ],
            // Medical Appointments
            3 => [
                "Medical appointment assistance assigned to Carmen Reyes with medical background.",
                "Request assigned to Jose Bautista who will handle all documentation needs.",
                "Medical support assigned to Care Worker Teresita Cruz who will take notes during visit.",
                "Assigned to medical appointment specialist with translation capabilities.",
                "Request assigned to healthcare coordinator who will contact doctor's office in advance."
            ],
            // Meal Delivery
            4 => [
                "Meal service assigned to nutrition team with knowledge of dietary restrictions.",
                "Request assigned to meal coordinator Antonio Flores for immediate implementation.",
                "Meal delivery assigned to Luz Garcia who will handle daily deliveries.",
                "Assigned to special dietary team to accommodate soft food requirement.",
                "Meal service request assigned to emergency food assistance program for rapid response."
            ],
            // Other Service
            5 => [
                "Service request assigned to technical assistance team for home modifications.",
                "Request assigned to specialist Care Worker Francisco De Leon with appropriate skills.",
                "Assigned to general assistance team for evaluation and service provision.",
                "Request assigned to special projects coordinator to handle unique requirements.",
                "Service assigned to community resources specialist to gather necessary supports."
            ]
        ],
        'completion' => [
            // Home Care Visit
            1 => [
                "Home care visit completed successfully. Beneficiary reported satisfaction with service.",
                "Additional home support provided as requested. All care tasks completed.",
                "Home visit service completed. Care worker reports beneficiary is doing well.",
                "Completed personal care assistance and household safety check during visit.",
                "Home care service provided with additional wound care as needed."
            ],
            // Transportation
            2 => [
                "Transportation service completed. Beneficiary safely returned home after appointment.",
                "Transportation assistance provided as scheduled. No issues reported.",
                "Completed transportation service with additional mobility support provided.",
                "Transportation to medical facility completed successfully and on time.",
                "Transportation service fulfilled with door-to-door assistance."
            ],
            // Medical Appointments
            3 => [
                "Medical appointment assistance completed. Notes from doctor attached to care plan.",
                "Accompanied beneficiary to appointment. New medication instructions documented.",
                "Medical support service completed. Follow-up appointment scheduled for next month.",
                "Completed appointment assistance. Doctor recommended physical therapy referral.",
                "Medical appointment support provided. Beneficiary understood all instructions."
            ],
            // Meal Delivery
            4 => [
                "Meal delivery service completed for requested period. Beneficiary satisfied with meals.",
                "Completed meal assistance service. All dietary requirements were met.",
                "Meal delivery completed with additional snack options as requested.",
                "Temporary meal service provided. Regular family support has now resumed.",
                "Completed meal delivery program. Beneficiary now able to prepare simple meals."
            ],
            // Other Service
            5 => [
                "Service request completed. Home environment improvements implemented as requested.",
                "Completed assistance with documentation and communication needs.",
                "Service fulfilled successfully. Equipment set up and beneficiary trained on use.",
                "Completed requested modifications to improve accessibility in the home.",
                "Service provided as requested. Beneficiary now able to manage independently."
            ]
        ],
        'note' => [
            "Contacted beneficiary to confirm service details and specific needs.",
            "Rescheduled to a more convenient time based on beneficiary's request.",
            "Family member will be present during service delivery for additional support.",
            "Special equipment required for this service has been arranged and confirmed.",
            "Beneficiary has additional requests that will be addressed during service.",
            "Additional training completed to meet specific needs of this service request.",
            "Coordinated with healthcare provider for complete information before service.",
            "Weather conditions may affect timing of service delivery. Will update if delayed.",
            "Beneficiary's condition has changed slightly since request. Will adjust service accordingly.",
            "Previous service notes reviewed to ensure continuity of care for this request."
        ]
    ];

    public function definition(): array
    {
        $update_types = ['approval', 'rejection', 'assignment', 'completion', 'note'];
        $update_type = $this->faker->randomElement($update_types);

        // Find a service request and get its type for context-specific messages
        $serviceRequest = ServiceRequest::with('serviceType')->inRandomOrder()->first();
        $service_type_id = $serviceRequest ? $serviceRequest->service_type_id : 1;

        // Get appropriate message based on update type and service type
        $message = $this->getUpdateMessage($update_type, $service_type_id);

        $status_change_to = null;
        if ($update_type === 'approval') {
            $status_change_to = 'approved';
        } elseif ($update_type === 'rejection') {
            $status_change_to = 'rejected';
        } elseif ($update_type === 'completion') {
            $status_change_to = 'completed';
        }

        return [
            'service_request_id' => $serviceRequest->service_request_id,
            'message' => $message,
            'update_type' => $update_type,
            'status_change_to' => $status_change_to,
            'updated_by' => User::where('role_id', '<=', 3)->inRandomOrder()->first()->id,
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }

    /**
     * Get an appropriate update message based on type and service context
     */
    protected function getUpdateMessage($update_type, $service_type_id)
    {
        if ($update_type !== 'note' && isset($this->serviceRequestUpdateMessages[$update_type][$service_type_id])) {
            // For specific update types, use service-type specific messages
            return $this->faker->randomElement($this->serviceRequestUpdateMessages[$update_type][$service_type_id]);
        } elseif (isset($this->serviceRequestUpdateMessages[$update_type])) {
            // For notes or fallback, use general messages for that update type
            return $this->faker->randomElement($this->serviceRequestUpdateMessages[$update_type]);
        }
        
        // Final fallback
        return "Update on service request: staff is processing your request.";
    }
}