<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\LanguageController;


// Include route files for role-specific routes
require __DIR__.'/adminRoutes.php';
require __DIR__.'/careManagerRoutes.php';
require __DIR__.'/careWorkerRoutes.php';
require __DIR__.'/beneficiaryRoutes.php';  // Add beneficiary routes
require __DIR__.'/familyRoutes.php';       // Add family routes

Route::post('/toggle-language', [LanguageController::class, 'toggle'])
    ->name('toggle-language')
    ->middleware('web');
    
// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->middleware('throttle:10,1');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/forgot-password', function () {
    return view('forgot-password');
})->name('forgotPass');

// Session Checking for Family and Beneficiary
Route::get('/session-check', function () {
    return response()->json([
        'authenticated' => Auth::check() || Auth::guard('beneficiary')->check() || Auth::guard('family')->check(),
        'beneficiary_auth' => Auth::guard('beneficiary')->check(),
        'beneficiary_id' => Auth::guard('beneficiary')->check() ? Auth::guard('beneficiary')->id() : null,
        'family_auth' => Auth::guard('family')->check(),
        'family_id' => Auth::guard('family')->check() ? Auth::guard('family')->id() : null,
        'session_id' => session()->getId(),
        'csrf' => csrf_token()
    ]);
});

// Dashboard routes by role (for staff users)
Route::get('/admin/dashboard', function () {
    if (auth()->user()?->role_id == 1) {
        \Log::debug('Admin dashboard accessed by user', [
            'user_id' => auth()->id(),
            'role_id' => auth()->user()->role_id,
            'org_role_id' => auth()->user()->organization_role_id
        ]);
        // Use the controller instead of directly returning a view
        return app()->make('App\Http\Controllers\DashboardController')->adminDashboard();
    }
    abort(403, 'Only administrators can access this page');
})->middleware('auth')->name('admin.dashboard');

Route::get('/manager/dashboard', function () {
    if (auth()->user()?->isCareManager()) {
        \Log::debug('Care Manager dashboard accessed by user', [
            'user_id' => auth()->id(),
            'role_id' => auth()->user()->role_id
        ]);
        // Use the controller instead of directly returning a view
        return app()->make('App\Http\Controllers\DashboardController')->careManagerDashboard();
    }
    abort(403, 'Only care managers can access this page');
})->middleware('auth')->name('care-manager.dashboard');

Route::get('/worker/dashboard', function () {
    if (auth()->user()?->isCareWorker()) {
        \Log::debug('Care Worker dashboard accessed by user', [
            'user_id' => auth()->id(),
            'role_id' => auth()->user()->role_id
        ]);
        // Use the controller instead of directly returning a view
        return app()->make('App\Http\Controllers\DashboardController')->careWorkerDashboard();
    }
    abort(403, 'Only care workers can access this page');
})->middleware('auth')->name('care-worker.dashboard'); // Changed to match naming pattern

Route::get('/family/homePage', function () {
    $showWelcome = session()->pull('show_welcome', false);
    return view('familyPortal.homePage', ['showWelcome' => $showWelcome]);
})->middleware('auth')->name('familyPortalHomePage');

// Public website routes
Route::get('/', function () {
    return view('publicWeb.landing');
})->name('landing');

Route::get('/contactUs', function () {
    return view('publicWeb.contactUs');
})->name('contactUs');

Route::get('/donor', function () {
    return view('publicWeb.donor');
})->name('donor');

Route::get('/aboutUs', function () {
    return view('publicWeb.aboutUs');
})->name('aboutUs');

Route::get('/milestones', function () {
    return view('publicWeb.milestones');
})->name('milestones');

Route::get('/updates', function () {
    return view('publicWeb.updates');
})->name('updates');

Route::get('/events', function () {
    return view('publicWeb.events');
})->name('events');

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])
    ->name('password.request');
    
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->name('password.email');
    
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');
    
Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->name('password.update');

// Health check endpoint
Route::get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});