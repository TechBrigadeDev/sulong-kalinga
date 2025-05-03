<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Logout function
    public function logout()
    {
        Auth::logout(); // Log out the user
        session()->flush(); // Clear the session data
        return redirect()->route('login'); // Redirect to the login page
    }

    public function showForgotPassword()
    {
        return view('forgot-password');
    }
    
    public function forgotPasswordSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        
        // Instead of actually sending an email, just show a success message
        return back()->with('status', 'Password reset link would be sent to your email if it exists in our system.');
    }
}
?>

<!-- old auth -->