<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FamilyMember;
use App\Models\Beneficiary;
use App\Services\UploadService;

class RelativesApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Beneficiary: return related family members
        if ($user->role_id == 4) {
            $familyMembers = FamilyMember::where('related_beneficiary_id', $user->beneficiary_id)
                ->get()
                ->map(function ($fm) {
                    return [
                        'id' => $fm->family_member_id,
                        'first_name' => $fm->first_name,
                        'last_name' => $fm->last_name,
                        'mobile' => $fm->mobile,
                        'email' => $fm->email,
                        'landline' => $fm->landline,
                        'street_address' => $fm->street_address,
                        'photo' => $fm->photo,
                        'photo_url' => $fm->photo
                            ? app(UploadService::class)->getTemporaryPrivateUrl($fm->photo, 30)
                            : null,
                    ];
                })
                ->values();

            return response()->json([
                'type' => 'beneficiary',
                'family_members' => $familyMembers,
            ]);
        }

        // Family member: return related beneficiary
        if ($user->role_id == 5) {
            $familyMember = FamilyMember::find($user->family_member_id);
            $beneficiary = $familyMember
                ? Beneficiary::find($familyMember->related_beneficiary_id)
                : null;

            return response()->json([
                'type' => 'family_member',
                'beneficiary' => $beneficiary ? [
                    'id' => $beneficiary->beneficiary_id,
                    'first_name' => $beneficiary->first_name,
                    'last_name' => $beneficiary->last_name,
                    'mobile' => $beneficiary->mobile,
                    'landline' => $beneficiary->landline,
                    'street_address' => $beneficiary->street_address,
                    'photo' => $beneficiary->photo,
                    'photo_url' => $beneficiary->photo
                        ? app(UploadService::class)->getTemporaryPrivateUrl($beneficiary->photo, 30)
                        : null,
                ] : null,
            ]);
        }

        // Not beneficiary or family member
        return response()->json([
            'success' => false,
            'message' => 'Not allowed for this user type.'
        ], 403);
    }
}
