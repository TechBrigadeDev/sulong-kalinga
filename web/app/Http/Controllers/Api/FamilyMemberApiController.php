<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FamilyMemberApiController extends Controller
{
    // List all family members
    public function index(Request $request)
    {
        $query = FamilyMember::with('beneficiary');

        // Optional: search/filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'ILIKE', "%{$search}%")
                  ->orWhere('last_name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        $familyMembers = $query->orderBy('first_name')->get();

        return response()->json([
            'success' => true,
            'family_members' => $familyMembers->map(function($fm) {
                return [
                    'family_member_id' => $fm->family_member_id,
                    'first_name' => $fm->first_name,
                    'last_name' => $fm->last_name,
                    'email' => $fm->email,
                    'mobile' => $fm->mobile,
                    'relation_to_beneficiary' => $fm->relation_to_beneficiary,
                    'is_primary_caregiver' => $fm->is_primary_caregiver,
                    'photo' => $fm->photo,
                    'beneficiary' => $fm->beneficiary,
                    // Add other fields as needed for mobile
                ];
            })
        ]);
    }

    // Show a single family member
    public function show($id)
    {
        $familyMember = FamilyMember::with('beneficiary')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'family_member_id' => $familyMember->family_member_id,
                'first_name' => $familyMember->first_name,
                'last_name' => $familyMember->last_name,
                'email' => $familyMember->email,
                'mobile' => $familyMember->mobile,
                'relation_to_beneficiary' => $familyMember->relation_to_beneficiary,
                'is_primary_caregiver' => $familyMember->is_primary_caregiver,
                'photo' => $familyMember->photo,
                'beneficiary' => $familyMember->beneficiary,
                // Add other fields as needed for mobile
            ]
        ]);
    }

    // Add a new family member (admin only)
    public function store(Request $request)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required', 'email',
                Rule::unique('family_members', 'email'),
            ],
            'mobile' => [
                'required', 'string',
                Rule::unique('family_members', 'mobile'),
            ],
            'related_beneficiary_id' => 'required|integer|exists:beneficiaries,beneficiary_id',
            'relation_to_beneficiary' => 'required|string|max:50',
            // Add other required fields as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('is_primary_caregiver') && $request->is_primary_caregiver) {
            $exists = FamilyMember::where('related_beneficiary_id', $request->related_beneficiary_id)
                ->where('is_primary_caregiver', true)
                ->exists();
            if ($exists) {
                return response()->json([
                    'errors' => [
                        'is_primary_caregiver' => ['There is already a primary caregiver for this beneficiary.']
                    ]
                ], 422);
            }
        }

        $mobile = $request->mobile;
        if (preg_match('/^09\d{9}$/', $mobile)) {
            $mobile = preg_replace('/^0/', '+63', $mobile);
        }
        $request->merge(['mobile' => $mobile]);

        $data = $request->all();

        // Accept photo path from mobile upload API
        if ($request->has('photo')) {
            $data['photo'] = $request->input('photo');
        }

        $familyMember = FamilyMember::create($data);

        return response()->json([
            'success' => true,
            'data' => [
                'family_member_id' => $familyMember->family_member_id,
                'first_name' => $familyMember->first_name,
                'last_name' => $familyMember->last_name,
                'email' => $familyMember->email,
                'mobile' => $familyMember->mobile,
                'relation_to_beneficiary' => $familyMember->relation_to_beneficiary,
                'is_primary_caregiver' => $familyMember->is_primary_caregiver,
                'photo' => $familyMember->photo,
                'beneficiary' => $familyMember->beneficiary,
                // Add other fields as needed for mobile
            ]
        ]);
    }

    // Edit family member (admin only)
    public function update(Request $request, $id)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $familyMember = FamilyMember::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes', 'required', 'email',
                Rule::unique('family_members', 'email')->ignore($familyMember->family_member_id, 'family_member_id'),
            ],
            'mobile' => [
                'sometimes', 'required', 'string',
                Rule::unique('family_members', 'mobile')->ignore($familyMember->family_member_id, 'family_member_id'),
            ],
            'related_beneficiary_id' => 'sometimes|required|integer|exists:beneficiaries,beneficiary_id',
            'relation_to_beneficiary' => 'sometimes|required|string|max:50',
            // Add other fields as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('is_primary_caregiver') && $request->is_primary_caregiver) {
            $exists = FamilyMember::where('related_beneficiary_id', $request->related_beneficiary_id ?? $familyMember->related_beneficiary_id)
                ->where('is_primary_caregiver', true)
                ->where('family_member_id', '!=', $familyMember->family_member_id)
                ->exists();
            if ($exists) {
                return response()->json([
                    'errors' => [
                        'is_primary_caregiver' => ['There is already a primary caregiver for this beneficiary.']
                    ]
                ], 422);
            }
        }

        if ($request->has('mobile')) {
            $mobile = $request->mobile;
            if (preg_match('/^09\d{9}$/', $mobile)) {
                $mobile = preg_replace('/^0/', '+63', $mobile);
            }
            $request->merge(['mobile' => $mobile]);
        }

        $data = $request->all();

        if ($request->has('photo')) {
            $data['photo'] = $request->input('photo');
        }

        $familyMember->update($data);

        return response()->json([
            'success' => true,
            'data' => [
                'family_member_id' => $familyMember->family_member_id,
                'first_name' => $familyMember->first_name,
                'last_name' => $familyMember->last_name,
                'email' => $familyMember->email,
                'mobile' => $familyMember->mobile,
                'relation_to_beneficiary' => $familyMember->relation_to_beneficiary,
                'is_primary_caregiver' => $familyMember->is_primary_caregiver,
                'photo' => $familyMember->photo,
                'beneficiary' => $familyMember->beneficiary,
                // Add other fields as needed for mobile
            ]
        ]);
    }

    // Delete family member (admin only)
    public function destroy(Request $request, $id)
    {
        // Only admins and care managers can delete(role 3 is care worker)
        if (!in_array($request->user()->role_id, [1, 2])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $familyMember = FamilyMember::findOrFail($id);

        // Dependency check: prevent deletion if acknowledged care plans exist
        if (method_exists($familyMember, 'acknowledgedCarePlans') && $familyMember->acknowledgedCarePlans()->exists()) {
            return response()->json([
                'errors' => [
                    'delete' => ['Cannot delete: Family member has acknowledged care plans.']
                ]
            ], 422);
        }

        $familyMember->delete();

        return response()->json(['success' => true]);
    }
}