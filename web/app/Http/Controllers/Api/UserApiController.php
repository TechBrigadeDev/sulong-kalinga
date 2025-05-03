<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnifiedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserApiController extends Controller
{
    /**
     * Update the authenticated user's profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validator = \Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes', 'required', 'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'mobile' => [
                'sometimes', 'required', 'string',
                Rule::unique('users', 'mobile')->ignore($user->id),
            ],
            'address' => 'sometimes|required|string|max:255',
            'gender' => 'sometimes|required|in:Male,Female,Other',
            'civil_status' => 'sometimes|required|in:Single,Married,Widowed,Divorced',
            'religion' => 'sometimes|nullable|string',
            'nationality' => 'sometimes|nullable|string',
            'photo' => 'sometimes|nullable|image|max:2048',
            'current_password' => 'required_with:password',
            'password' => 'sometimes|nullable|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle photo upload if present
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            
            $uniqueIdentifier = time() . '_' . Str::random(5);
            $photoPath = $request->file('photo')->storeAs(
                'uploads/profile_photos',
                $user->first_name . '_' . $user->last_name . '_photo_' . $uniqueIdentifier . '.' . 
                $request->file('photo')->getClientOriginalExtension(),
                'public'
            );
            
            $user->photo = $photoPath;
        }

        // Update basic info
        $fieldsToUpdate = [
            'first_name', 'last_name', 'email', 'mobile',
            'address', 'gender', 'civil_status', 'religion', 'nationality'
        ];
        
        foreach ($fieldsToUpdate as $field) {
            if ($request->has($field)) {
                $user->{$field} = $request->input($field);
            }
        }

        // Update password if provided
        if ($request->filled('password')) {
            // Verify current password
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }
            
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'personal_email' => $user->personal_email,
                'mobile' => $user->mobile,
                'address' => $user->address,
                'gender' => $user->gender,
                'civil_status' => $user->civil_status,
                'religion' => $user->religion,
                'nationality' => $user->nationality,
                'role_id' => $user->role_id,
                'photo' => $user->photo ? asset('storage/' . $user->photo) : null
            ]
        ]);
    }
}