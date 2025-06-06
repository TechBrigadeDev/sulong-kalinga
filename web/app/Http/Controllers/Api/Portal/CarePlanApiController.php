<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\GeneralCarePlan;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;

class CarePlanApiController extends Controller
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
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

        $carePlans = GeneralCarePlan::with(['careNeeds', 'medications', 'healthHistory'])
            ->where('beneficiary_id', $beneficiary->beneficiary_id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $carePlans
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

        $totalPlans = GeneralCarePlan::where('beneficiary_id', $beneficiary->beneficiary_id)->count();
        $latestPlan = GeneralCarePlan::where('beneficiary_id', $beneficiary->beneficiary_id)
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

        $carePlan = GeneralCarePlan::with(['careNeeds', 'medications', 'healthHistory'])
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

        $carePlan = GeneralCarePlan::where('beneficiary_id', $beneficiary->beneficiary_id)->find($id);

        if (!$carePlan) {
            return response()->json(['success' => false, 'message' => 'Care plan not found.'], 404);
        }

        // Mark as acknowledged (example: set a flag or create a record)
        $carePlan->acknowledged_at = now();
        $carePlan->save();

        // Log the action
        $this->logService->createLog(
            $user->id,
            'care_plan_acknowledged',
            "Care plan ID {$carePlan->general_care_plan_id} acknowledged by user {$user->id}"
        );

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
