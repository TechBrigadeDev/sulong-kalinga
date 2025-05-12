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
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // If validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check in the cose_users table
        $user = \DB::table('cose_users')
            ->where('email', $request->input('email'))
            ->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            // Create a proper user model instance
            $userModel = User::find($user->id);

            if (!$userModel) {
                return redirect()->back()->withErrors(['email' => 'User account issue. Please contact support.']);
            }

            // Login properly with the model
            Auth::login($userModel);
            session(['user_type' => 'cose']);

            // Determine entity type based on role
            $entityType = 'user';
            if ($userModel && isset($userModel->role_id)) {
                if ($userModel->role_id == 1) {
                    $entityType = 'administrator';
                } elseif ($userModel->role_id == 2) {
                    $entityType = 'care_manager';
                } elseif ($userModel->role_id == 3) {
                    $entityType = 'care_worker';
                }
                // Add more roles as needed
            }

            // Log the login with full name
            $fullName = $userModel->first_name . ' ' . $userModel->last_name;
            $this->logService->createLog(
                $entityType,
                $userModel->id,
                LogType::VIEW,
                $fullName . ' logged in.',
                $userModel->id
            );

            session(['user_type' => 'cose']); // Store user type in session
        
            if ($user->role_id == 1) {
                session()->put('show_welcome', true);
                
                // DEBUG the redirect issue
                \Log::debug('Admin login, about to redirect to dashboard', [
                    'user_id' => $user->id,
                    'role_id' => $user->role_id,
                    'org_role_id' => $user->organization_role_id,
                    'current_url' => $request->fullUrl()
                ]);
                
                // Force a direct redirect to the dashboard URL
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
    }

    // If not found in cose_users, check in the portal_accounts table
    $user = \DB::table('portal_accounts')
                    ->where('portal_email', $request->input('email'))
                    ->first();

    if ($user && Hash::check($request->input('password'), $user->portal_password)) {
        Auth::loginUsingId($user->id);
        session([
            'user_type' => 'family',
            'portal_user_id' => $user->id,
            'portal_user_name' => $user->portal_name,
            'portal_user_email' => $user->portal_email
        ]);

        // Log the login with portal_email
        $entityType = 'family_member'; // or another appropriate type for portal accounts
        $this->logService->createLog(
            $entityType,
            $user->id,
            LogType::VIEW,
            $user->portal_email . ' logged in.',
            $user->id
        );

        return redirect()->route('landing');
    }

    // If no user is found in either table, return an error
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
