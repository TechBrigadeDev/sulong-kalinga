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
    
    // Visitation Schedule
    Route::get('/visitation-schedule', function() {
        return view('beneficiaryPortal.visitationSchedule');
    })->name('visitation.schedule.index');
    
    // Medication Schedule
    Route::get('/medication-schedule', function() {
        return view('beneficiaryPortal.medicationSchedule');
    })->name('medication.schedule.index');
    
    // Emergency Service
    Route::get('/emergency-service', function() {
        return view('beneficiaryPortal.emergencyAndService');
    })->name('emergency.service.index');
    
    // Messages
    Route::get('/messages', function() {
        return view('beneficiaryPortal.messages');
    })->name('messages.index');
    
    // Profile
    Route::get('/profile', function() {
        return view('beneficiaryPortal.profile');
    })->name('profile.index');

    // Family Members
    Route::get('/family-members', function() {
        return view('beneficiaryPortal.familyMembers');
    })->name('member.index');

    // 
    Route::get('/faq', function() {
        return view('beneficiaryPortal.FAQuestions');
    })->name('faQuestions.index');
});