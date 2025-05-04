<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    /**
     * Authenticate user and return token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Revoke old tokens if needed
        // $user->tokens()->delete();
        
        $token = $user->createToken('mobile-app')->plainTextToken;
        
        if ($user->role_id == 4) {
            $beneficiary = \App\Models\Beneficiary::where('portal_account_id', $user->portal_account_id)->first();
            $familyMembers = \App\Models\FamilyMember::where('portal_account_id', $user->portal_account_id)->get();

            return response()->json([
                'success' => true,
                'select_user_required' => true,
                'users' => [
                    'beneficiary' => $beneficiary,
                    'family_members' => $familyMembers,
                ],
                'token' => $token,
            ]);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'role_id' => $user->role_id,
                'photo' => $user->photo ? asset('storage/' . $user->photo) : null
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

        // Example: Map role_id to role name
        $roleNames = [
            1 => 'admin',
            2 => 'care_manager',
            3 => 'care_worker',
            4 => 'portal',
        ];
        $role = $roleNames[$user->role_id] ?? 'unknown';

        // If portal user, return selected beneficiary or family member
        if ($user->role_id == 4) {
            $type = session('portal_user_type');
            $id = session('portal_user_id');
            if ($type === 'beneficiary') {
                $selected = \App\Models\Beneficiary::find($id);
            } elseif ($type === 'family_member') {
                $selected = \App\Models\FamilyMember::find($id);
            } else {
                $selected = null;
            }
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'role' => $role,
                    'selected_type' => $type,
                    'selected_user' => $selected,
                    'email' => $user->email,
                    'status' => $user->status ?? null,
                ]
            ]);
        }

        // For other users, return unified user data
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'role' => $role,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'status' => $user->status ?? null,
                'photo' => $user->photo ? asset('storage/' . $user->photo) : null
            ]
        ]);
    }
}
