<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FamilyMemberApiController extends Controller
{
    /**
     * Display a listing of family members.
     */
    public function index(Request $request)
    {
        $query = FamilyMember::with('beneficiary');
            
        // Filter by beneficiary ID
        if ($request->has('beneficiary_id')) {
            $query->where('beneficiary_id', $request->get('beneficiary_id'));
        }
        
        // Add search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }
        
        $familyMembers = $query->orderBy('first_name')->get();
        
        return response()->json([
            'success' => true,
            'family_members' => $familyMembers
        ]);
    }

    /**
     * Display the specified family member.
     */
    public function show($id)
    {
        $familyMember = FamilyMember::with('beneficiary')->findOrFail($id);
            
        return response()->json([
            'success' => true,
            'family_member' => $familyMember
        ]);
    }

    /**
     * Store a newly created family member.
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'beneficiary_id' => 'required|exists:cose_users,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'gender' => 'required|string|in:Male,Female,Other',
            'relationship' => 'required|string',
            'mobile' => 'nullable|string',
            'email' => 'nullable|email',
            'occupation' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'is_emergency_contact' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Verify beneficiary exists
        $beneficiary = User::where('role_id', 5)->findOrFail($request->input('beneficiary_id'));
        
        // Create a new family member
        $familyMember = new FamilyMember();
        $familyMember->beneficiary_id = $request->input('beneficiary_id');
        $familyMember->first_name = $request->input('first_name');
        $familyMember->last_name = $request->input('last_name');
        $familyMember->birth_date = $request->input('birth_date');
        $familyMember->gender = $request->input('gender');
        $familyMember->relationship = $request->input('relationship');
        $familyMember->mobile = $request->input('mobile');
        $familyMember->email = $request->input('email');
        $familyMember->occupation = $request->input('occupation');
        $familyMember->is_emergency_contact = $request->input('is_emergency_contact', false);
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $uniqueIdentifier = time() . '_' . Str::random(5);
            $photoPath = $request->file('photo')->storeAs(
                'uploads/family_member_photos',
                $request->input('first_name') . '_' . $request->input('last_name') . '_photo_' . $uniqueIdentifier . '.' . 
                $request->file('photo')->getClientOriginalExtension(),
                'public'
            );
            $familyMember->photo = $photoPath;
        }
        
        $familyMember->save();
        
        // If marked as emergency contact, update beneficiary's emergency contact info
        if ($request->input('is_emergency_contact', false)) {
            $beneficiary->emergency_contact_name = $familyMember->first_name . ' ' . $familyMember->last_name;
            $beneficiary->emergency_contact_number = $familyMember->mobile;
            $beneficiary->emergency_contact_relationship = $familyMember->relationship;
            $beneficiary->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Family member created successfully',
            'family_member' => $familyMember
        ], 201);
    }

    /**
     * Update the specified family member.
     */
    public function update(Request $request, $id)
    {
        $familyMember = FamilyMember::findOrFail($id);
        
        $validator = \Validator::make($request->all(), [
            'beneficiary_id' => 'sometimes|required|exists:cose_users,id',
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'birth_date' => 'sometimes|required|date',
            'gender' => 'sometimes|required|string|in:Male,Female,Other',
            'relationship' => 'sometimes|required|string',
            'mobile' => 'sometimes|nullable|string',
            'email' => 'sometimes|nullable|email',
            'occupation' => 'sometimes|nullable|string',
            'photo' => 'sometimes|nullable|image|max:2048',
            'is_emergency_contact' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Update family member details
        $fieldsToUpdate = [
            'first_name', 'last_name', 'gender', 'relationship',
            'mobile', 'email', 'occupation', 'is_emergency_contact'
        ];
        
        foreach ($fieldsToUpdate as $field) {
            if ($request->has($field)) {
                $familyMember->{$field} = $request->input($field);
            }
        }
        
        if ($request->has('birth_date')) {
            $familyMember->birth_date = $request->input('birth_date');
        }
        
        if ($request->has('beneficiary_id')) {
            $familyMember->beneficiary_id = $request->input('beneficiary_id');
        }
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($familyMember->photo && Storage::disk('public')->exists($familyMember->photo)) {
                Storage::disk('public')->delete($familyMember->photo);
            }
            
            $uniqueIdentifier = time() . '_' . Str::random(5);
            $photoPath = $request->file('photo')->storeAs(
                'uploads/family_member_photos',
                $familyMember->first_name . '_' . $familyMember->last_name . '_photo_' . $uniqueIdentifier . '.' . 
                $request->file('photo')->getClientOriginalExtension(),
                'public'
            );
            $familyMember->photo = $photoPath;
        }
        
        $familyMember->save();
        
        // If marked as emergency contact, update beneficiary's emergency contact info
        if ($request->has('is_emergency_contact') && $request->input('is_emergency_contact')) {
            $beneficiary = User::findOrFail($familyMember->beneficiary_id);
            $beneficiary->emergency_contact_name = $familyMember->first_name . ' ' . $familyMember->last_name;
            $beneficiary->emergency_contact_number = $familyMember->mobile;
            $beneficiary->emergency_contact_relationship = $familyMember->relationship;
            $beneficiary->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Family member updated successfully',
            'family_member' => $familyMember
        ]);
    }

    /**
     * Remove the specified family member.
     */
    public function destroy($id)
    {
        $familyMember = FamilyMember::findOrFail($id);
        
        // Delete associated photo if exists
        if ($familyMember->photo && Storage::disk('public')->exists($familyMember->photo)) {
            Storage::disk('public')->delete($familyMember->photo);
        }
        
        // If this was an emergency contact, clear the beneficiary's emergency contact info
        if ($familyMember->is_emergency_contact) {
            $beneficiary = User::findOrFail($familyMember->beneficiary_id);
            $beneficiary->emergency_contact_name = null;
            $beneficiary->emergency_contact_number = null;
            $beneficiary->emergency_contact_relationship = null;
            $beneficiary->save();
        }
        
        $familyMember->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Family member deleted successfully'
        ]);
    }
}