<?php

namespace App\Http\Controllers;

use App\Models\LanguagePreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    /**
     * Toggle language preference
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request)
    {
        $useTagalog = filter_var($request->input('use_tagalog', false), FILTER_VALIDATE_BOOLEAN);
        
        // Get user information from the appropriate guard
        $userInfo = $this->getUserInfo();
        
        // If the user is logged in (with any guard), update their preference in the database
        if ($userInfo) {
            if ($useTagalog) {
                // Create a preference for Tagalog
                LanguagePreference::updateOrCreate(
                    ['user_type' => $userInfo['type'], 'user_id' => $userInfo['id']]
                );
            } else {
                // Delete preference row if switching to English (default)
                LanguagePreference::where('user_type', $userInfo['type'])
                    ->where('user_id', $userInfo['id'])
                    ->delete();
            }
        }
        
        // Set cookie for guests and to persist between sessions
        Cookie::queue('use_tagalog', $useTagalog ? '1' : '0', 60*24*30); // 30 days
        
        return response()->json(['success' => true, 'use_tagalog' => $useTagalog]);
    }
    
    /**
     * Get current user information from the appropriate guard
     * 
     * @return array|null
     */
    private function getUserInfo()
    {
        if (Auth::guard('web')->check()) {
            // Staff users (admin, care manager, care worker)
            return [
                'type' => 'cose_user',
                'id' => Auth::guard('web')->id()
            ];
        } 
        
        if (Auth::guard('beneficiary')->check()) {
            return [
                'type' => 'beneficiary',
                'id' => Auth::guard('beneficiary')->user()->beneficiary_id
            ];
        } 
        
        if (Auth::guard('family')->check()) {
            return [
                'type' => 'family_member',
                'id' => Auth::guard('family')->user()->family_member_id
            ];
        }
        
        return null;
    }
}