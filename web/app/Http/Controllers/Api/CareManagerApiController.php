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

class CareManagerApiController extends Controller
{
    /**
     * Display a listing of care managers.
     */
    public function index(Request $request)
    {
        $query = User::where('role_id', 2)
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
            $query->where('volunteer_status', $request->get('status'));
        }
        
        $caremanagers = $query->orderBy('first_name')->get();
        
        return response()->json([
            'success' => true,
            'caremanagers' => $caremanagers
        ]);
    }

    /**
     * Display the specified care manager.
     */
    public function show($id)
    {
        $caremanager = User::where('role_id', 2)
            ->with('municipality')
            ->findOrFail($id);
            
        return response()->json([
            'success' => true,
            'caremanager' => $caremanager
        ]);
    }

    /**
     * Store a newly created care manager.
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
            'educational_background' => 'required|string|in:College,Highschool,Doctorate',
            'address' => 'required|string',
            'personal_email' => 'required|email|unique:cose_users,personal_email',
            'mobile' => 'required|string|unique:cose_users,mobile',
            'landline' => 'nullable|string',
            'email' => 'required|email|unique:cose_users,email',
            'password' => 'required|min:8|confirmed',
            'municipality_id' => 'required|exists:municipalities,municipality_id',
            'photo' => 'nullable|image|max:2048',
            'government_id' => 'nullable|image|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Create a new care manager
        $caremanager = new User();
        $caremanager->role_id = 2; // Care Manager role
        $caremanager->first_name = $request->input('first_name');
        $caremanager->last_name = $request->input('last_name');
        $caremanager->birthday = $request->input('birth_date');
        $caremanager->gender = $request->input('gender');
        $caremanager->civil_status = $request->input('civil_status');
        $caremanager->religion = $request->input('religion');
        $caremanager->nationality = $request->input('nationality');
        $caremanager->educational_background = $request->input('educational_background');
        $caremanager->address = $request->input('address');
        $caremanager->personal_email = $request->input('personal_email');
        
        // Format mobile number
        $mobile = $request->input('mobile');
        if (substr($mobile, 0, 3) !== '+63') {
            $mobile = '+63' . $mobile;
        }
        $caremanager->mobile = $mobile;
        
        $caremanager->landline = $request->input('landline');
        $caremanager->email = $request->input('email');
        $caremanager->password = Hash::make($request->input('password'));
        $caremanager->assigned_municipality_id = $request->input('municipality_id');
        $caremanager->volunteer_status = 'Active';
        
        // Handle file uploads
        $uniqueIdentifier = time() . '_' . Str::random(5);
        
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->storeAs(
                'uploads/caremanager_photos',
                $request->input('first_name') . '_' . $request->input('last_name') . '_photo_' . $uniqueIdentifier . '.' . 
                $request->file('photo')->getClientOriginalExtension(),
                'public'
            );
            $caremanager->photo = $photoPath;
        }
        
        if ($request->hasFile('government_id')) {
            $governmentIDPath = $request->file('government_id')->storeAs(
                'uploads/caremanager_government_ids',
                $request->input('first_name') . '_' . $request->input('last_name') . '_government_id_' . $uniqueIdentifier . '.' . 
                $request->file('government_id')->getClientOriginalExtension(),
                'public'
            );
            $caremanager->government_issued_id = $governmentIDPath;
        }
        
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->storeAs(
                'uploads/caremanager_resumes',
                $request->input('first_name') . '_' . $request->input('last_name') . '_resume_' . $uniqueIdentifier . '.' . 
                $request->file('resume')->getClientOriginalExtension(),
                'public'
            );
            $caremanager->cv_resume = $resumePath;
        }
        
        $caremanager->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Care Manager created successfully',
            'caremanager' => $caremanager
        ], 201);
    }

    /**
     * Update the specified care manager.
     */
    public function update(Request $request, $id)
    {
        $caremanager = User::where('role_id', 2)->findOrFail($id);
        
        $validator = \Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'birth_date' => 'sometimes|required|date',
            'gender' => 'sometimes|required|string|in:Male,Female,Other',
            'civil_status' => 'sometimes|required|string|in:Single,Married,Widowed,Divorced',
            'religion' => 'sometimes|nullable|string',
            'nationality' => 'sometimes|required|string',
            'educational_background' => 'sometimes|required|string|in:College,Highschool,Doctorate',
            'address' => 'sometimes|required|string',
            'personal_email' => [
                'sometimes', 'required', 'email',
                Rule::unique('cose_users', 'personal_email')->ignore($id),
            ],
            'mobile' => [
                'sometimes', 'required', 'string',
                Rule::unique('cose_users', 'mobile')->ignore($id),
            ],
            'landline' => 'sometimes|nullable|string',
            'email' => [
                'sometimes', 'required', 'email',
                Rule::unique('cose_users', 'email')->ignore($id),
            ],
            'password' => 'sometimes|nullable|min:8|confirmed',
            'municipality_id' => 'sometimes|required|exists:municipalities,municipality_id',
            'volunteer_status' => 'sometimes|required|in:Active,Inactive',
            'photo' => 'sometimes|nullable|image|max:2048',
            'government_id' => 'sometimes|nullable|image|max:2048',
            'resume' => 'sometimes|nullable|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Update care manager details
        $fieldsToUpdate = [
            'first_name', 'last_name', 'gender', 'civil_status',
            'religion', 'nationality', 'educational_background', 'address',
            'personal_email', 'landline', 'email', 'volunteer_status'
        ];
        
        foreach ($fieldsToUpdate as $field) {
            if ($request->has($field)) {
                $caremanager->{$field} = $request->input($field);
            }
        }
        
        if ($request->has('birth_date')) {
            $caremanager->birthday = $request->input('birth_date');
        }
        
        if ($request->has('municipality_id')) {
            $caremanager->assigned_municipality_id = $request->input('municipality_id');
        }
        
        // Format mobile number if provided
        if ($request->has('mobile')) {
            $mobile = $request->input('mobile');
            if (substr($mobile, 0, 3) !== '+63') {
                $mobile = '+63' . $mobile;
            }
            $caremanager->mobile = $mobile;
        }
        
        // Update password if provided
        if ($request->filled('password')) {
            $caremanager->password = Hash::make($request->input('password'));
        }
        
        // Handle file uploads
        $uniqueIdentifier = time() . '_' . Str::random(5);
        
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($caremanager->photo && Storage::disk('public')->exists($caremanager->photo)) {
                Storage::disk('public')->delete($caremanager->photo);
            }
            
            $photoPath = $request->file('photo')->storeAs(
                'uploads/caremanager_photos',
                $caremanager->first_name . '_' . $caremanager->last_name . '_photo_' . $uniqueIdentifier . '.' . 
                $request->file('photo')->getClientOriginalExtension(),
                'public'
            );
            $caremanager->photo = $photoPath;
        }
        
        if ($request->hasFile('government_id')) {
            // Delete old file if exists
            if ($caremanager->government_issued_id && Storage::disk('public')->exists($caremanager->government_issued_id)) {
                Storage::disk('public')->delete($caremanager->government_issued_id);
            }
            
            $governmentIDPath = $request->file('government_id')->storeAs(
                'uploads/caremanager_government_ids',
                $caremanager->first_name . '_' . $caremanager->last_name . '_government_id_' . $uniqueIdentifier . '.' . 
                $request->file('government_id')->getClientOriginalExtension(),
                'public'
            );
            $caremanager->government_issued_id = $governmentIDPath;
        }
        
        if ($request->hasFile('resume')) {
            // Delete old file if exists
            if ($caremanager->cv_resume && Storage::disk('public')->exists($caremanager->cv_resume)) {
                Storage::disk('public')->delete($caremanager->cv_resume);
            }
            
            $resumePath = $request->file('resume')->storeAs(
                'uploads/caremanager_resumes',
                $caremanager->first_name . '_' . $caremanager->last_name . '_resume_' . $uniqueIdentifier . '.' . 
                $request->file('resume')->getClientOriginalExtension(),
                'public'
            );
            $caremanager->cv_resume = $resumePath;
        }
        
        $caremanager->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Care Manager updated successfully',
            'caremanager' => $caremanager
        ]);
    }

    /**
     * Remove the specified care manager.
     */
    public function destroy($id)
    {
        $caremanager = User::where('role_id', 2)->findOrFail($id);
        
        // Delete associated files
        if ($caremanager->photo && Storage::disk('public')->exists($caremanager->photo)) {
            Storage::disk('public')->delete($caremanager->photo);
        }
        if ($caremanager->government_issued_id && Storage::disk('public')->exists($caremanager->government_issued_id)) {
            Storage::disk('public')->delete($caremanager->government_issued_id);
        }
        if ($caremanager->cv_resume && Storage::disk('public')->exists($caremanager->cv_resume)) {
            Storage::disk('public')->delete($caremanager->cv_resume);
        }
        
        $caremanager->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Care Manager deleted successfully'
        ]);
    }


    // /**
    //  * Get the municipality associated with the care manager.
    //  */
    // public function municipality($id)
    // {
    //     $caremanager = User::where('role_id', 2)->findOrFail($id);
    //     $municipality = Municipality::findOrFail($caremanager->assigned_municipality_id);
        
    //     return response()->json([
    //         'success' => true,
    //         'municipality' => $municipality
    //     ]);
    // }
}