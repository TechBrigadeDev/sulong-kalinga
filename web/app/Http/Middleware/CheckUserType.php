<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $userType
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $userType)
    {
        \Log::debug('CheckUserType middleware called:');
        \Log::debug('User type parameter: ' . $userType);
        
        switch ($userType) {
            case 'staff':
                if (!Auth::check()) {
                    return redirect('login');
                }
                break;
                
            case 'beneficiary':
                if (!Auth::guard('beneficiary')->check()) {
                    return redirect('login');
                }
                break;
                
            case 'family':
                if (!Auth::guard('family')->check()) {
                    return redirect('login');
                }
                break;
                
            default:
                return redirect('login');
        }
        
        return $next($request);
    }
}