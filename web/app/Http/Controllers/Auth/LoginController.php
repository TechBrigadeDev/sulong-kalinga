<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 
use App\Models\PortalAccount; 
use Illuminate\Support\Facades\Validator;
use App\Services\LogService;
use App\Enums\LogType;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login submission
    public function login(Request $request)
    {
        $ip = $request->ip();

        // Configurable values
        $maxAttempts = (int) (env('LOGIN_ATTEMPT_LIMIT', 5));
        $decayMinutes = (int) (env('LOGIN_ATTEMPT_WINDOW', 10));
        $lockoutMinutes = (int) (env('LOGIN_LOCKOUT_DURATION', 10));

        $email = strtolower($request->input('email'));

        // Per-device/IP cache keys
        $cacheKeyAttempts = 'login_attempts:' . $email . ':' . $ip;
        $cacheKeyLockout = 'login_lockout:' . $email . ':' . $ip;
        $portalCacheKeyAttempts = 'login_attempts:portal:' . $email . ':' . $ip;
        $portalCacheKeyLockout = 'login_lockout:portal:' . $email . ':' . $ip;

        // Check for lockout (COSE)
        if (Cache::has($cacheKeyLockout)) {
            $lockoutExpires = Cache::get($cacheKeyLockout);
            $remaining = max(1, ceil(now()->diffInMinutes(Carbon::parse($lockoutExpires), false)));
            $message = "Too many failed attempts. Please try again in {$remaining} minute(s).";
            $this->logService->createLog(
                'user',
                null,
                LogType::VIEW,
                "Lockout: {$email} attempted login during lockout.",
                null
            );
            return redirect()->back()->withErrors(['email' => $message])->withInput();
        }

        // Validate the input data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check in the cose_users table
        $user = \DB::table('cose_users')
            ->where('email', $email)
            ->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            // Successful login: clear attempts and lockout
            Cache::forget($cacheKeyAttempts);
            Cache::forget($cacheKeyLockout);

            $userModel = User::find($user->id);

            if (!$userModel) {
                return redirect()->back()->withErrors(['email' => 'User account issue. Please contact support.']);
            }

            Auth::login($userModel);
            session(['user_type' => 'cose']);

            $entityType = 'user';
            if ($userModel && isset($userModel->role_id)) {
                if ($userModel->role_id == 1) {
                    $entityType = 'administrator';
                } elseif ($userModel->role_id == 2) {
                    $entityType = 'care_manager';
                } elseif ($userModel->role_id == 3) {
                    $entityType = 'care_worker';
                }
            }

            $fullName = $userModel->first_name . ' ' . $userModel->last_name;
            $this->logService->createLog(
                $entityType,
                $userModel->id,
                LogType::VIEW,
                $fullName . ' logged in.',
                $userModel->id
            );

            session(['user_type' => 'cose']);

            if ($user->role_id == 1) {
                session()->put('show_welcome', true);
                \Log::debug('Admin login, about to redirect to dashboard', [
                    'user_id' => $user->id,
                    'role_id' => $user->role_id,
                    'org_role_id' => $user->organization_role_id,
                    'current_url' => $request->fullUrl()
                ]);
                return redirect('/admin/dashboard')->with('success', 'Welcome, Admin!');
            }
            if ($user->role_id == 2) {
                session()->put('show_welcome', true);
                return redirect()->route('care-manager.dashboard');
            }
            if ($user->role_id == 3) {
                session()->put('show_welcome', true);
                return redirect()->route('workerdashboard');
            }
        } else {
            // Failed login: increment attempts
            $attempts = Cache::get($cacheKeyAttempts, 0) + 1;
            Cache::put($cacheKeyAttempts, $attempts, now()->addMinutes($decayMinutes));

            $this->logService->createLog(
                'user',
                null,
                LogType::VIEW,
                "Failed login attempt for {$email} ({$attempts}/{$maxAttempts})",
                null
            );

            if ($attempts >= $maxAttempts) {
                $lockoutUntil = now()->addMinutes($lockoutMinutes);
                Cache::put($cacheKeyLockout, $lockoutUntil, $lockoutUntil);
                $this->logService->createLog(
                    'user',
                    null,
                    LogType::VIEW,
                    "Lockout: {$email} locked out for {$lockoutMinutes} minutes.",
                    null
                );
                $message = "Too many failed attempts. Please try again in {$lockoutMinutes} minute(s).";
                return redirect()->back()->withErrors(['email' => $message])->withInput();
            }

            return redirect()->back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        // Check for lockout (portal_accounts)
        if (Cache::has($portalCacheKeyLockout)) {
            $lockoutExpires = Cache::get($portalCacheKeyLockout);
            $remaining = max(1, ceil(now()->diffInMinutes(Carbon::parse($lockoutExpires), false)));
            $message = "Too many failed attempts. Please try again in {$remaining} minute(s).";
            $this->logService->createLog(
                'family_member',
                null,
                LogType::VIEW,
                "Lockout: {$email} attempted portal login during lockout.",
                null
            );
            return redirect()->back()->withErrors(['email' => $message])->withInput();
        }

        // If not found in cose_users, check in the portal_accounts table
        $user = \DB::table('portal_accounts')
            ->where('portal_email', $request->input('email'))
            ->first();

        if ($user && Hash::check($request->input('password'), $user->portal_password)) {
            // Successful login: clear attempts and lockout
            Cache::forget($portalCacheKeyAttempts);
            Cache::forget($portalCacheKeyLockout);

            Auth::loginUsingId($user->id);
            session([
                'user_type' => 'family',
                'portal_user_id' => $user->id,
                'portal_user_name' => $user->portal_name,
                'portal_user_email' => $user->portal_email
            ]);

            $entityType = 'family_member';
            $this->logService->createLog(
                $entityType,
                $user->id,
                LogType::VIEW,
                $user->portal_email . ' logged in.',
                $user->id
            );

            return redirect()->route('landing');
        } else {
            // Failed login: increment attempts
            $attempts = Cache::get($portalCacheKeyAttempts, 0) + 1;
            Cache::put($portalCacheKeyAttempts, $attempts, now()->addMinutes($decayMinutes));

            $this->logService->createLog(
                'family_member',
                null,
                LogType::VIEW,
                "Failed portal login attempt for {$email} ({$attempts}/{$maxAttempts})",
                null
            );

            if ($attempts >= $maxAttempts) {
                $lockoutUntil = now()->addMinutes($lockoutMinutes);
                Cache::put($portalCacheKeyLockout, $lockoutUntil, $lockoutUntil);
                $this->logService->createLog(
                    'family_member',
                    null,
                    LogType::VIEW,
                    "Lockout: {$email} locked out of portal for {$lockoutMinutes} minutes.",
                    null
                );
                $message = "Too many failed attempts. Please try again in {$lockoutMinutes} minute(s).";
                return redirect()->back()->withErrors(['email' => $message])->withInput();
            }

            return redirect()->back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        return redirect()->back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    // Handle logout
    public function logout()
    {
        $user = Auth::user();

        // If user is null, try to get info from session (for portal_accounts)
        if (!$user && session('user_type') === 'family') {
            $userId = session('portal_user_id');
            $userEmail = session('portal_user_email', 'Unknown Family User');
            $entityType = 'family_member';
            $logName = $userEmail;
        } elseif ($user) {
            $userId = $user->id;
            $entityType = 'user';
            if (isset($user->role_id)) {
                if ($user->role_id == 1) {
                    $entityType = 'administrator';
                } elseif ($user->role_id == 2) {
                    $entityType = 'care_manager';
                } elseif ($user->role_id == 3) {
                    $entityType = 'care_worker';
                }
            }
            $logName = $user->first_name . ' ' . $user->last_name;
        } else {
            $userId = null;
            $entityType = 'user';
            $logName = 'Unknown User';
        }

        Auth::logout();

        if ($userId) {
            $this->logService->createLog(
                $entityType,
                $userId,
                LogType::VIEW,
                $logName . ' logged out.',
                $userId
            );
        }

        // Clear session values
        session()->forget(['portal_user_id', 'portal_user_name', 'portal_user_email', 'user_type']);

        return redirect()->route('login');
    }
}
