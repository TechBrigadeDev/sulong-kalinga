<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function validatePassword(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|string',
            ]);

            $user = auth()->user();
            
            // Check if user is authenticated
            if (!$user) {
                return response()->json(['valid' => false, 'message' => 'User not authenticated'], 401);
            }

            if (Hash::check($request->password, $user->password)) {
                return response()->json(['valid' => true]);
            } else {
                return response()->json(['valid' => false, 'message' => 'Incorrect password']);
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Password validation error: ' . $e->getMessage());
            
            // Always return JSON for API requests
            return response()->json(['valid' => false, 'message' => 'An error occurred during validation'], 500);
        }
    }
}