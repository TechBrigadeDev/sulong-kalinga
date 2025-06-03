<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\WeeklyCareController;
use App\Http\Controllers\SchedulesAndAppointmentsController;
use App\Http\Controllers\ViewAccountProfileController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\HealthMonitoringController;
use App\Http\Controllers\VisitationController;
use App\Http\Controllers\MedicationScheduleController;
use App\Http\Controllers\FamilyPortalVisitationScheduleController;
use App\Http\Controllers\FamilyPortalMedicationScheduleController;
use App\Http\Controllers\FamilyPortalEmergencyServiceRequestController;
use App\Http\Controllers\FamilyPortalCarePlanController;
use App\Http\Controllers\FamilyPortalFamilyMemberController;
use App\Http\Controllers\FamilyPortalFAQuestionsController;

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
        return view('familyPortal.emergencyAndService');
    })->name('emergency.service.index');
    
    // Care Plan
    Route::prefix('care-plan')->name('care.plan.')->group(function () {
        Route::get('/', [FamilyPortalCarePlanController::class, 'index'])->name('index');
        Route::get('/allCareplans', [FamilyPortalCarePlanController::class, 'allCarePlans'])->name('allCarePlans');
    });
    
    // Family Members
    Route::get('/family-members', function() {
        return view('familyPortal.familyMembers');
    })->name('member.index');

    // FAQ Section
    Route::get('/faq', function() {
        return view('familyPortal.FAQuestions');
    })->name('faQuestions.index');
});