<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;
use App\Enums\LogType;

class AuthController extends Controller
{
    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }
    
    // Logout function
    public function logout()
    {
        $user = Auth::user(); // Get the user BEFORE logging out
        Auth::logout(); // Log out the user
        session()->flush(); // Clear the session data

        if ($user) {
            $fullName = $user->first_name . ' ' . $user->last_name;
            $this->logService->createLog(
                'user',
                $user->id,
                LogType::VIEW, // or LogType::UPDATE if you prefer
                $fullName . ' logged out.'
            );
        }    

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

        // Log the password reset request
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user) {
            $fullName = $user->first_name . ' ' . $user->last_name;
            $this->logService->createLog(
                'user',
                $user->id,
                LogType::UPDATE,
                $fullName . ' requested a password reset.'
            );
        }
        
        // Instead of actually sending an email, just show a success message
        return back()->with('status', 'Password reset link would be sent to your email if it exists in our system.');
    }
}
?>

<!-- old auth -->