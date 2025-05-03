<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PortalAccount;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\PasswordResetMail;

class PasswordResetController extends Controller
{
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
        
        // Check if email exists in cose_users table
        $coseUser = User::where('email', $email)->first();
        
        // Check if email exists in portal_accounts table - FIXED COLUMN NAME
        $portalUser = PortalAccount::where('portal_email', $email)->first();
        
        // If email doesn't exist in either table, the message should be the same as we should not give away which addresses do and don't exist.
        if (!$coseUser && !$portalUser) {
            return redirect()->back()
                ->with('status', 'Password reset link has been sent to your email address.');
        }
        
        // Generate token for the appropriate user type
        if ($coseUser) {
            $token = PasswordReset::createToken($email, 'cose');
            $user = $coseUser;
            $userType = 'cose';
        } else {
            $token = PasswordReset::createToken($email, 'portal');
            $user = $portalUser;
            $userType = 'portal';
        }
        
        // Generate reset URL
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $email,
            'type' => $userType
        ]);
        
        // Send email
        Mail::to($email)->send(new PasswordResetMail($resetUrl, $user));
        
        return redirect()->back()
            ->with('status', 'Password reset link has been sent to your email address.');
    }
    
    public function showResetForm(Request $request, $token)
    {
        return view('reset-password', [
            'token' => $token,
            'email' => $request->email,
            'type' => $request->type
        ]);
    }
    
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'type' => 'required|in:cose,portal',
            'password' => 'required|confirmed|min:8',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Validate token
        if (!PasswordReset::validateToken($request->token, $request->email)) {
            return redirect()->back()
                ->withErrors(['email' => 'This password reset link is invalid or has expired.']);
        }
        
        // Update password based on user type
        if ($request->type === 'cose') {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return redirect()->back()
                    ->withErrors(['email' => 'User not found.']);
            }
            $user->password = Hash::make($request->password);
            $user->save();
        } else {
            // FIXED COLUMN NAME
            $user = PortalAccount::where('portal_email', $request->email)->first();
            if (!$user) {
                return redirect()->back()
                    ->withErrors(['email' => 'User not found.']);
            }
            $user->password = Hash::make($request->password);
            $user->save();
        }
        
        // Mark the token as used
        PasswordReset::markAsUsed($request->token, $request->email);
        
        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully. You can now log in with your new password.');
    }
}