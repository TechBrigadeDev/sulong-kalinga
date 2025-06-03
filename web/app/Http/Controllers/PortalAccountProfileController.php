<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PortalAccountProfileController extends Controller
{
    /**
     * Display the beneficiary account profile page
     */
    public function beneficiaryIndex()
    {
        // Get the authenticated beneficiary
        $beneficiary = Auth::guard('beneficiary')->user();
        
        // Format the beneficiary's birthday (if it exists)
        $formattedBirthday = null;
        if ($beneficiary->birthday) {
            $formattedBirthday = Carbon::parse($beneficiary->birthday)->format('F d, Y');
        }
        
        // Format the account creation date
        $memberSince = Carbon::parse($beneficiary->created_at)->format('M Y');
        
        // Get the beneficiary's age
        $age = $beneficiary->birthday ? Carbon::parse($beneficiary->birthday)->age : null;
        
        return view('beneficiaryPortal.accountProfile', compact('beneficiary', 'formattedBirthday', 'memberSince', 'age'));
    }
    
    /**
     * Redirect to settings section of beneficiary profile
     */
    public function beneficiarySettings()
    {
        return redirect()->route('beneficiary.profile.index')->with('activeTab', 'settings');
    }
    
    /**
     * Display the family member account profile page
     */
    public function familyIndex()
    {
        // Get the authenticated family member
        $familyMember = Auth::guard('family')->user();
        
        // Format the family member's birthday (if it exists)
        $formattedBirthday = null;
        if ($familyMember->birthday) {
            $formattedBirthday = Carbon::parse($familyMember->birthday)->format('F d, Y');
        }
        
        // Format the account creation date
        $memberSince = Carbon::parse($familyMember->created_at)->format('M Y');
        
        // Get the family member's age
        $age = $familyMember->birthday ? Carbon::parse($familyMember->birthday)->age : null;
        
        // Get related beneficiary information
        $beneficiary = $familyMember->beneficiary;
        
        return view('familyPortal.accountProfile', compact('familyMember', 'formattedBirthday', 'memberSince', 'age', 'beneficiary'));
    }
    
    /**
     * Redirect to settings section of family profile
     */
    public function familySettings()
    {
        return redirect()->route('family.profile.index')->with('activeTab', 'settings');
    }
    
    
    /**
     * Update family member email
     */
    public function updateFamilyEmail(Request $request)
    {
        $request->validate([
            'account_email' => ['required', 'string', 'email', 'max:255', 'unique:family_members,email'],
            'current_password' => ['required'],
        ]);

        try {
            $familyMember = Auth::guard('family')->user();
            
            // Check current password
            if (!Hash::check($request->current_password, $familyMember->password)) {
                return back()
                    ->withErrors(['current_password' => 'The provided password does not match your current password.'])
                    ->with('activeTab', 'settings');
            }
            
            // Update the email
            $familyMember->email = $request->account_email;
            $familyMember->save();
            
            return redirect()->route('family.profile.index')
                ->with('success', 'Email updated successfully.')
                ->with('activeTab', 'settings');
        } catch (\Exception $e) {
            Log::error('Error updating family email: ' . $e->getMessage());
            return back()
                ->withErrors(['general' => 'An error occurred while updating your email.'])
                ->with('activeTab', 'settings');
        }
    }
    
    /**
     * Update family member password
     */
    public function updateFamilyPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'account_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        try {
            $familyMember = Auth::guard('family')->user();
            
            // Check current password
            if (!Hash::check($request->current_password, $familyMember->password)) {
                return back()
                    ->withErrors(['current_password' => 'The provided password does not match your current password.'])
                    ->with('activeTab', 'settings');
            }
            
            // Update the password
            $familyMember->password = Hash::make($request->account_password);
            $familyMember->save();
            
            return redirect()->route('family.profile.index')
                ->with('success', 'Password updated successfully.')
                ->with('activeTab', 'settings');
        } catch (\Exception $e) {
            Log::error('Error updating family password: ' . $e->getMessage());
            return back()
                ->withErrors(['general' => 'An error occurred while updating your password.'])
                ->with('activeTab', 'settings');
        }
    }

    /**
     * Update beneficiary password
     */
    public function updateBeneficiaryPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'account_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        try {
            $beneficiary = Auth::guard('beneficiary')->user();
            
            // Check current password
            if (!Hash::check($request->current_password, $beneficiary->password)) {
                return back()
                    ->withErrors(['current_password' => 'The provided password does not match your current password.'])
                    ->with('activeTab', 'settings');
            }
            
            // Update the password
            $beneficiary->password = Hash::make($request->account_password);
            $beneficiary->save();
            
            return redirect()->route('beneficiary.profile.index')
                ->with('success', 'Password updated successfully.')
                ->with('activeTab', 'settings');
        } catch (\Exception $e) {
            Log::error('Error updating beneficiary password: ' . $e->getMessage());
            return back()
                ->withErrors(['general' => 'An error occurred while updating your password.'])
                ->with('activeTab', 'settings');
        }
    }
}