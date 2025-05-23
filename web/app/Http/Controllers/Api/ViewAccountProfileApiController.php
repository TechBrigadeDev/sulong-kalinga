<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ViewAccountProfileApiController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('organizationRole');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'full_name' => $user->first_name . ' ' . $user->last_name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'birthday' => $user->birthday,
                'gender' => $user->gender,
                'nationality' => $user->nationality,
                'marital_status' => $user->marital_status,
                'education' => $user->education,
                'religion' => $user->religion,
                'work_email' => $user->email,
                'personal_email' => $user->personal_email ?? null,
                'mobile' => $user->mobile,
                'landline' => $user->landline,
                'address' => $user->address,
                'account_status' => $user->status,
                'sss_id' => $user->sss_id,
                'philhealth_id' => $user->philhealth_id,
                'pagibig_id' => $user->pagibig_id,
                'member_since' => $user->created_at ? $user->created_at->format('M Y') : null,
                'role' => $user->organizationRole->role_name ?? null,
            ]
        ]);
    }

    public function updateEmail(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'new_email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password.'
            ], 403);
        }

        $user->email = $request->new_email;
        $user->save();

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
            'new_password' => 'required|string|min:8|confirmed', // Add 'confirmed' if you want password confirmation
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
