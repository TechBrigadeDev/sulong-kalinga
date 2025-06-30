<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User; // cose_users table
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\UnifiedUser; // users table
use App\Models\FcmToken;
use App\Models\MobileDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\UploadService;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Authenticate user and return token
     */
    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        if (!$request->has('email') && !$request->has('username')) {
            return response()->json([
                'message' => 'Either email or username is required.',
                'errors' => [
                    'email' => ['The email field is required when username is not present.'],
                    'username' => ['The username field is required when email is not present.'],
                ]
            ], 422);
        }

        $user = null;
        if ($request->filled('email')) {
            $user = UnifiedUser::where('email', $request->email)->first();
        } elseif ($request->filled('username')) {
            $user = UnifiedUser::where('username', $request->username)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Role mapping
        $roleNames = [
            1 => 'admin',
            2 => 'care_manager',
            3 => 'care_worker',
            4 => 'beneficiary',
            5 => 'family_member',
        ];
        $role = $roleNames[$user->role_id] ?? 'unknown';

        // --- Status checks ---
        // For family_member, check related beneficiary's status
        if ($role === 'family_member') {
            $familyMember = FamilyMember::find($user->family_member_id);
            if (!$familyMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'Family member account not found.'
                ], 403);
            }
            $beneficiary = Beneficiary::find($familyMember->related_beneficiary_id);
            if (!$beneficiary || $beneficiary->beneficiary_status_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is inactive because the related beneficiary is not active.'
                ], 403);
            }
        } else {
            // For all other users, check their own status
            if (strtolower($user->status) !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is inactive. Please contact support.'
                ], 403);
            }
        }

        $user->tokens()->delete();
        $token = $user->createToken('mobile-app')->plainTextToken;

        // Build uniform response
        $responseUser = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'mobile' => $user->mobile,
            'role' => $role,
            'status' => $user->status, 
        ];

        if ($role === 'beneficiary') {
            $beneficiary = Beneficiary::find($user->beneficiary_id);
            $photo = $beneficiary?->photo;
            $responseUser['photo'] = $photo;
            $responseUser['photo_url'] = $photo ? $this->uploadService->getTemporaryPrivateUrl($photo, 30) : null;
            $responseUser['username'] = $user->username;
            $familyMembers = FamilyMember::where('related_beneficiary_id', $user->beneficiary_id)
                ->get()
                ->map(function ($fm) {
                    return [
                        'id' => $fm->family_member_id,
                        'first_name' => $fm->first_name,
                        'last_name' => $fm->last_name,
                        'mobile' => $fm->mobile,
                        'photo' => $fm->photo,
                        'photo_url' => $fm->photo
                            ? app(UploadService::class)->getTemporaryPrivateUrl($fm->photo, 30)
                            : null,
                    ];
                })
                ->values();
            $responseUser['family_members'] = $familyMembers;
        } elseif ($role === 'family_member') {
            $familyMember = FamilyMember::find($user->family_member_id);
            $photo = $familyMember?->photo;
            $responseUser['photo'] = $photo;
            $responseUser['photo_url'] = $photo ? $this->uploadService->getTemporaryPrivateUrl($photo, 30) : null;
            $responseUser['email'] = $familyMember?->email;
            $responseUser['related_beneficiary_id'] = $familyMember?->related_beneficiary_id;
        } elseif ($role === 'admin') {
            $admin = User::find($user->cose_user_id);
            $photo = $admin?->photo;
            $responseUser['photo'] = $photo;
            $responseUser['photo_url'] = $photo ? $this->uploadService->getTemporaryPrivateUrl($photo, 30) : null;
            $responseUser['email'] = $admin?->email;
            $responseUser['organization_role_id'] = $admin?->organization_role_id ?? null;
        } else {
            // care_manager, care_worker
            $responseUser['photo'] = $user->photo;
            $responseUser['photo_url'] = $user->photo ? $this->uploadService->getTemporaryPrivateUrl($user->photo, 30) : null;
            $responseUser['email'] = $user->email;
        }

        return response()->json([
            'success' => true,
            'user' => $responseUser,
            'token' => $token,
        ]);
    }

    /**
     * Logout user (revoke token and device-specific push token)
     */
    public function logout(Request $request)
    {
        // Validate device_uuid for device-specific logout
        $validator = Validator::make($request->all(), [
            'device_uuid' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $deviceUuid = $request->input('device_uuid');

        // Remove the push token for this user+device (all roles)
        FcmToken::where('user_id', $user->id)
            ->where('device_uuid', $deviceUuid)
            ->delete();

        // Optionally, also remove the device record
        MobileDevice::where('device_uuid', $deviceUuid)
            ->where('user_id', $user->id)
            ->delete();

        // Revoke the Laravel access token
        $user->currentAccessToken()->delete();

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

        $roleNames = [
            1 => 'admin',
            2 => 'care_manager',
            3 => 'care_worker',
            4 => 'beneficiary',
            5 => 'family_member',
        ];
        $role = $roleNames[$user->role_id] ?? 'unknown';

        $responseUser = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'mobile' => $user->mobile,
            'role' => $role,
            'status' => $user->status, 
        ];

        if ($role === 'beneficiary') {
            $beneficiary = Beneficiary::find($user->beneficiary_id);
            $photo = $beneficiary?->photo;
            $responseUser['photo'] = $photo;
            $responseUser['photo_url'] = $photo ? $this->uploadService->getTemporaryPrivateUrl($photo, 30) : null;
            $responseUser['username'] = $user->username;
            // $familyMembers = FamilyMember::where('related_beneficiary_id', $user->beneficiary_id)
            //     ->get()
            //     ->map(function ($fm) {
            //         return [
            //             'id' => $fm->family_member_id,
            //             'first_name' => $fm->first_name,
            //             'last_name' => $fm->last_name,
            //             'mobile' => $fm->mobile,
            //             'photo' => $fm->photo,
            //             'photo_url' => $fm->photo
            //                 ? app(UploadService::class)->getTemporaryPrivateUrl($fm->photo, 30)
            //                 : null,
            //         ];
            //     })
            //     ->values();
            $responseUser['family_members'] = $familyMembers;
        } elseif ($role === 'family_member') {
            $familyMember = FamilyMember::find($user->family_member_id);
            $photo = $familyMember?->photo;
            $responseUser['photo'] = $photo;
            $responseUser['photo_url'] = $photo ? $this->uploadService->getTemporaryPrivateUrl($photo, 30) : null;
            $responseUser['email'] = $familyMember?->email;
            // $responseUser['related_beneficiary_id'] = $familyMember?->related_beneficiary_id;
        } elseif ($role === 'admin') {
            $admin = User::find($user->cose_user_id);
            $photo = $admin?->photo;
            $responseUser['photo'] = $photo;
            $responseUser['photo_url'] = $photo ? $this->uploadService->getTemporaryPrivateUrl($photo, 30) : null;
            $responseUser['email'] = $admin?->email;
            $responseUser['organization_role_id'] = $admin?->organization_role_id ?? null;
        } else {
            // care_manager, care_worker
            $responseUser['photo'] = $user->photo;
            $responseUser['photo_url'] = $user->photo ? $this->uploadService->getTemporaryPrivateUrl($user->photo, 30) : null;
            $responseUser['email'] = $user->email;
        }

        return response()->json([
            'success' => true,
            'user' => $responseUser,
        ]);
    }
}
