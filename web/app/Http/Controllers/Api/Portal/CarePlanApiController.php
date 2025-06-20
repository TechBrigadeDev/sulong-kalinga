<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\WeeklyCarePlan;
use App\Services\LogService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class CarePlanApiController extends Controller
{
    protected $logService;
    protected $notificationService;

    public function __construct(LogService $logService, NotificationService $notificationService)
    {
        $this->logService = $logService;
        $this->notificationService = $notificationService;
    }

    /**
     * List care plans for the authenticated beneficiary or related beneficiary.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        $carePlans = WeeklyCarePlan::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->orderByDesc('date')
            ->get();

        $result = $carePlans->map(function ($plan) use ($user) {
            // Get the care worker (author) full name from cose_users table
            $author = \App\Models\User::find($plan->created_by);
            $authorName = $author ? trim($author->first_name . ' ' . $author->last_name) : null;

            // Determine status
            $status = ($plan->acknowledged_by_beneficiary || $plan->acknowledged_by_family)
                ? 'Acknowledged'
                : 'Pending Review';

            return [
                'id' => $plan->weekly_care_plan_id,
                'author_name' => $authorName,
                'acknowledged' => $user->role_id == 4
                    ? $plan->acknowledged_by_beneficiary
                    : ($user->role_id == 5 ? $plan->acknowledged_by_family : null),
                'date' => $plan->date,
                'status' => $status,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Get care plan statistics for the authenticated beneficiary or related beneficiary.
     */
    public function statistics(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        $totalPlans = WeeklyCarePlan::where('beneficiary_id', $beneficiary->beneficiary_id)->count();
        $latestPlan = WeeklyCarePlan::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->orderByDesc('created_at')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'total_plans' => $totalPlans,
                'latest_plan_date' => $latestPlan ? $latestPlan->created_at->toDateString() : null,
            ]
        ]);
    }

    /**
     * View a specific care plan.
     */
    public function view(Request $request, $id)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        // Eager load all necessary relations
        $carePlan = \App\Models\WeeklyCarePlan::with([
            'beneficiary.barangay',
            'beneficiary.municipality',
            'beneficiary.generalCarePlan.healthHistory',
            'vitalSigns',
            'interventions',
            'author',
            'careWorker'
        ])
        ->where('beneficiary_id', $beneficiary->beneficiary_id)
        ->find($id);

        if (!$carePlan) {
            return response()->json(['success' => false, 'message' => 'Care plan not found.'], 404);
        }

        // Format beneficiary details
        $b = $carePlan->beneficiary;
        $beneficiaryFullName = $b
            ? trim("{$b->first_name} " . ($b->middle_name ? "{$b->middle_name} " : "") . "{$b->last_name}")
            : null;

        $addressParts = [];
        if ($b && $b->street_address) $addressParts[] = $b->street_address;
        if ($b && $b->barangay) $addressParts[] = $b->barangay->barangay_name;
        if ($b && $b->municipality) $addressParts[] = $b->municipality->municipality_name;
        $address = $b ? implode(', ', $addressParts) : null;

        // Medical Conditions
        $medicalConditions = null;
        if (
            $b &&
            $b->generalCarePlan &&
            $b->generalCarePlan->healthHistory &&
            $b->generalCarePlan->healthHistory->medical_conditions
        ) {
            $conditions = json_decode($b->generalCarePlan->healthHistory->medical_conditions, true);
            $medicalConditions = is_array($conditions) ? implode(', ', $conditions) : $b->generalCarePlan->healthHistory->medical_conditions;
        }

        // Illnesses
        $illnesses = $b && $b->illnesses ? $b->illnesses : null;

        // Civil Status
        $civilStatus = $b && $b->civil_status ? $b->civil_status : null;

        // Care worker (author)
        $careWorker = $carePlan->author ?: $carePlan->careWorker;
        $careWorkerFullName = $careWorker
            ? trim("{$careWorker->first_name} {$careWorker->last_name}")
            : null;

        // --- Acknowledgement logic ---
        $acknowledgeStatus = 'Not Acknowledged';
        $whoAcknowledged = null;

        if ($carePlan->acknowledged_by_beneficiary) {
            $ackBeneficiary = \App\Models\Beneficiary::find($carePlan->acknowledged_by_beneficiary);
            if ($ackBeneficiary) {
                $middle = $ackBeneficiary->middle_name ? $ackBeneficiary->middle_name : '';
                $whoAcknowledged = trim("{$ackBeneficiary->first_name} {$middle} {$ackBeneficiary->last_name}");
                $acknowledgeStatus = 'Acknowledged';
            }
        } elseif ($carePlan->acknowledged_by_family) {
            $ackFamily = \App\Models\FamilyMember::find($carePlan->acknowledged_by_family);
            if ($ackFamily) {
                $whoAcknowledged = trim("{$ackFamily->first_name} {$ackFamily->last_name}");
                $acknowledgeStatus = 'Acknowledged';
            }
        }

        // Photo URL (if you have an upload service, use it; otherwise, just use the path)
        $photoUrl = null;
        if (isset($this->uploadService) && $carePlan->photo_path) {
            $photoUrl = $this->uploadService->getTemporaryPrivateUrl($carePlan->photo_path, 30);
        } elseif ($carePlan->photo_path) {
            $photoUrl = $carePlan->photo_path;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $carePlan->weekly_care_plan_id,
                'date' => $carePlan->date,
                'beneficiary' => [
                    'full_name' => $beneficiaryFullName,
                    'address' => $address,
                    'medical_conditions' => $medicalConditions,
                    'illnesses' => $illnesses,
                    'civil_status' => $civilStatus,
                ],
                'care_worker' => $careWorkerFullName,
                'assessment' => $carePlan->assessment,
                'evaluation_recommendations' => $carePlan->evaluation_recommendations,
                'illnesses' => $carePlan->illnesses ? json_decode($carePlan->illnesses) : [],
                'vital_signs' => $carePlan->vitalSigns,
                'interventions' => $carePlan->interventions,
                'photo_url' => $photoUrl,
                'created_at' => $carePlan->created_at,
                'updated_at' => $carePlan->updated_at,
                'acknowledge_status' => $acknowledgeStatus,
                'who_acknowledged' => $whoAcknowledged,
            ]
        ]);
    }

    /**
     * Acknowledge a care plan.
     */
    public function acknowledge(Request $request, $id)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        $carePlan = WeeklyCarePlan::where('beneficiary_id', $beneficiary->beneficiary_id)->find($id);

        if (!$carePlan) {
            return response()->json(['success' => false, 'message' => 'Care plan not found.'], 404);
        }

        // If already acknowledged by anyone, block further acknowledgments
        if ($carePlan->acknowledged_by_beneficiary || $carePlan->acknowledged_by_family) {
            return response()->json([
                'success' => false,
                'message' => 'This care plan has already been acknowledged.'
            ], 409);
        }

        // Mark as acknowledged by the correct user type, storing the ID of the acknowledger
        $acknowledgerType = null;
        if ($user->role_id == 4) {
            $carePlan->acknowledged_by_beneficiary = $user->beneficiary_id;
            $acknowledgerType = 'beneficiary';
        } elseif ($user->role_id == 5) {
            $carePlan->acknowledged_by_family = $user->family_member_id;
            $acknowledgerType = 'family_member';
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $carePlan->save();

        // Log the action
        $this->logService->createLog(
            'WeeklyCarePlan',
            $carePlan->weekly_care_plan_id,
            'care_plan_acknowledged',
            "Care plan for {$beneficiary->first_name} {$beneficiary->last_name} acknowledged by user {$user->id}",
            $user->id
        );

        // Prepare names for notifications
        $beneficiaryName = trim($beneficiary->first_name . ' ' . $beneficiary->last_name);

        // Notify the care worker (author) if available
        if ($carePlan->created_by) {
            $this->notificationService->notifyStaff(
                $carePlan->created_by,
                'Care Plan Acknowledged',
                "Your weekly care plan for {$beneficiaryName} has been acknowledged."
            );
        }

        // Notify the other party (beneficiary or family members)
        if ($acknowledgerType === 'beneficiary') {
            // Notify all family members except the one who did the action (if any)
            if ($beneficiary->familyMembers) {
                foreach ($beneficiary->familyMembers as $familyMember) {
                    $this->notificationService->notifyFamilyMember(
                        $familyMember->family_member_id,
                        'Care Plan Acknowledged',
                        "The weekly care plan for {$beneficiaryName} has been acknowledged by the beneficiary."
                    );
                }
            }
        } elseif ($acknowledgerType === 'family_member') {
            // Notify the beneficiary
            $this->notificationService->notifyBeneficiary(
                $beneficiary->beneficiary_id,
                'Care Plan Acknowledged',
                "The weekly care plan for you has been acknowledged by a family member."
            );
            // Notify other family members except the one who did the action
            if ($beneficiary->familyMembers) {
                foreach ($beneficiary->familyMembers as $familyMember) {
                    if ($familyMember->family_member_id != $user->family_member_id) {
                        $this->notificationService->notifyFamilyMember(
                            $familyMember->family_member_id,
                            'Care Plan Acknowledged',
                            "The weekly care plan for {$beneficiaryName} has been acknowledged by a family member."
                        );
                    }
                }
            }
        }

        // Notify the actor with a unique message
        if ($acknowledgerType === 'beneficiary') {
            $this->notificationService->notifyBeneficiary(
                $user->beneficiary_id,
                'Care Plan Acknowledged',
                'Your acknowledgment of the weekly care plan was successful.'
            );
        } elseif ($acknowledgerType === 'family_member') {
            $this->notificationService->notifyFamilyMember(
                $user->family_member_id,
                'Care Plan Acknowledged',
                'Your acknowledgment of the weekly care plan was successful.'
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Care plan acknowledged.'
        ]);
    }

    /**
     * Helper to get the beneficiary for the current user (beneficiary or family member).
     */
    protected function getCurrentBeneficiary($user)
    {
        if ($user->role_id == 4) {
            return Beneficiary::find($user->beneficiary_id);
        } elseif ($user->role_id == 5) {
            $familyMember = FamilyMember::find($user->family_member_id);
            return $familyMember ? Beneficiary::find($familyMember->related_beneficiary_id) : null;
        }
        return null;
    }
}
