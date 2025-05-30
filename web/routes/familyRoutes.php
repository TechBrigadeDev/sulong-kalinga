<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// All routes for family member portal
// These routes require authentication via the family guard
Route::middleware(['auth:family'])->prefix('family')->name('family.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        $showWelcome = session()->pull('show_welcome', false);
        $familyMember = Auth::guard('family')->user();
        $beneficiary = $familyMember->beneficiary;
        
        return view('familyPortal.homePage', [
            'showWelcome' => $showWelcome,
            'familyMember' => $familyMember,
            'beneficiary' => $beneficiary
        ]);
    })->name('dashboard');
    
    // Visitation Schedule
    Route::get('/visitation-schedule', function() {
        return view('familyPortal.visitationSchedule');
    })->name('visitation.schedule.index');
    
    // Medication Schedule
    Route::get('/medication-schedule', function() {
        return view('familyPortal.medicationSchedule');
    })->name('medication.schedule.index');
    
    // Emergency Service
    Route::get('/emergency-service', function() {
        return view('familyPortal.emergencyService');
    })->name('emergency.service.index');
    
    // Care Plan
    Route::get('/care-plan', function() {
        return view('familyPortal.carePlan');
    })->name('care.plan.index');
    
    // Family Members
    Route::get('/family-members', function() {
        return view('familyPortal.familyMembers');
    })->name('family.member.index');
});