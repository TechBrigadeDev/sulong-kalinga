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

class CareWorkerApiController extends Controller
{
    /**
     * Display a listing of care workers.
     */
    public function index(Request $request)
    {
        // Only allow Admin and CM to view care workers
        if (!in_array($request->user()->role_id, [1, 2])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        
        $query = User::where('role_id', 3)
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
        
        $careworkers = $query->orderBy('first_name')->get();
        
        return response()->json([
            'success' => true,
            'careworkers' => $careworkers
        ]);
    }

    /**
     * Display the specified care worker.
     */
    public function show($id)
    {
        $careworker = User::where('role_id', 3)
            ->with('municipality')
            ->findOrFail($id);
            
        return response()->json([
            'success' => true,
            'careworker' => $careworker
        ]);
    }

    /**
     * Store a newly created care worker.
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
            'sss_id' => 'nullable|string|max:10',
            'philhealth_id' => 'nullable|string|max:12',
            'pagibig_id' => 'nullable|string|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Create a new care worker
        $careworker = new User();
        $careworker->role_id = 3; // Care Worker role
        $careworker->first_name = $request->input('first_name');
        $careworker->last_name = $request->input('last_name');
        $careworker->birthday = $request->input('birth_date');
        $careworker->gender = $request->input('gender');
        $careworker->civil_status = $request->input('civil_status');
        $careworker->religion = $request->input('religion');
        $careworker->nationality = $request->input('nationality');
        $careworker->educational_background = $request->input('educational_background');
        $careworker->address = $request->input('address');
        $careworker->personal_email = $request->input('personal_email');
        
        // Format mobile number
        $mobile = $request->input('mobile');
        if (substr($mobile, 0, 3) !== '+63') {
            $mobile = '+63' . $mobile;
        }
        $careworker->mobile = $mobile;
        
        $careworker->landline = $request->input('landline');
        $careworker->email = $request->input('email');
        $careworker->password = Hash::make($request->input('password'));
        $careworker->assigned_municipality_id = $request->input('municipality_id');
        $careworker->volunteer_status = 'Active';
        
        // Set government ID numbers
        $careworker->sss_id_number = $request->input('sss_id');
        $careworker->philhealth_id_number = $request->input('philhealth_id');
        $careworker->pagibig_id_number = $request->input('pagibig_id');
        
        // Handle file uploads
        $uniqueIdentifier = time() . '_' . Str::random(5);
        
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->storeAs(
                'uploads/careworker_photos',
                $request->input('first_name') . '_' . $request->input('last_name') . '_photo_' . $uniqueIdentifier . '.' . 
                $request->file('photo')->getClientOriginalExtension(),
                'public'
            );
            $careworker->photo = $photoPath;
        }
        
        if ($request->hasFile('government_id')) {
            $governmentIDPath = $request->file('government_id')->storeAs(
                'uploads/careworker_government_ids',
                $request->input('first_name') . '_' . $request->input('last_name') . '_government_id_' . $uniqueIdentifier . '.' . 
                $request->file('government_id')->getClientOriginalExtension(),
                'public'
            );
            $careworker->government_issued_id = $governmentIDPath;
        }
        
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->storeAs(
                'uploads/careworker_resumes',
                $request->input('first_name') . '_' . $request->input('last_name') . '_resume_' . $uniqueIdentifier . '.' . 
                $request->file('resume')->getClientOriginalExtension(),
                'public'
            );
            $careworker->cv_resume = $resumePath;
        }
        
        $careworker->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Care Worker created successfully',
            'careworker' => $careworker
        ], 201);
    }

    /**
     * Update the specified care worker.
     */
    public function update(Request $request, $id)
    {
        $careworker = User::where('role_id', 3)->findOrFail($id);
        
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
            'sss_id' => 'sometimes|nullable|string|max:10',
            'philhealth_id' => 'sometimes|nullable|string|max:12',
            'pagibig_id' => 'sometimes|nullable|string|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Update care worker details
        $fieldsToUpdate = [
            'first_name', 'last_name', 'gender', 'civil_status',
            'religion', 'nationality', 'educational_background', 'address',
            'personal_email', 'landline', 'email', 'volunteer_status'
        ];
        
        foreach ($fieldsToUpdate as $field) {
            if ($request->has($field)) {
                $careworker->{$field} = $request->input($field);
            }
        }
        
        if ($request->has('birth_date')) {
            $careworker->birthday = $request->input('birth_date');
        }
        
        if ($request->has('municipality_id')) {
            $careworker->assigned_municipality_id = $request->input('municipality_id');
        }
        
        // Format mobile number if provided
        if ($request->has('mobile')) {
            $mobile = $request->input('mobile');
            if (substr($mobile, 0, 3) !== '+63') {
                $mobile = '+63' . $mobile;
            }
            $careworker->mobile = $mobile;
        }
        
        // Update password if provided
        if ($request->filled('password')) {
            $careworker->password = Hash::make($request->input('password'));
        }
        
        // Update government ID numbers
        if ($request->has('sss_id')) {
            $careworker->sss_id_number = $request->input('sss_id');
        }
        if ($request->has('philhealth_id')) {
            $careworker->philhealth_id_number = $request->input('philhealth_id');
        }
        if ($request->has('pagibig_id')) {
            $careworker->pagibig_id_number = $request->input('pagibig_id');
        }
        
        // Handle file uploads
        $uniqueIdentifier = time() . '_' . Str::random(5);
        
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($careworker->photo && Storage::disk('public')->exists($careworker->photo)) {
                Storage::disk('public')->delete($careworker->photo);
            }
            
            $photoPath = $request->file('photo')->storeAs(
                'uploads/careworker_photos',
                $careworker->first_name . '_' . $careworker->last_name . '_photo_' . $uniqueIdentifier . '.' . 
                $request->file('photo')->getClientOriginalExtension(),
                'public'
            );
            $careworker->photo = $photoPath;
        }
        
        if ($request->hasFile('government_id')) {
            // Delete old file if exists
            if ($careworker->government_issued_id && Storage::disk('public')->exists($careworker->government_issued_id)) {
                Storage::disk('public')->delete($careworker->government_issued_id);
            }
            
            $governmentIDPath = $request->file('government_id')->storeAs(
                'uploads/careworker_government_ids',
                $careworker->first_name . '_' . $careworker->last_name . '_government_id_' . $uniqueIdentifier . '.' . 
                $request->file('government_id')->getClientOriginalExtension(),
                'public'
            );
            $careworker->government_issued_id = $governmentIDPath;
        }
        
        if ($request->hasFile('resume')) {
            // Delete old file if exists
            if ($careworker->cv_resume && Storage::disk('public')->exists($careworker->cv_resume)) {
                Storage::disk('public')->delete($careworker->cv_resume);
            }
            
            $resumePath = $request->file('resume')->storeAs(
                'uploads/careworker_resumes',
                $careworker->first_name . '_' . $careworker->last_name . '_resume_' . $uniqueIdentifier . '.' . 
                $request->file('resume')->getClientOriginalExtension(),
                'public'
            );
            $careworker->cv_resume = $resumePath;
        }
        
        $careworker->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Care Worker updated successfully',
            'careworker' => $careworker
        ]);
    }

    /**
     * Remove the specified care worker.
     */
    public function destroy($id)
    {
        $careworker = User::where('role_id', 3)->findOrFail($id);
        
        // Delete associated files
        if ($careworker->photo && Storage::disk('public')->exists($careworker->photo)) {
            Storage::disk('public')->delete($careworker->photo);
        }
        if ($careworker->government_issued_id && Storage::disk('public')->exists($careworker->government_issued_id)) {
            Storage::disk('public')->delete($careworker->government_issued_id);
        }
        if ($careworker->cv_resume && Storage::disk('public')->exists($careworker->cv_resume)) {
            Storage::disk('public')->delete($careworker->cv_resume);
        }
        
        $careworker->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Care Worker deleted successfully'
        ]);
    }
}