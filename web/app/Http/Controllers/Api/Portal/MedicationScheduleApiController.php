<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\MedicationSchedule;

class MedicationScheduleApiController extends Controller
{
    /**
     * List medication schedules for the authenticated beneficiary or related beneficiary.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        $medications = MedicationSchedule::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $medications
        ]);
    }

    /**
     * Get the next scheduled medication for the authenticated beneficiary or related beneficiary.
     */
    public function next(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        $now = now();
        $nextMedication = MedicationSchedule::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where(function ($query) use ($now) {
                $query->where('start_date', '<=', $now)
                      ->where(function ($q) use ($now) {
                          $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
                      });
            })
            ->orderBy('start_date')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $nextMedication
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
