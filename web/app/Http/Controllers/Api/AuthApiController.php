<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthApiController extends Controller
{
    /**
     * Authenticate user and return token
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string', // Now using username for login
            'password' => 'required|string',
        ]);

        // Find user by username (for beneficiary/family_member) or email (for cose users)
        $user = User::where(function ($q) use ($request) {
                $q->where('username', $request->username)
                  ->orWhere('email', $request->username);
            })->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Get photo path from the specific table
        $photo = null;
        if ($user->user_type === 'beneficiary' && $user->beneficiary_id) {
            $beneficiary = \App\Models\Beneficiary::find($user->beneficiary_id);
            $photo = $beneficiary?->photo;
        } elseif ($user->user_type === 'family_member' && $user->family_member_id) {
            $familyMember = \App\Models\FamilyMember::find($user->family_member_id);
            $photo = $familyMember?->photo;
        } elseif ($user->user_type === 'cose_user' && $user->cose_user_id) {
            $coseUser = \App\Models\CoseUser::find($user->cose_user_id);
            $photo = $coseUser?->photo;
        }
        $photo_url = $photo ? Storage::disk('spaces-private')->temporaryUrl($photo, now()->addMinutes(30)) : null;

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'role_id' => $user->role_id,
                'user_type' => $user->user_type,
                'status' => $user->status ?? null,
                'photo_url' => $photo_url,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }
    
    /**
     * Get authenticated user details
     */
    public function user(Request $request)
    {
        $user = $request->user();

        // Map role_id to role name (1-5, no skip)
        $roleNames = [
            1 => 'admin',
            2 => 'care_manager',
            3 => 'care_worker',
            4 => 'beneficiary',
            5 => 'family_member',
        ];
        $role = $roleNames[$user->role_id] ?? 'unknown';

        // Get photo path from the specific table
        $photo = null;
        if ($user->user_type === 'beneficiary' && $user->beneficiary_id) {
            $beneficiary = \App\Models\Beneficiary::find($user->beneficiary_id);
            $photo = $beneficiary?->photo;
        } elseif ($user->user_type === 'family_member' && $user->family_member_id) {
            $familyMember = \App\Models\FamilyMember::find($user->family_member_id);
            $photo = $familyMember?->photo;
        } elseif ($user->user_type === 'cose_user' && $user->cose_user_id) {
            $coseUser = \App\Models\CoseUser::find($user->cose_user_id);
            $photo = $coseUser?->photo;
        }
        $photo_url = $photo ? Storage::disk('spaces-private')->temporaryUrl($photo, now()->addMinutes(30)) : null;

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $role,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'status' => $user->status ?? null,
                'photo_url' => $photo_url,
            ]
        ]);
    }
}
