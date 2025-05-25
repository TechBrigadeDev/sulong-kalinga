<?php

use Illuminate\Support\Facades\Route;
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



require_once __DIR__.'/routeHelpers.php';

Route::middleware(['auth',])->prefix('family')->name('family.')->group(function () {

    //Home Page
    Route::get('/family/homePage', function () {
        $showWelcome = session()->pull('show_welcome', false);
        return view('familyPortal.homePage', ['showWelcome' => $showWelcome]);
    })->name('familyPortalHomePage');

    // Visitation Schedule
    Route::prefix('visitation-schedule')->name('visitation.schedule.')->group(function () {
        Route::get('/', [FamilyPortalVisitationScheduleController::class, 'index'])->name('index');
    });

    // Mediction Schedule
    Route::prefix('medication-schedule')->name('medication.schedule.')->group(function () {
        Route::get('/', [FamilyPortalMedicationScheduleController::class, 'index'])->name('index');
    });

    // Emergency and Service Requests
    Route::prefix('emergency-service')->name('emergency.service.')->group(function () {
        Route::get('/', [FamilyPortalEmergencyServiceRequestController::class, 'index'])->name('index');
    });
    
    // Care Plan
    Route::prefix('care-plan')->name('care.plan.')->group(function () {
        Route::get('/', [FamilyPortalCarePlanController::class, 'index'])->name('index');
    });

    // Family Member Management
    Route::prefix('family-member')->name('family.member.')->group(function () {
        Route::get('/', [FamilyPortalFamilyMemberController::class, 'index'])->name('index');
    });
    
    // FAQ
    Route::prefix('faQuestions')->name('faQuestions.')->group(function () {
        Route::get('/', [FamilyPortalFAQuestionsController::class, 'index'])->name('index');
    });

});


