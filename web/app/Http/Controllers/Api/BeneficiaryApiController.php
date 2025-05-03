<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BeneficiaryApiController extends Controller
{
    /**
     * Display a listing of beneficiaries.
     */
    public function index(Request $request)
    {
        $query = User::where('role_id', 5)
            ->with('municipality');
            
        // Add search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Add filtering by municipality
        if ($request->has('municipality_id')) {
            $query->where('assigned_municipality_id', $request->get('municipality_id'));
        }
        
        // Add filtering by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }
        
        $beneficiaries = $query->orderBy('first_name')->get();
        
        return response()->json([
            'success' => true,
            'beneficiaries' => $beneficiaries
        ]);
    }

    /**
     * Display the specified beneficiary.
     */
    public function show($id)
    {
        $beneficiary = User::where('role_id', 5)
            ->with('municipality')
            ->findOrFail($id);
            
        return response()->json([
            'success' => true,
            'beneficiary' => $beneficiary
        ]);
    }

    /**
     * Store a newly created beneficiary.
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'gender' => 'required|string|in:Male,Female,Other',
            'civil_status' => 'required|string|in:Single,Married,Widowed,Divorced',
            'religion' => 'nullable|string',
            'nationality' => 'required|string',
            'address' => 'required|string',
            'personal_email' => 'nullable|email|unique:cose_users,personal_email',
            'mobile' => 'required|string|unique:cose_users,mobile',
            'landline' => 'nullable|string',
            'emergency_contact_name' => 'required|string',
            'emergency_contact_number' => 'required|string',
            'emergency_contact_relationship' => 'required|string',
            'email' => 'nullable|email|unique:cose_users,email',
            'password' => 'required|min:8|confirmed',
            'municipality_id' => 'required|exists:municipalities,municipality_id',
            'photo' => 'nullable|image|max:2048',
            'medical_condition' => 'nullable|string',
            'philhealth_id' => 'nullable|string|max:12',
            'osca_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Create a new beneficiary
        $beneficiary = new User();
        $beneficiary->role_id = 5; // Beneficiary role
        $beneficiary->first_name = $request->input('first_name');
        $beneficiary->last_name = $request->input('last_name');
        $beneficiary->birthday = $request->input('birth_date');
        $beneficiary->gender = $request->input('gender');
        $beneficiary->civil_status = $request->input('civil_status');
        $beneficiary->religion = $request->input('religion');
        $beneficiary->nationality = $request->input('nationality');
        $beneficiary->address = $request->input('address');
        $beneficiary->personal_email = $request->input('personal_email');
        
        // Format mobile number
        $mobile = $request->input('mobile');
        if (substr($mobile, 0, 3) !== '+63') {
            $mobile = '+63' . $mobile;
        }
        $beneficiary->mobile = $mobile;
        
        $beneficiary->landline = $request->input('landline');
        
        // Emergency contact info
        $beneficiary->emergency_contact_name = $request->input('emergency_contact_name');
        $beneficiary->emergency_contact_number = $request->input('emergency_contact_number');
        $beneficiary->emergency_contact_relationship = $request->input('emergency_contact_relationship');
        
        $beneficiary->email = $request->input('email');
        $beneficiary->password = Hash::make($request->input('password'));
        $beneficiary->assigned_municipality_id = $request->input('municipality_id');
        $beneficiary->status = 'Active';
        
        // Health-related information
        $beneficiary->medical_condition = $request->input('medical_condition');
        $beneficiary->philhealth_id_number = $request->input('philhealth_id');
        $beneficiary->osca_id_number = $request->input('osca_id');
        
        // Handle file uploads
        $uniqueIdentifier = time() . '_' . Str::random(5);
        
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->storeAs(
                'uploads/beneficiary_photos',
                $request->input('first_name') . '_' . $request->input('last_name') . '_photo_' . $uniqueIdentifier . '.' . 
                $request->file('photo')->getClientOriginalExtension(),
                'public'
            );
            $beneficiary->photo = $photoPath;
        }
        
        $beneficiary->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Beneficiary created successfully',
            'beneficiary' => $beneficiary
        ], 201);
    }

    /**
     * Update the specified beneficiary.
     */
    public function update(Request $request, $id)
    {
        $beneficiary = User::where('role_id', 5)->findOrFail($id);
        
        $validator = \Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'birth_date' => 'sometimes|required|date',
            'gender' => 'sometimes|required|string|in:Male,Female,Other',
            'civil_status' => 'sometimes|required|string|in:Single,Married,Widowed,Divorced',
            'religion' => 'sometimes|nullable|string',
            'nationality' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'personal_email' => [
                'sometimes', 'nullable', 'email',
                Rule::unique('cose_users', 'personal_email')->ignore($id),
            ],
            'mobile' => [
                'sometimes', 'required', 'string',
                Rule::unique('cose_users', 'mobile')->ignore($id),
            ],
            'landline' => 'sometimes|nullable|string',
            'emergency_contact_name' => 'sometimes|required|string',
            'emergency_contact_number' => 'sometimes|required|string',
            'emergency_contact_relationship' => 'sometimes|required|string',
            'email' => [
                'sometimes', 'nullable', 'email',
                Rule::unique('cose_users', 'email')->ignore($id),
            ],
            'password' => 'sometimes|nullable|min:8|confirmed',
            'municipality_id' => 'sometimes|required|exists:municipalities,municipality_id',
            'status' => 'sometimes|required|in:Active,Inactive',
            'photo' => 'sometimes|nullable|image|max:2048',
            'medical_condition' => 'sometimes|nullable|string',
            'philhealth_id' => 'sometimes|nullable|string|max:12',
            'osca_id' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Update beneficiary details
        $fieldsToUpdate = [
            'first_name', 'last_name', 'gender', 'civil_status',
            'religion', 'nationality', 'address', 'personal_email',
            'landline', 'email', 'status', 'emergency_contact_name',
            'emergency_contact_number', 'emergency_contact_relationship',
            'medical_condition', 'philhealth_id_number', 'osca_id_number'
        ];
        
        foreach ($fieldsToUpdate as $field) {
            if ($request->has($field)) {
                $beneficiary->{$field} = $request->input($field);
            }
        }
        
        if ($request->has('birth_date')) {
            $beneficiary->birthday = $request->input('birth_date');
        }
        
        if ($request->has('municipality_id')) {
            $beneficiary->assigned_municipality_id = $request->input('municipality_id');
        }
        
        if ($request->has('philhealth_id')) {
            $beneficiary->philhealth_id_number = $request->input('philhealth_id');
        }
        
        if ($request->has('osca_id')) {
            $beneficiary->osca_id_number = $request->input('osca_id');
        }
        
        // Format mobile number if provided
        if ($request->has('mobile')) {
            $mobile = $request->input('mobile');
            if (substr($mobile, 0, 3) !== '+63') {
                $mobile = '+63' . $mobile;
            }
            $beneficiary->mobile = $mobile;
        }
        
        // Update password if provided
        if ($request->filled('password')) {
            $beneficiary->password = Hash::make($request->input('password'));
        }
        
        // Handle file uploads
        $uniqueIdentifier = time() . '_' . Str::random(5);
        
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($beneficiary->photo && Storage::disk('public')->exists($beneficiary->photo)) {
                Storage::disk('public')->delete($beneficiary->photo);
            }
            
            $photoPath = $request->file('photo')->storeAs(
                'uploads/beneficiary_photos',
                $beneficiary->first_name . '_' . $beneficiary->last_name . '_photo_' . $uniqueIdentifier . '.' . 
                $request->file('photo')->getClientOriginalExtension(),
                'public'
            );
            $beneficiary->photo = $photoPath;
        }
        
        $beneficiary->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Beneficiary updated successfully',
            'beneficiary' => $beneficiary
        ]);
    }

    /**
     * Remove the specified beneficiary.
     */
    public function destroy($id)
    {
        $beneficiary = User::where('role_id', 5)->findOrFail($id);
        
        // Delete associated files
        if ($beneficiary->photo && Storage::disk('public')->exists($beneficiary->photo)) {
            Storage::disk('public')->delete($beneficiary->photo);
        }
        
        $beneficiary->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Beneficiary deleted successfully'
        ]);
    }
}