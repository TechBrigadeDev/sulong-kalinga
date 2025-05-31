<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 
use App\Models\Beneficiary;
use App\Models\FamilyMember;
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
        $userType = $request->input('user_type', 'staff');

        // Configurable values
        $maxAttempts = (int) (env('LOGIN_ATTEMPT_LIMIT', 5));
        $decayMinutes = (int) (env('LOGIN_ATTEMPT_WINDOW', 10));
        $lockoutMinutes = (int) (env('LOGIN_LOCKOUT_DURATION', 10));

        $email = strtolower($request->input('email'));
        
        // Generate type-specific cache keys
        $cacheKeyPrefix = "login_attempts:{$userType}:{$email}:{$ip}";
        $cacheKeyAttempts = "{$cacheKeyPrefix}:attempts";
        $cacheKeyLockout = "{$cacheKeyPrefix}:lockout";

        // Check for lockout
        if (Cache::has($cacheKeyLockout)) {
            $lockoutExpires = Cache::get($cacheKeyLockout);
            $remaining = max(1, ceil(now()->diffInMinutes(Carbon::parse($lockoutExpires), false)));
            $message = "Too many failed attempts. Please try again in {$remaining} minute(s).";
            $this->logService->createLog(
                $userType === 'staff' ? 'user' : $userType,
                null,
                LogType::VIEW,
                "Lockout: {$email} attempted login during lockout.",
                null
            );
            return redirect()->back()->withErrors(['email' => $message])->withInput();
        }

        // Validate the input data
        $validationRules = [
            'password' => 'required|string|min:8',
        ];
        
        if ($userType === 'beneficiary') {
            $validationRules['email'] = 'required|string'; // Username for beneficiaries
        } else {
            $validationRules['email'] = 'required|email';  // Email for staff and family
        }
        
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $authSuccess = false;

        // Try authentication based on user type
        switch ($userType) {
            case 'staff':
                // Existing COSE users logic
                $user = \DB::table('cose_users')
                    ->where('email', $email)
                    ->first();

                if ($user && Hash::check($request->input('password'), $user->password)) {
                    // Check if the user status is active
                    if ($user->status !== 'Active') {
                        // Log the attempt
                        $this->logService->createLog(
                            'user',
                            $user->id,
                            LogType::VIEW,
                            "Login denied: User account '{$email}' is not active."
                        );
                        
                        return redirect()->back()->withErrors(['email' => 'Your account is not active. Please contact the administrator.'])->withInput();
                    }
                    
                    // Successful login: clear attempts and lockout
                    Cache::forget($cacheKeyAttempts);
                    Cache::forget($cacheKeyLockout);

                    $userModel = User::find($user->id);

                    if (!$userModel) {
                        return redirect()->back()->withErrors(['email' => 'User account issue. Please contact support.']);
                    }

                    Auth::login($userModel);
                    session(['user_type' => 'staff']);

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
                    
                    $authSuccess = true;
                }
                break;
                
            case 'beneficiary':
                // First check if the beneficiary exists and is active
                $beneficiary = Beneficiary::where('username', $request->input('email'))->first();
                
                if ($beneficiary) {
                    if ($beneficiary->beneficiary_status_id !== 1) {
                        // Log the attempt
                        $this->logService->createLog(
                            'beneficiary',
                            $beneficiary->beneficiary_id,
                            LogType::VIEW,
                            "Login denied: Beneficiary account '{$request->input('email')}' is not active."
                        );
                        
                        return redirect()->back()->withErrors(['email' => 'Your beneficiary account is currently inactive. Please contact your care provider.'])->withInput();
                    }
                }
                
                // Try authenticate beneficiary
                $credentials = [
                    'username' => $request->input('email'),
                    'password' => $request->input('password')
                ];
                
                if (Auth::guard('beneficiary')->attempt($credentials)) {
                    // Successful login: clear attempts and lockout
                    Cache::forget($cacheKeyAttempts);
                    Cache::forget($cacheKeyLockout);
                    
                    $beneficiary = Auth::guard('beneficiary')->user();
                    $fullName = $beneficiary->first_name . ' ' . $beneficiary->last_name;
                    
                    $this->logService->createLog(
                        'beneficiary',
                        $beneficiary->beneficiary_id,
                        LogType::VIEW,
                        $fullName . ' (beneficiary) logged in.',
                        $beneficiary->beneficiary_id
                    );
                    
                    session(['user_type' => 'beneficiary']);
                    session()->put('show_welcome', true);
                    
                    return redirect()->route('beneficiary.dashboard');
                    $authSuccess = true;
                }
                break;
                
            case 'family':
                // Try to find the family member
                $familyMember = FamilyMember::where('email', $email)->first();
                
                if ($familyMember) {
                    // Get the related beneficiary to check status
                    $relatedBeneficiary = Beneficiary::find($familyMember->related_beneficiary_id);
                    
                    if (!$relatedBeneficiary || $relatedBeneficiary->beneficiary_status_id !== 1) {
                        // Log the attempt
                        $this->logService->createLog(
                            'family_member',
                            $familyMember->family_member_id,
                            LogType::VIEW,
                            "Login denied: Related beneficiary account for family member '{$email}' is not active."
                        );
                        
                        return redirect()->back()->withErrors(['email' => 'Access denied. The beneficiary account associated with your profile is currently inactive.'])->withInput();
                    }
                }
                
                // Try authenticate family member
                $credentials = [
                    'email' => $email,
                    'password' => $request->input('password')
                ];
                
                if (Auth::guard('family')->attempt($credentials)) {
                    // Successful login: clear attempts and lockout
                    Cache::forget($cacheKeyAttempts);
                    Cache::forget($cacheKeyLockout);
                    
                    $familyMember = Auth::guard('family')->user();
                    $fullName = $familyMember->first_name . ' ' . $familyMember->last_name;
                    
                    $this->logService->createLog(
                        'family_member',
                        $familyMember->family_member_id,
                        LogType::VIEW,
                        $fullName . ' (family) logged in.',
                        $familyMember->family_member_id
                    );
                    
                    session(['user_type' => 'family']);
                    session()->put('show_welcome', true);
                    
                    return redirect()->route('family.dashboard');
                    $authSuccess = true;
                }
                break;
        }

        // If we get here, authentication failed
        if (!$authSuccess) {
            // Failed login: increment attempts
            $attempts = Cache::get($cacheKeyAttempts, 0) + 1;
            Cache::put($cacheKeyAttempts, $attempts, now()->addMinutes($decayMinutes));

            $this->logService->createLog(
                $userType === 'staff' ? 'user' : $userType,
                null,
                LogType::VIEW,
                "Failed login attempt for {$email} ({$attempts}/{$maxAttempts})",
                null
            );

            if ($attempts >= $maxAttempts) {
                $lockoutUntil = now()->addMinutes($lockoutMinutes);
                Cache::put($cacheKeyLockout, $lockoutUntil, $lockoutUntil);
                
                $this->logService->createLog(
                    $userType === 'staff' ? 'user' : $userType,
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

        return redirect()->route('login');
    }

    // Handle logout (rest of the file unchanged)
    public function logout()
    {
        // Get the correct guard based on user type
        $userType = session('user_type', 'staff');
        $userId = null;
        $entityType = 'user';
        $logName = 'Unknown User';

        switch ($userType) {
            case 'staff':
                $user = Auth::user();
                if ($user) {
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
                }
                Auth::logout();
                break;
                
            case 'beneficiary':
                $beneficiary = Auth::guard('beneficiary')->user();
                if ($beneficiary) {
                    $userId = $beneficiary->beneficiary_id;
                    $entityType = 'beneficiary';
                    $logName = $beneficiary->first_name . ' ' . $beneficiary->last_name . ' (beneficiary)';
                }
                Auth::guard('beneficiary')->logout();
                break;
                
            case 'family':
                $familyMember = Auth::guard('family')->user();
                if ($familyMember) {
                    $userId = $familyMember->family_member_id;
                    $entityType = 'family_member';
                    $logName = $familyMember->first_name . ' ' . $familyMember->last_name . ' (family)';
                }
                Auth::guard('family')->logout();
                break;
        }

        // Log the logout if we have a user ID
        if ($userId) {
            $this->logService->createLog(
                $entityType,
                $userId,
                LogType::VIEW,
                $logName . ' logged out.',
                $userId
            );
        }

        // Clear all session values
        session()->flush();

        return redirect()->route('login');
    }
}