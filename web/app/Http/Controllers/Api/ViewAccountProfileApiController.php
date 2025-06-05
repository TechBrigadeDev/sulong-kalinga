<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\UnifiedUser;
use App\Services\UploadService;

class ViewAccountProfileApiController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function show(Request $request)
    {
        $unifiedUser = $request->user();

        $profile = null;
        $roleNames = [
            1 => 'admin',
            2 => 'care_manager',
            3 => 'care_worker',
            4 => 'beneficiary',
            5 => 'family_member',
        ];
        $role = $roleNames[$unifiedUser->role_id] ?? 'unknown';

        if ($role === 'beneficiary') {
            $beneficiary = Beneficiary::with(['municipality', 'barangay', 'category', 'status'])->find($unifiedUser->beneficiary_id);

            if (!$beneficiary) {
                return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
            }

            $profile = [
                'id' => $beneficiary->beneficiary_id,
                'first_name' => $beneficiary->first_name,
                'middle_name' => $beneficiary->middle_name,
                'last_name' => $beneficiary->last_name,
                'full_name' => trim($beneficiary->first_name . ' ' . $beneficiary->middle_name . ' ' . $beneficiary->last_name),
                'birthday' => $beneficiary->birthday,
                'civil_status' => $beneficiary->civil_status,
                'gender' => $beneficiary->gender,
                'mobile' => $beneficiary->mobile,
                'landline' => $beneficiary->landline,
                'address' => $beneficiary->street_address,
                'municipality' => $beneficiary->municipality->municipality_name ?? null,
                'barangay' => $beneficiary->barangay->barangay_name ?? null,
                'category' => $beneficiary->category->category_name ?? null,
                'status' => $beneficiary->status->status_name ?? null,
                'emergency_contact_name' => $beneficiary->emergency_contact_name,
                'emergency_contact_relation' => $beneficiary->emergency_contact_relation,
                'emergency_contact_mobile' => $beneficiary->emergency_contact_mobile,
                'emergency_contact_email' => $beneficiary->emergency_contact_email,
                'emergency_procedure' => $beneficiary->emergency_procedure,
                'username' => $beneficiary->username,
                'account_status' => $unifiedUser->status,
                'member_since' => $beneficiary->created_at ? $beneficiary->created_at->format('M Y') : null,
                'photo_url' => $beneficiary->photo
                    ? $this->uploadService->getTemporaryPrivateUrl($beneficiary->photo, 30)
                    : null,
                'role' => $role,
            ];
        } elseif ($role === 'family_member') {
            $family = FamilyMember::with(['beneficiary'])->find($unifiedUser->family_member_id);

            if (!$family) {
                return response()->json(['success' => false, 'message' => 'Family member not found.'], 404);
            }

            $profile = [
                'id' => $family->family_member_id,
                'first_name' => $family->first_name,
                'last_name' => $family->last_name,
                'full_name' => trim($family->first_name . ' ' . $family->last_name),
                'birthday' => $family->birthday,
                'gender' => $family->gender,
                'mobile' => $family->mobile,
                'landline' => $family->landline,
                'email' => $family->email,
                'address' => $family->street_address,
                'relation_to_beneficiary' => $family->relation_to_beneficiary,
                'is_primary_caregiver' => $family->is_primary_caregiver,
                'related_beneficiary_id' => $family->related_beneficiary_id,
                'related_beneficiary_name' => $family->beneficiary ? ($family->beneficiary->first_name . ' ' . $family->beneficiary->last_name) : null,
                'account_status' => $unifiedUser->status,
                'member_since' => $family->created_at ? $family->created_at->format('M Y') : null,
                'photo_url' => $family->photo
                    ? $this->uploadService->getTemporaryPrivateUrl($family->photo, 30)
                    : null,
                'username' => $unifiedUser->username,
                'role' => $role,
            ];
        } else {
            // cose_users: admin, care_manager, care_worker
            $user = User::with(['organizationRole', 'municipality', 'assignedCareManager'])->find($unifiedUser->cose_user_id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }

            $profile = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => trim($user->first_name . ' ' . $user->last_name),
                'middle_name' => $user->middle_name ?? null,
                'birthday' => $user->birthday,
                'civil_status' => $user->civil_status,
                'educational_background' => $user->educational_background,
                'gender' => $user->gender,
                'religion' => $user->religion,
                'nationality' => $user->nationality,
                'work_email' => $user->email,
                'personal_email' => $user->personal_email,
                'mobile' => $user->mobile,
                'landline' => $user->landline,
                'address' => $user->address,
                'municipality' => $user->municipality->municipality_name ?? null,
                'assigned_care_manager' => $user->assignedCareManager ? ($user->assignedCareManager->first_name . ' ' . $user->assignedCareManager->last_name) : null,
                'account_status' => $user->status,
                'volunteer_status' => $user->volunteer_status ?? null,
                'status_start_date' => $user->status_start_date,
                'status_end_date' => $user->status_end_date,
                'sss_id' => $user->sss_id_number,
                'philhealth_id' => $user->philhealth_id_number,
                'pagibig_id' => $user->pagibig_id_number,
                'member_since' => $user->created_at ? $user->created_at->format('M Y') : null,
                'role' => $user->organizationRole->role_name ?? $role,
                'role_id' => $user->role_id,
                'organization_role_id' => $user->organization_role_id,
                'photo_url' => $user->photo
                    ? $this->uploadService->getTemporaryPrivateUrl($user->photo, 30)
                    : null,
                'username' => $unifiedUser->username,
                'email' => $user->email,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $profile
        ]);
    }

    public function updateEmail(Request $request)
    {
        $unifiedUser = $request->user();

        $role = $unifiedUser->role_id;
        $validator = Validator::make($request->all(), [
            'new_email' => 'required|email|unique:users,email,' . $unifiedUser->id,
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check password and update the correct table
        if ($role == 4) { // beneficiary
            $beneficiary = Beneficiary::find($unifiedUser->beneficiary_id);
            if (!$beneficiary || !Hash::check($request->password, $beneficiary->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect password.'
                ], 403);
            }
            $beneficiary->username = $request->new_email; // For beneficiaries, username is used for login
            $beneficiary->save();
        } elseif ($role == 5) { // family_member
            $family = FamilyMember::find($unifiedUser->family_member_id);
            if (!$family || !Hash::check($request->password, $family->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect password.'
                ], 403);
            }
            $family->email = $request->new_email;
            $family->save();
        } else { // cose_users (admin, care_manager, care_worker)
            $user = User::find($unifiedUser->cose_user_id);
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect password.'
                ], 403);
            }
            $user->email = $request->new_email;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Email updated successfully.',
            'data' => [
                'work_email' => $user->email
            ]
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.'
        ]);
    }
}
