<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\LanguagePreference;
use Symfony\Component\HttpFoundation\Response;

class CheckLanguagePreference
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $useTagalog = false; // Default language is English
            
            // Get authenticated user info from the appropriate guard
            $userInfo = $this->getUserInfo();
            
            // Check database preference if user is logged in
            if ($userInfo) {
                $preference = LanguagePreference::where('user_type', $userInfo['type'])
                                            ->where('user_id', $userInfo['id'])
                                            ->exists();
                
                $useTagalog = $preference;
            } 
            // Fall back to cookie for guests or when no DB preference
            else {
                $useTagalog = $request->cookie('use_tagalog') === '1';
            }
            
            // Share the language preference with all views
            View::share('useTagalog', $useTagalog);
        } catch (\Exception $e) {
            // Log the error but don't break the application
            \Log::error('Error in CheckLanguagePreference middleware: ' . $e->getMessage());
            View::share('useTagalog', false); // Default to English if there's an error
        }
        
        return $next($request);
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