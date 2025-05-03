<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\CheckRole;

// Include route files for role-specific routes
require __DIR__.'/adminRoutes.php';
require __DIR__.'/careManagerRoutes.php';
require __DIR__.'/careWorkerRoutes.php';

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/forgot-password', function () {
    return view('forgot-password');
})->name('forgotPass');

// Dashboard routes by role
Route::get('/admin/dashboard', function () {
    if (auth()->user()?->role_id == 1) {  // Allow ALL users with role_id=1
        $showWelcome = session()->pull('show_welcome', false);
        \Log::debug('Admin dashboard accessed by user', [
            'user_id' => auth()->id(),
            'role_id' => auth()->user()->role_id,
            'org_role_id' => auth()->user()->organization_role_id
        ]);
        return view('admin.admindashboard', ['showWelcome' => $showWelcome]);
    }
    abort(403, 'Only administrators can access this page');
})->middleware('auth')->name('admin.dashboard');  // Also fixed the route name

Route::get('/manager/dashboard', function () {
    if (auth()->user()?->isCareManager()) {
        $showWelcome = session()->pull('show_welcome', false);
        return view('careManager.managerdashboard', ['showWelcome' => $showWelcome]);
    }
    abort(403);
})->middleware('auth')->name('managerdashboard');

Route::get('/worker/dashboard', function () {
    $showWelcome = session()->pull('show_welcome', false);
    if (auth()->user()?->isCareWorker()) {
        return view('careWorker.workerdashboard', ['showWelcome' => $showWelcome]);
    }
    abort(403);
})->middleware('auth')->name('workerdashboard');

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
    