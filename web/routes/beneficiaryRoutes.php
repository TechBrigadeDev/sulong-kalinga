<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// All routes for beneficiary portal
// These routes require authentication via the beneficiary guard
Route::middleware(['auth:beneficiary'])->prefix('beneficiary')->name('beneficiary.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        $showWelcome = session()->pull('show_welcome', false);
        $beneficiary = Auth::guard('beneficiary')->user();
        
        return view('beneficiaryPortal.homePage', [
            'showWelcome' => $showWelcome,
            'beneficiary' => $beneficiary
        ]);
    })->name('dashboard');
    
    // Care plan
    Route::get('/care-plan', function() {
        return view('beneficiaryPortal.carePlan');
    })->name('care.plan.index');
    
    // Schedule
    Route::get('/schedule', function() {
        return view('beneficiaryPortal.schedule');
    })->name('schedule.index');
    
    // Messages
    Route::get('/messages', function() {
        return view('beneficiaryPortal.messages');
    })->name('messages.index');
    
    // Profile
    Route::get('/profile', function() {
        return view('beneficiaryPortal.profile');
    })->name('profile.index');
    
    // Emergency contact
    Route::get('/emergency', function() {
        return view('beneficiaryPortal.emergency');
    })->name('emergency.index');
});