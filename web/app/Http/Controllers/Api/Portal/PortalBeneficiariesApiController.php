<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\FamilyMember;

class PortalBeneficiariesApiController extends Controller
{
    /**
     * Get the beneficiary profile for the authenticated beneficiary or related beneficiary.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $beneficiary
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