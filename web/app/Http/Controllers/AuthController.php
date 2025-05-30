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
    
    // Updated logout function to handle multiple guards
    public function logout(Request $request)
    {
        // Check if user is logged in as staff
        if (Auth::check()) {
            $user = Auth::user();
            $fullName = $user->first_name . ' ' . $user->last_name;
            $this->logService->createLog(
                'user',
                $user->id,
                LogType::VIEW,
                $fullName . ' logged out.'
            );
            Auth::logout();
        } 
        // Check if user is logged in as beneficiary
        else if (Auth::guard('beneficiary')->check()) {
            $beneficiary = Auth::guard('beneficiary')->user();
            $fullName = $beneficiary->first_name . ' ' . $beneficiary->last_name;
            $this->logService->createLog(
                'beneficiary',
                $beneficiary->beneficiary_id,
                LogType::VIEW,
                $fullName . ' (beneficiary) logged out.'
            );
            Auth::guard('beneficiary')->logout();
        }
        // Check if user is logged in as family member
        else if (Auth::guard('family')->check()) {
            $familyMember = Auth::guard('family')->user();
            $fullName = $familyMember->first_name . ' ' . $familyMember->last_name;
            $this->logService->createLog(
                'family_member',
                $familyMember->family_member_id,
                LogType::VIEW,
                $fullName . ' (family) logged out.'
            );
            Auth::guard('family')->logout();
        }
        
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
            'email' => 'required|email',
            'user_type' => 'required|in:staff,beneficiary,family'
        ]);

        $email = $request->email;
        $userType = $request->user_type;
        
        // Different handling based on user type
        switch ($userType) {
            case 'staff':
                $user = \App\Models\User::where('email', $email)->first();
                if ($user) {
                    $fullName = $user->first_name . ' ' . $user->last_name;
                    $this->logService->createLog(
                        'user',
                        $user->id,
                        LogType::UPDATE,
                        $fullName . ' requested a password reset.'
                    );
                }
                break;
                
            case 'beneficiary':
                $beneficiary = \App\Models\Beneficiary::where('username', $email)->first();
                if ($beneficiary) {
                    $fullName = $beneficiary->first_name . ' ' . $beneficiary->last_name;
                    $this->logService->createLog(
                        'beneficiary',
                        $beneficiary->beneficiary_id,
                        LogType::UPDATE,
                        $fullName . ' (beneficiary) requested a password reset.'
                    );
                }
                break;
                
            case 'family':
                $familyMember = \App\Models\FamilyMember::where('email', $email)->first();
                if ($familyMember) {
                    $fullName = $familyMember->first_name . ' ' . $familyMember->last_name;
                    $this->logService->createLog(
                        'family_member',
                        $familyMember->family_member_id,
                        LogType::UPDATE,
                        $fullName . ' (family) requested a password reset.'
                    );
                }
                break;
        }
        
        // Instead of actually sending an email, just show a success message
        return back()->with('status', 'Password reset link would be sent to your email if it exists in our system.');
    }
}
?>

<!-- old auth -->