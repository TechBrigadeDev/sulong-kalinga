<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FamilyMember;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\PasswordResetMail;

use App\Services\LogService;
use App\Enums\LogType;

class PasswordResetController extends Controller
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function showForgotPasswordForm()
    {
        return view('forgot-password');
    }
    
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $email = $request->email;
        
        // Only check in users and family_members tables
        $coseUser = User::where('email', $email)->first();
        $familyMember = FamilyMember::where('email', $email)->first();

        if (!$coseUser && !$familyMember) {
            return redirect()->back()
                ->with('status', 'Password reset link has been sent to your email address.');
        }
        
        if ($coseUser) {
            $token = PasswordReset::createToken($email, 'cose');
            $user = $coseUser;
            $userType = 'cose';
        } else {
            $token = PasswordReset::createToken($email, 'family');
            $user = $familyMember;
            $userType = 'family';
        }
        
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $email,
            'type' => $userType
        ]);

        // Render the email HTML using your Blade view
        $htmlContent = view('emails.password-reset', [
            'resetUrl' => $resetUrl,
            'user' => $user
        ])->render();

        // Send email via ZeptoMail API
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Zoho-enczapikey ' . env('ZEPTO_API_KEY'),
        ])->post('https://api.zeptomail.com/v1.1/email', [
            'from' => [
                'address' => env('ZEPTO_FROM_ADDRESS'),
                'name' => env('ZEPTO_FROM_NAME'),
            ],
            'to' => [
                [
                    'email_address' => [
                        'address' => $email,
                        'name' => $user->first_name ?? 'User'
                    ]
                ]
            ],
            'subject' => 'Reset Your Password',
            'htmlbody' => $htmlContent,
            // Optional: 'track_opens' => true, 'track_clicks' => true,
        ]);

        if (!$response->successful()) {
            return redirect()->back()
                ->withErrors(['email' => 'Failed to send password reset email. Please try again later.']);
        }

        $userName = $user->first_name . ' ' . $user->last_name;
        $this->logService->createLog(
            $userType === 'cose' ? 'user' : 'family_member',
            $user->id,
            LogType::CREATE,
            $userName . ' requested a password reset link.',
            $user->id
        );

        return redirect()->back()
            ->with('status', 'Password reset link has been sent to your email address.');
    }
    
    public function showResetForm(Request $request, $token)
    {
        return view('reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
            'type' => $request->query('type')
        ]);
    }
        
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'type' => 'required|in:cose,family',
            'password' => 'required|confirmed|min:8',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        if (!PasswordReset::validateToken($request->token, $request->email)) {
            return redirect()->back()
                ->withErrors(['email' => 'This password reset link is invalid or has expired.']);
        }
        
        if ($request->type === 'cose') {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return redirect()->back()
                    ->withErrors(['email' => 'User not found.']);
            }
            $user->password = Hash::make($request->password);
            $user->save();
            $userId = $user->id;
            $userName = $user->first_name . ' ' . $user->last_name;
            $entityType = 'user';
        } else {
            // For family members
            $user = FamilyMember::where('email', $request->email)->first();
            if (!$user) {
                return redirect()->back()
                    ->withErrors(['email' => 'User not found.']);
            }
            $user->password = Hash::make($request->password);
            $user->save();
            $userId = $user->id;
            $userName = $user->first_name . ' ' . $user->last_name;
            $entityType = 'family_member';
        }

        $this->logService->createLog(
            $entityType,
            $userId,
            LogType::UPDATE,
            $userName . ' reset their password.',
            $userId
        );

        PasswordReset::markAsUsed($request->token, $request->email);
        
        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully. You can now log in with your new password.');
    }
}