<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (!$user || !in_array($user->role_id, self::roleNamesToIds($roles))) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
        return $next($request);
    }

    // Map role names to IDs
    protected static function roleNamesToIds($roles)
    {
        $map = [
            'admin' => 1,
            'care_manager' => 2,
            'care_worker' => 3,
            'beneficiary' => 4,
            'family_member' => 5,
        ];
        return array_map(fn($r) => $map[$r] ?? null, $roles);
    }
}
