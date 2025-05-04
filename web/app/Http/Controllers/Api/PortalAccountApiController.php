<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PortalAccountApiController extends Controller
{
    // Store the selected user type and id in the session
    public function selectPortalUser(Request $request)
    {
        $request->validate([
            'type' => 'required|in:beneficiary,family_member',
            'id' => 'required|integer',
        ]);

        // Store in session (for now; you can later use token claims if needed)
        session([
            'portal_user_type' => $request->type,
            'portal_user_id' => $request->id,
        ]);

        return response()->json(['success' => true]);
    }

    public function getPortalAccountUsers($portalAccountId)
    {
        $beneficiary = \App\Models\Beneficiary::where('portal_account_id', $portalAccountId)->first();
        $familyMembers = \App\Models\FamilyMember::where('portal_account_id', $portalAccountId)->get();

        return response()->json([
            'beneficiary' => $beneficiary,
            'family_members' => $familyMembers,
        ]);
    }
}
