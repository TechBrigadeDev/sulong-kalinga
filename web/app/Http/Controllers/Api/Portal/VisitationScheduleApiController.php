<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\VisitationOccurrence;

class VisitationScheduleApiController extends Controller
{
    /**
     * List visitation events for the authenticated beneficiary or related beneficiary.
     */
    public function events(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        $events = VisitationOccurrence::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Get details for a specific visitation occurrence.
     */
    public function details(Request $request, $id)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        $occurrence = VisitationOccurrence::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where('id', $id)
            ->first();

        if (!$occurrence) {
            return response()->json(['success' => false, 'message' => 'Occurrence not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $occurrence
        ]);
    }

    /**
     * List upcoming visitation occurrences for the authenticated beneficiary or related beneficiary.
     */
    public function upcoming(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        $now = now();
        $upcoming = VisitationOccurrence::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where('start_time', '>=', $now)
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $upcoming
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
