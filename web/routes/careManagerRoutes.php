<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\CareWorkerController;
use App\Http\Controllers\CareManagerController;
use App\Http\Controllers\WeeklyCareController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\CareWorkerPerformanceController;
use App\Http\Controllers\SchedulesAndAppointmentsController;
use App\Http\Controllers\BeneficiaryMapController;
use App\Http\Controllers\DonorAcknowledgementController;
use App\Http\Controllers\HighlightsAndEventsController;
use App\Http\Controllers\ViewAccountProfileController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\HealthMonitoringController;
use App\Http\Controllers\VisitationController;
use App\Http\Controllers\InternalAppointmentsController;
use App\Http\Controllers\MedicationScheduleController;
use App\Http\Controllers\EmergencyAndRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShiftHistoryController;

require_once __DIR__.'/routeHelpers.php';

// All routes with care_manager role check
Route::middleware(['auth', '\App\Http\Middleware\CheckRole:care_manager'])->prefix('care-manager')->name('care-manager.')->group(function () {
    // Dashboard
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'careManagerDashboard'])->name('index');
    });
    
    // Care Worker Management
    Route::prefix('care-workers')->name('careworkers.')->group(function () {
        Route::get('/', [CareWorkerController::class, 'index'])->name('index');
        Route::get('/add', [CareWorkerController::class, 'create'])->name('create');
        Route::post('/store', [CareWorkerController::class, 'storeCareWorker'])->name('store');
        Route::get('/{id}/edit', [CareWorkerController::class, 'editCareworkerProfile'])->name('edit');
        Route::put('/{id}', [CareWorkerController::class, 'updateCareWorker'])->name('update');
        Route::post('/{id}/update-status-ajax', [CareWorkerController::class, 'updateStatusAjax'])->name('updateStatusAjax');
        Route::post('/delete', [CareWorkerController::class, 'deleteCareworker'])->name('delete');
        Route::post('/view-details', [CareWorkerController::class, 'viewCareworkerDetails'])->name('view');
    });
    
    // Beneficiary Management
    Route::prefix('beneficiaries')->name('beneficiaries.')->group(function () {
        Route::get('/', [BeneficiaryController::class, 'index'])->name('index');
        Route::get('/add-beneficiary', [BeneficiaryController::class, 'create'])->name('create');
        Route::post('/add-beneficiary', [BeneficiaryController::class, 'storeBeneficiary'])->name('store');
        Route::get('/edit-beneficiary/{id}', [BeneficiaryController::class, 'editBeneficiary'])->name('edit');
        Route::put('/edit-beneficiary/{id}', [BeneficiaryController::class, 'updateBeneficiary'])->name('update');
        Route::put('/{id}/status', [BeneficiaryController::class, 'updateStatusAjax'])->name('updateStatusAjax');
        Route::post('/view-beneficiary-details', [BeneficiaryController::class, 'viewProfileDetails'])->name('view-details');
        Route::post('/delete', [BeneficiaryController::class, 'deleteBeneficiary'])->name('delete');
    });
    
    // Family Member Management
    Route::prefix('families')->name('families.')->group(function () {
        Route::get('/', [FamilyMemberController::class, 'index'])->name('index');
        Route::get('/add', [FamilyMemberController::class, 'create'])->name('create');
        Route::post('/store', [FamilyMemberController::class, 'storeFamily'])->name('store');
        Route::put('/{id}', [FamilyMemberController::class, 'updateFamilyMember'])->name('update');
        Route::post('/delete', [FamilyMemberController::class, 'deleteFamilyMember'])->name('delete');
        Route::post('/view-details', [FamilyMemberController::class, 'viewFamilyDetails'])->name('view');
        Route::get('/{id}/edit', [FamilyMemberController::class, 'editFamilyMember'])->name('edit');
    });
    
    // Weekly Care Plans
    Route::prefix('weekly-care-plans')->name('weeklycareplans.')->group(function () {
        Route::get('/', [WeeklyCareController::class, 'index'])->name('index');
        Route::get('/create', [WeeklyCareController::class, 'create'])->name('create');
        Route::post('/store', [WeeklyCareController::class, 'store'])->name('store');
        Route::get('/{id}', [WeeklyCareController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [WeeklyCareController::class, 'edit'])->name('edit');
        Route::put('/{id}', [WeeklyCareController::class, 'update'])->name('update');
        Route::delete('/{id}', [WeeklyCareController::class, 'destroy'])->name('delete');
        Route::get('/beneficiary/{id}', [WeeklyCareController::class, 'getBeneficiaryDetails'])->name('beneficiaryDetails');
    });

    // Records Management
    Route::get('/records', [ReportsController::class, 'index'])->name('reports');
    
    // Password validation route
    Route::post('/validate-password', [UserController::class, 'validatePassword'])->name('validate-password');

    // Exports
    Route::prefix('exports')->name('exports.')->group(function () {
        Route::post('/beneficiaries-pdf', [ExportController::class, 'exportBeneficiariesToPdf'])->name('beneficiaries-pdf');
        Route::post('/family-pdf', [ExportController::class, 'exportFamilyToPdf'])->name('family-pdf');
        Route::post('/careworkers-pdf', [ExportController::class, 'exportCareworkersToPdf'])->name('careworkers-pdf');
        Route::post('/beneficiaries-excel', [ExportController::class, 'exportBeneficiariesToExcel'])->name('beneficiaries-excel');
        Route::post('/family-excel', [ExportController::class, 'exportFamilyMembersToExcel'])->name('family-excel');
        Route::post('/careworkers-excel', [ExportController::class, 'exportCareworkersToExcel'])->name('careworkers-excel');
        Route::post('/health-monitoring-pdf', [ExportController::class, 'exportHealthMonitoringToPdfForCareManager'])->name('health.monitoring.pdf');
        Route::post('/careworker-performance-pdf', [ExportController::class, 'exportCareWorkerPerformanceToPdfForCareManager'])->name('careworker.performance.pdf');
        Route::post('/reports-pdf', [ExportController::class, 'exportReportsToPdf'])->name('reports.pdf');
    });

    //Municipalities (Read-Only)
    Route::get('/municipalities', [CareManagerController::class, 'municipality'])->name('municipalities.index');

    //Notification routes
    Route::get('/notifications', [NotificationsController::class, 'getUserNotifications'])->name('notifications.get');
    Route::post('/notifications/{id}/read', [NotificationsController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationsController::class, 'markAllAsRead'])->name('notifications.read-all');

    // View Account Profile
    Route::prefix('account-profile')->name('account.profile.')->group(function () {
        Route::get('/', [ViewAccountProfileController::class, 'careManagerIndex'])->name('index');
        Route::get('/settings', [ViewAccountProfileController::class, 'careManagerSettings'])->name('settings');
    });

    // Update email and password
    Route::post('/update-email', [CareManagerController::class, 'updateCareManagerEmail'])->name('update.email');
    Route::post('/update-password', [CareManagerController::class, 'updateCareManagerPassword'])->name('update.password');

    // Messaging system
    Route::prefix('messaging')->name('messaging.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/conversation/{id}', [MessageController::class, 'viewConversation'])->name('conversation');
        Route::post('/send-message', [MessageController::class, 'sendMessage'])->name('send');
        Route::post('/create-conversation', [MessageController::class, 'createConversation'])->name('create');
        Route::post('/create-group', [MessageController::class, 'createGroupConversation'])->name('create-group');
        Route::get('/unread-count', [MessageController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/recent-messages', [MessageController::class, 'getRecentMessages'])->name('recent');
        Route::post('/read-all', [MessageController::class, 'markAllAsRead'])->name('read-all');
        Route::get('/get-users', [MessageController::class, 'getUsers'])->name('get-users');
        Route::get('/get-conversation', [MessageController::class, 'getConversation'])->name('get-conversation');
        Route::post('/mark-as-read', [MessageController::class, 'markConversationAsRead'])->name('mark-as-read');
        Route::get('/get-conversations', [MessageController::class, 'getConversationsList'])->name('get-conversations');
        Route::post('/get-conversations-with-recipient', [MessageController::class, 'getConversationsWithRecipient'])->name('messaging.get-conversations-with-recipient');
        Route::get('check-last-participant/{id}', [MessageController::class, 'checkLastParticipant'])->name('check-last-participant');
        Route::post('leave-conversation', [MessageController::class, 'leaveConversation'])->name('leave-conversation');
        Route::get('group-members/{id}', [MessageController::class, 'getGroupMembers'])->name('group-members');
        Route::post('add-group-member', [MessageController::class, 'addGroupMember'])->name('add-group-member');
        Route::post('unsend-message/{id}', [MessageController::class, 'unsendMessage'])->name('unsend');
    });

    // Health Monitoring
    Route::prefix('health-monitoring')->name('health.monitoring.')->group(function () {
        Route::get('/', [HealthMonitoringController::class, 'careManagerIndex'])->name('index');
    });

    // Care Worker Performance
    Route::prefix('care-worker-performance')->name('careworker.performance.')->group(function () {
        Route::get('/', [CareWorkerPerformanceController::class, 'careManagerIndex'])->name('index');
    });

    // Care Worker Appointments
    Route::prefix('careworker-appointments')->name('careworker.appointments.')->group(function () {
        Route::get('/', [VisitationController::class, 'index'])->name('index');
        Route::get('/get-visitations', [VisitationController::class, 'getVisitations'])->name('get');
        Route::get('/beneficiaries', [VisitationController::class, 'getBeneficiaries'])->name('beneficiaries');
        Route::get('/beneficiary/{id}', [VisitationController::class, 'getBeneficiaryDetails'])->name('beneficiary');
        Route::get('/beneficiary/{id}', [VisitationController::class, 'getBeneficiaryDetails'])->name('beneficiary.details');
        Route::post('/store', [VisitationController::class, 'storeAppointment'])->name('store');
        Route::post('/update', [VisitationController::class, 'updateAppointment'])->name('update');
        Route::post('/cancel', [VisitationController::class, 'cancelAppointment'])->name('cancel');
    });

    // Internal Appointments
    Route::prefix('internal-appointments')->name('internal-appointments.')->group(function () {
        Route::get('/', [InternalAppointmentsController::class, 'index'])->name('index');
        Route::get('/get-appointments', [InternalAppointmentsController::class, 'getAppointments'])->name('getAppointments');
        Route::post('/store', [InternalAppointmentsController::class, 'store'])->name('store');
        Route::post('/update', [InternalAppointmentsController::class, 'update'])->name('update');  // Fixed path
        Route::post('/cancel', [InternalAppointmentsController::class, 'cancel'])->name('cancel');
    });

    // Medication Schedule
    Route::prefix('medication-schedule')->name('medication.schedule.')->group(function () {
        Route::get('/', [MedicationScheduleController::class, 'index'])->name('index');
        Route::post('/store', [MedicationScheduleController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [MedicationScheduleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MedicationScheduleController::class, 'update'])->name('update');
        Route::post('/delete', [MedicationScheduleController::class, 'destroy'])->name('delete');
    });

    Route::prefix('ai-summary')->name('ai-summary.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AiSummaryController::class, 'index'])->name('index');
        Route::get('/search', [\App\Http\Controllers\AiSummaryController::class, 'search'])->name('search');
        Route::get('/care-plan/{id}', [\App\Http\Controllers\AiSummaryController::class, 'getCarePlan'])->name('getCarePlan');
        Route::post('/summarize', [\App\Http\Controllers\AiSummaryController::class, 'summarize'])->name('summarize');
        Route::put('/update/{id}', [\App\Http\Controllers\AiSummaryController::class, 'updateSummary'])->name('update');
        Route::put('/finalize/{id}', [\App\Http\Controllers\AiSummaryController::class, 'finalizeSummary'])->name('finalize');
        Route::post('/translate', [\App\Http\Controllers\AiSummaryController::class, 'translate'])->name('translate');
        Route::post('/translate-sections', [\App\Http\Controllers\AiSummaryController::class, 'translateSections'])->name('translate-sections');
    });

    // Emergency and Service Request
        Route::prefix('emergency-request')->name('emergency.request.')->group(function () {
        Route::get('/', [EmergencyAndRequestController::class, 'index'])->name('index');
        Route::get('/view-history', [EmergencyAndRequestController::class, 'viewHistory'])->name('viewHistory');
        Route::post('/filter-history', [EmergencyAndRequestController::class, 'filterHistory'])->name('filter.history');
        Route::get('/partial-content', [EmergencyAndRequestController::class, 'partialContent'])->name('partial-content');
        
        // Data retrieval routes for modals
        Route::get('/emergency/{id}', [EmergencyAndRequestController::class, 'getEmergencyNotice'])->name('get.emergency');
        Route::get('/service-request/{id}', [EmergencyAndRequestController::class, 'getServiceRequest'])->name('get.service');
        Route::get('/care-workers', [EmergencyAndRequestController::class, 'getAllCareWorkers'])->name('get.careworkers');
        
        // Action routes
        Route::post('/respond-emergency', [EmergencyAndRequestController::class, 'respondToEmergency'])->name('respond.emergency');
        Route::post('/handle-service', [EmergencyAndRequestController::class, 'handleServiceRequest'])->name('handle.service');
        Route::post('/archive', [EmergencyAndRequestController::class, 'archiveRecord'])->name('archive');
        });

        // Shift Histories
        Route::prefix('shift-histories')->name('shift.histories.')->group(function () {
            Route::get('/', [ShiftHistoryController::class, 'index'])->name('index');
            Route::get('/shift-details/{shiftId}', [ShiftHistoryController::class, 'shiftDetails'])->name('shiftDetails');
            Route::get('/{shiftId}/export-pdf', [ShiftHistoryController::class, 'exportShiftPdf'])->name('exportPdf');
        });

        //Beneficiary Map
        Route::prefix('beneficiary-map')->name('beneficiary.map.')->group(function () {
            Route::get('/', [BeneficiaryMapController::class, 'index'])->name('index');
        });
    
});