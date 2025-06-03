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
use App\Http\Controllers\PortalVisitationScheduleController;
use App\Http\Controllers\PortalMedicationScheduleController;

// All routes for beneficiary portal
// These routes require authentication via the beneficiary guard
Route::middleware(['auth:beneficiary'])->prefix('beneficiary')->name('beneficiary.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        $showWelcome = session()->pull('show_welcome', false);
        $beneficiary = Auth::guard('beneficiary')->user();
        
        // Get the next upcoming visit
        $nextVisit = app()->make(\App\Http\Controllers\PortalVisitationScheduleController::class)
            ->getNextVisit();
        
        return view('beneficiaryPortal.homePage', [
            'showWelcome' => $showWelcome,
            'beneficiary' => $beneficiary,
            'nextVisit' => $nextVisit
        ]);
    })->name('dashboard');
    
    // Care plan
    Route::prefix('care-plan')->name('care.plan.')->group(function () {
        Route::get('/', [FamilyPortalCarePlanController::class, 'index'])->name('index');
        Route::get('/allCareplans', [FamilyPortalCarePlanController::class, 'allCarePlans'])->name('allCarePlans');
    });
    
    // Visitation Schedule
    Route::prefix('/visitation-schedule')->name('visitation.schedule.')->group(function () {
        Route::get('/', [PortalVisitationScheduleController::class, 'index'])->name('index');
        Route::get('/events', [PortalVisitationScheduleController::class, 'getEvents'])->name('events');
        Route::get('/details/{id}', [PortalVisitationScheduleController::class, 'getOccurrenceDetails'])->name('details');
        Route::get('/upcoming', [PortalVisitationScheduleController::class, 'getUpcomingVisits'])->name('upcoming');
    });
    
    // Medication Schedule
    Route::get('/medication-schedule', [PortalMedicationScheduleController::class, 'index'])->name('medication.schedule.index');
    
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