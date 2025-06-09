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
use App\Http\Controllers\PortalAccountProfileController;
use App\Http\Controllers\PortalMessagingController;
use App\Http\Controllers\PortalNotificationsController;
use App\Http\Controllers\PortalFamilyMembersController;

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
        
        // Get the next upcoming medication
        $nextMedication = app()->make(\App\Http\Controllers\PortalMedicationScheduleController::class)
            ->getNextMedication();
        
        return view('beneficiaryPortal.homePage', [
            'showWelcome' => $showWelcome,
            'beneficiary' => $beneficiary,
            'nextVisit' => $nextVisit,
            'nextMedication' => $nextMedication
        ]);
    })->name('dashboard');
    
    // Care plan
    Route::prefix('care-plan')->name('care.plan.')->group(function () {
        Route::get('/', [FamilyPortalCarePlanController::class, 'index'])->name('index');
        Route::get('/statistics', [FamilyPortalCarePlanController::class, 'statistics'])->name('allCarePlans');
        Route::get('/view/{id}', [FamilyPortalCarePlanController::class, 'viewWeeklyCarePlan'])->name('view');
        Route::post('/acknowledge/{id}', [FamilyPortalCarePlanController::class, 'acknowledgeWeeklyCarePlan'])->name('acknowledge');
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
    
    // Emergency Service routes (these should already be present)
    Route::prefix('/emergency-service')->name('emergency.service.')->group(function () {
        Route::get('/', [FamilyPortalEmergencyServiceRequestController::class, 'index'])->name('index');
        Route::post('/emergency/submit', [FamilyPortalEmergencyServiceRequestController::class, 'submitEmergencyRequest'])->name('submit-emergency');
        Route::post('/service/submit', [FamilyPortalEmergencyServiceRequestController::class, 'submitServiceRequest'])->name('submit-service');
        Route::get('/active', [FamilyPortalEmergencyServiceRequestController::class, 'getActiveRequests'])->name('active');
        Route::post('/history', [FamilyPortalEmergencyServiceRequestController::class, 'getRequestHistory'])->name('history');
        Route::post('/cancel', [FamilyPortalEmergencyServiceRequestController::class, 'cancelRequest'])->name('cancel');
        Route::get('/emergency/{id}', [FamilyPortalEmergencyServiceRequestController::class, 'getEmergencyDetails'])->name('emergency-details');
        Route::get('/service/{id}', [FamilyPortalEmergencyServiceRequestController::class, 'getServiceDetails'])->name('service-details');
        Route::post('/emergency/update', [FamilyPortalEmergencyServiceRequestController::class, 'updateEmergencyRequest'])->name('update-emergency');
        Route::post('/service/update', [FamilyPortalEmergencyServiceRequestController::class, 'updateServiceRequest'])->name('update-service');
    });
    
    // Messages
    Route::prefix('messaging')->name('messaging.')->group(function () {
        Route::get('/', [PortalMessagingController::class, 'index'])->name('index');
        Route::get('/get-conversation', [PortalMessagingController::class, 'getConversation'])->name('get-conversation');
        Route::get('/get-conversations', [PortalMessagingController::class, 'getConversations'])->name('get-conversations');
        Route::get('/conversation/{id}', [PortalMessagingController::class, 'viewConversation'])->name('conversation');
        Route::post('/send', [PortalMessagingController::class, 'sendMessage'])->name('send');
        Route::post('/create', [PortalMessagingController::class, 'createConversation'])->name('create');
        Route::post('/leave-group', [PortalMessagingController::class, 'leaveGroupConversation'])->name('leave-group');
        Route::post('/unsend/{id}', [PortalMessagingController::class, 'unsendMessage'])->name('unsend');
        Route::get('/unread-count', [PortalMessagingController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/conversation-unread-count', [PortalMessagingController::class, 'getConversationUnreadCount'])->name('conversation-unread-count');
        Route::get('/recent-messages', [PortalMessagingController::class, 'getRecentMessages'])->name('recent-messages');
        Route::post('/read-all', [PortalMessagingController::class, 'markAllAsRead'])->name('read-all');
        Route::post('/mark-as-read', [PortalMessagingController::class, 'markAsRead'])->name('mark-as-read');
        Route::get('/attachment/{id}', [PortalMessagingController::class, 'downloadAttachment'])->name('attachment');
        Route::get('/check-conversation', [PortalMessagingController::class, 'getConversationsWithRecipient'])->name('check-conversation');
    });
    
    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [PortalAccountProfileController::class, 'beneficiaryIndex'])->name('index');
        Route::get('/settings', [PortalAccountProfileController::class, 'beneficiarySettings'])->name('settings');
        Route::post('/update-password', [PortalAccountProfileController::class, 'updateBeneficiaryPassword'])->name('update-password');
    });

    // Family Members
    Route::get('/family-members', [PortalFamilyMembersController::class, 'index'])->name('member.index');

    // 
    Route::get('/faq', function() {
        return view('beneficiaryPortal.FAQuestions');
    })->name('faQuestions.index');

    // Notification Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [PortalNotificationsController::class, 'getUserNotifications'])->name('index');
        Route::post('/{id}/read', [PortalNotificationsController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [PortalNotificationsController::class, 'markAllAsRead'])->name('mark-all-read');
    });
});