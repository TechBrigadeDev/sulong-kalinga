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

        $carePlan = WeeklyCarePlan::with(['beneficiary', 'author', 'careWorker'])
            ->where('beneficiary_id', $beneficiary->beneficiary_id)
            ->find($id);

        if (!$carePlan) {
            return response()->json(['success' => false, 'message' => 'Care plan not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $carePlan
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
