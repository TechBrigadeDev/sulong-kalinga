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
        $query = FamilyMember::query();

        // Optional: search/filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $familyMembers = $query->orderBy('first_name')->get();

        return response()->json([
            'success' => true,
            'family_members' => $familyMembers
        ]);
    }

    // Show a single family member
    public function show($id)
    {
        $familyMember = FamilyMember::findOrFail($id);

        return response()->json([
            'success' => true,
            'family_member' => $familyMember
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

        $familyMember = FamilyMember::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $familyMember
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

        $familyMember->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $familyMember
        ]);
    }

    // Delete family member (admin only)
    public function destroy(Request $request, $id)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $familyMember = FamilyMember::findOrFail($id);
        $familyMember->delete();

        return response()->json(['success' => true]);
    }
}