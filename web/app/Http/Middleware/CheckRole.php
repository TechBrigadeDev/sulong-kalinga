<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Add debugging
        \Log::debug('CheckRole middleware called:');
        \Log::debug('Role parameter: ' . $role);
        \Log::debug('User role_id: ' . Auth::user()->role_id);
        \Log::debug('User organization_role_id: ' . Auth::user()->organization_role_id);
        \Log::debug('Request path: ' . $request->path());
        
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        // Check for admin routes - ALL users with role_id=1 should be allowed 
        // regardless of organization_role_id
        if (($role === 'admin' || $role === 'administrator')) {
            // First check if user is an admin (role_id=1)
            if ($user->role_id == 1) {
                \Log::debug('Admin access GRANTED for user', [
                    'user_id' => $user->id,
                    'role_id' => $user->role_id,
                    'org_role_id' => $user->organization_role_id,
                    'requested_role' => $role
                ]);
                return $next($request);
            } else {
                \Log::debug('Admin access DENIED - User is not an admin (role_id is not 1)');
            }
        }
        
        // Care manager check
        if ($role === 'care_manager' && $user->role_id == 2) {
            return $next($request);
        }
        
        // Care worker check
        if ($role === 'care_worker' && $user->role_id == 3) {
            return $next($request);
        }
        
        // If we get here, the user doesn't have the required role
        \Log::debug('Access denied - user does not have required role');
        
        // Return a clearer error message for debugging purposes
        return response()->view('errors.403', [
            'message' => 'Permission denied. Required role: ' . $role . 
                    ', Your role_id: ' . $user->role_id . 
                    ', Your org_role_id: ' . $user->organization_role_id
        ], 403);
    }
}