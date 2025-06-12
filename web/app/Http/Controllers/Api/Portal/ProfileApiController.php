<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Services\NotificationService;

class ProfileApiController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Show the authenticated user's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        // Get beneficiary or family member details
        if ($user->role_id == 4) {
            $profile = Beneficiary::find($user->beneficiary_id);
        } elseif ($user->role_id == 5) {
            $profile = FamilyMember::find($user->family_member_id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if (!$profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $profile
        ]);
    }

    /**
     * Update the authenticated family member's email.
     */
    public function updateEmail(Request $request)
    {
        $user = $request->user();

        if ($user->role_id != 5) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'email' => 'required|email|unique:family_members,email,' . $user->family_member_id . ',family_member_id',
            'current_password' => 'required',
        ]);

        if (!\Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 422);
        }

        $familyMember = FamilyMember::find($user->family_member_id);
        if (!$familyMember) {
            return response()->json(['success' => false, 'message' => 'Family member not found.'], 404);
        }
        $familyMember->email = $request->email;
        $familyMember->save();

        // Notify the actor
        $this->notificationService->notifyFamilyMember(
            $user->family_member_id,
            'Email Updated',
            'Your email was updated successfully.'
        );

        return response()->json([
            'success' => true,
            'message' => 'Email updated successfully.'
        ]);
    }

    /**
     * Update the authenticated beneficiary's username. NOT ALLOWED, DO NOT IMPLEMENT
     */
    // public function updateUsername(Request $request)
    // {
    //     $user = $request->user();

    //     if ($user->role_id != 4) {
    //         return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
    //     }

    //     $request->validate([
    //         'username' => 'required|string|unique:beneficiaries,username,' . $user->beneficiary_id . ',beneficiary_id',
    //         'current_password' => 'required',
    //     ]);

    //     if (!\Hash::check($request->current_password, $user->password)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Current password is incorrect.'
    //         ], 422);
    //     }

    //     $beneficiary = Beneficiary::find($user->beneficiary_id);
    //     if (!$beneficiary) {
    //         return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
    //     }
    //     $beneficiary->username = $request->username;
    //     $beneficiary->save();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Username updated successfully.'
    //     ]);
    // }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::defaults()],
            'new_password_confirmation' => 'required',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 422);
        }

        $hashed = Hash::make($request->new_password);

        if ($user->role_id == 4) {
            // Beneficiary
            $beneficiary = Beneficiary::find($user->beneficiary_id);
            if (!$beneficiary) {
                return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
            }
            $beneficiary->password = $hashed;
            $beneficiary->save();

            // Notify the actor
            $this->notificationService->notifyBeneficiary(
                $user->beneficiary_id,
                'Password Updated',
                'Your password was updated successfully.'
            );
        } elseif ($user->role_id == 5) {
            // Family Member
            $familyMember = FamilyMember::find($user->family_member_id);
            if (!$familyMember) {
                return response()->json(['success' => false, 'message' => 'Family member not found.'], 404);
            }
            $familyMember->password = $hashed;
            $familyMember->save();

            // Notify the actor
            $this->notificationService->notifyFamilyMember(
                $user->family_member_id,
                'Password Updated',
                'Your password was updated successfully.'
            );
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.'
        ]);
    }
}
