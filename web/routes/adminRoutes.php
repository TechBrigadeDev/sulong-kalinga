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
use App\Http\Controllers\CareWorkerAppointmentController;
use App\Http\Controllers\InternalAppointmentsController;
use App\Http\Controllers\MedicationScheduleController;
use App\Http\Controllers\EmergencyAndRequestController;
use App\Http\Controllers\ExpenseTrackerController;
use App\Http\Controllers\VisitationController;
use App\Http\Controllers\CareWorkerTrackingController;
use App\Http\Controllers\AiSummaryController;
use App\Http\Controllers\ShiftHistoryController;
use App\Http\Controllers\DashboardController;


// All routes with administrator role check

// This route group is for the administrator role and will be prefixed with 'admin'
// It will also use the CheckRole middleware to ensure that only users with the administrator role can access these routes
// The routes are grouped under the 'admin' prefix and will have the 'auth' middleware applied to them
// CheckRole's full namespace is used to ensure that the correct middleware is applied, do not remove this to prevent errors

require_once __DIR__.'/routeHelpers.php';

Route::middleware(['auth', '\App\Http\Middleware\CheckRole:administrator'])->prefix('admin')->name('admin.')->group(function () {
     Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    // Administrator Management
    Route::prefix('administrators')->name('administrators.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/add', function () {
            return view('admin.addAdministrator');
        })->name('create');
        Route::post('/store', [AdminController::class, 'storeAdministrator'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'editAdminProfile'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'updateAdministrator'])->name('update');
        Route::post('/{id}/update-status-ajax', [AdminController::class, 'updateStatusAjax'])->name('updateStatusAjax');
        Route::post('/delete', [AdminController::class, 'deleteAdministrator'])->name('delete');
        Route::post('/view-details', [AdminController::class, 'viewAdminDetails'])->name('view');
    });

    // Care Manager Management
    Route::prefix('care-managers')->name('caremanagers.')->group(function () {
        Route::get('/', [CareManagerController::class, 'index'])->name('index');
        Route::get('/add', [CareManagerController::class, 'create'])->name('create');
        Route::post('/store', [CareManagerController::class, 'storeCareManager'])->name('store');
        Route::get('/{id}/edit', [CareManagerController::class, 'editCaremanagerProfile'])->name('edit');
        Route::put('/{id}', [CareManagerController::class, 'updateCaremanager'])->name('update');
        Route::post('/{id}/update-status-ajax', [CareManagerController::class, 'updateStatusAjax'])->name('updateStatusAjax');        
        Route::post('/delete', [AdminController::class, 'deleteCaremanager'])->name('delete');
        Route::post('/view-details', [CareManagerController::class, 'viewCaremanagerDetails'])->name('view');
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
        Route::get('/add', [BeneficiaryController::class, 'create'])->name('create');
        Route::post('/store', [BeneficiaryController::class, 'storeBeneficiary'])->name('store');
        Route::get('/{id}/edit', [BeneficiaryController::class, 'editBeneficiary'])->name('edit');
        Route::put('/{id}', [BeneficiaryController::class, 'updateBeneficiary'])->name('update');
        Route::post('/{id}/update-status-ajax', [BeneficiaryController::class, 'updateStatusAjax'])->name('updateStatusAjax');        Route::put('/{id}/activate', [BeneficiaryController::class, 'activate'])->name('activate');
        Route::post('/delete', [BeneficiaryController::class, 'deleteBeneficiary'])->name('delete');
        Route::post('/view-details', [BeneficiaryController::class, 'viewProfileDetails'])->name('view');
    });

    // Family Member Management
    Route::prefix('families')->name('families.')->group(function () {
        Route::get('/', [FamilyMemberController::class, 'index'])->name('index');
        Route::get('/add', [FamilyMemberController::class, 'create'])->name('create');
        Route::post('/store', [FamilyMemberController::class, 'storeFamily'])->name('store');
        Route::get('/{id}/edit', [FamilyMemberController::class, 'editFamilyMember'])->name('edit');
        Route::put('/{id}', [FamilyMemberController::class, 'updateFamily'])->name('update');
        Route::post('/delete', [FamilyMemberController::class, 'deleteFamilyMember'])->name('delete');
        Route::post('/view-details', [FamilyMemberController::class, 'viewFamilyDetails'])->name('view');
        Route::put('/{id}', [FamilyMemberController::class, 'updateFamilyMember'])->name('update');
    });

    // Weekly Care Plans
    Route::prefix('weekly-care-plans')->name('weeklycareplans.')->group(function () {
        Route::get('/', [WeeklyCareController::class, 'index'])->name('index');
        Route::get('/create', [WeeklyCareController::class, 'create'])->name('create');
        Route::post('/store', [WeeklyCareController::class, 'store'])->name('store');
        Route::get('/{id}', [WeeklyCareController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [WeeklyCareController::class, 'edit'])->name('edit');
        Route::put('/{id}', [WeeklyCareController::class, 'update'])->name('update');
        Route::get('/beneficiary/{id}', [WeeklyCareController::class, 'getBeneficiaryDetails'])->name('beneficiaryDetails');
        Route::delete('/{id}', [WeeklyCareController::class, 'destroy'])->name('delete');

    });

    // Record Management
    Route::get('/records', [ReportsController::class, 'index'])->name('reports');

    // Municipality and Barangay Management
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', [AdminController::class, 'municipality'])->name('index');
        Route::post('/municipalities/add', [AdminController::class, 'addMunicipality'])->name('municipalities.store');
        Route::post('/municipalities/update', [AdminController::class, 'updateMunicipality'])->name('municipalities.update');
        Route::delete('/municipalities/{id}', [AdminController::class, 'deleteMunicipality'])->name('municipalities.delete');
        Route::post('/barangays/add', [AdminController::class, 'addBarangay'])->name('barangays.store');
        Route::post('/barangays/update', [AdminController::class, 'updateBarangay'])->name('barangays.update');
        Route::delete('/barangays/{id}', [AdminController::class, 'deleteBarangay'])->name('barangays.delete');
    });

    // Export functionality
    Route::prefix('export')->name('export.')->group(function () {
        // PDF Exports
        Route::post('/beneficiaries-pdf', [ExportController::class, 'exportBeneficiariesToPdf'])->name('beneficiaries.pdf');
        Route::post('/family-pdf', [ExportController::class, 'exportFamilyToPdf'])->name('family.pdf');
        Route::post('/careworkers-pdf', [ExportController::class, 'exportCareworkersToPdf'])->name('careworkers.pdf');
        Route::post('/caremanagers-pdf', [ExportController::class, 'exportCaremanagersToPdf'])->name('caremanagers.pdf');
        Route::post('/administrators-pdf', [ExportController::class, 'exportAdministratorsToPdf'])->name('administrators.pdf');
        Route::post('/health-monitoring-pdf', [ExportController::class, 'exportHealthMonitoringToPdf'])->name('health.monitoring.pdf');
        Route::post('/careworker-performance-pdf', [ExportController::class, 'exportCareWorkerPerformanceToPdf'])->name('careworker.performance.pdf');
        Route::post('/reports-pdf', [ExportController::class, 'exportReportsToPdf'])->name('reports.pdf');

        // Excel Exports
        Route::post('/beneficiaries-excel', [ExportController::class, 'exportBeneficiariesToExcel'])->name('beneficiaries.excel');
        Route::post('/family-excel', [ExportController::class, 'exportFamilyMembersToExcel'])->name('family.excel');
        Route::post('/careworkers-excel', [ExportController::class, 'exportCareworkersToExcel'])->name('careworkers.excel');
        Route::post('/caremanagers-excel', [ExportController::class, 'exportCareManagersToExcel'])->name('caremanagers.excel');
        Route::post('/administrators-excel', [ExportController::class, 'exportAdministratorsToExcel'])->name('administrators.excel');
    });
    
    // Password validation route
    Route::post('/validate-password', [UserController::class, 'validatePassword'])->name('validate-password');

    // Care Worker Performance
    Route::prefix('care-worker-performance')->name('careworker.performance.')->group(function () {
        Route::get('/', [CareWorkerPerformanceController::class, 'index'])->name('index');
    });

    //Schedules and Appointments
    Route::prefix('schedules-appointments')->name('schedules.appointments.')->group(function () {
        Route::get('/', [SchedulesAndAppointmentsController::class, 'index'])->name('index');
    });

    //Beneficiary Map
    Route::prefix('beneficiary-map')->name('beneficiary.map.')->group(function () {
        Route::get('/', [BeneficiaryMapController::class, 'index'])->name('index');
    });

    //Donor Acknowledgement
    Route::prefix('donor-acknowledgement')->name('donor.acknowledgement.')->group(function () {
        Route::get('/', [DonorAcknowledgementController::class, 'index'])->name('index');
    });

    //Highlights and Events
    Route::prefix('highlights-events')->name('highlights.events.')->group(function () {
        Route::get('/', [HighlightsAndEventsController::class, 'index'])->name('index');
    });

    // View Account Profile
    Route::prefix('account-profile')->name('account.profile.')->group(function () {
        Route::get('/', [ViewAccountProfileController::class, 'index'])->name('index');
        Route::get('/settings', [ViewAccountProfileController::class, 'settings'])->name('settings');
    });

    // Health Monitoring
    Route::prefix('health-monitoring')->name('health.monitoring.')->group(function () {
        Route::get('/', [HealthMonitoringController::class, 'index'])->name('index');
    });

    // Medication Schedule
    Route::prefix('medication-schedule')->name('medication.schedule.')->group(function () {
        Route::get('/', [MedicationScheduleController::class, 'index'])->name('index');
        Route::post('/store', [MedicationScheduleController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [MedicationScheduleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MedicationScheduleController::class, 'update'])->name('update');
        Route::post('/delete', [MedicationScheduleController::class, 'destroy'])->name('delete');
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

    // Expense Tracker
    Route::prefix('expense-tracker')->name('expense.')->group(function () {
        Route::get('/', [ExpenseTrackerController::class, 'index'])->name('index');
                
        // Expense CRUD routes
        Route::post('/store-expense', [ExpenseTrackerController::class, 'storeExpense'])->name('store');
        Route::get('/get-expense/{id}', [ExpenseTrackerController::class, 'getExpense'])->name('get');
        Route::post('/update-expense/{id}', [ExpenseTrackerController::class, 'updateExpense'])->name('update');
        Route::post('/delete-expense', [ExpenseTrackerController::class, 'deleteExpense'])->name('delete');
        Route::put('/expense/{id}', [ExpenseTrackerController::class, 'updateExpense'])->name('update');
        
        // Budget CRUD routes
        Route::post('/store-budget', [ExpenseTrackerController::class, 'storeBudget'])->name('budget.store');
        Route::get('/get-budget/{id}', [ExpenseTrackerController::class, 'getBudgetAllocation'])->name('budget.get');
        Route::post('/update-budget/{id}', [ExpenseTrackerController::class, 'updateBudget'])->name('budget.update');
        Route::post('/delete-budget', [ExpenseTrackerController::class, 'deleteBudget'])->name('budget.delete');
        Route::put('/budget/{id}', [ExpenseTrackerController::class, 'updateBudget'])->name('budget.update'); // Add this line
        
        // Filter routes
        Route::get('/filtered-expenses', [ExpenseTrackerController::class, 'getFilteredExpenses'])->name('filtered');
        Route::get('/filtered-budgets', [ExpenseTrackerController::class, 'getFilteredBudgets'])->name('budget.filtered');
        
        // Export routes
        Route::post('/export-expenses-excel', [ExpenseTrackerController::class, 'exportExpensesToExcel'])->name('export.excel');
        Route::post('/export-budgets-excel', [ExpenseTrackerController::class, 'exportBudgetsToExcel'])->name('budget.export.excel');
    });

    // Careworker Tracking
    Route::prefix('careworker-tracking')->name('careworker.tracking.')->group(function () {
        Route::get('/', [CareWorkerTrackingController::class, 'index'])->name('index');
    });


    // Update email and password
    Route::post('/update-email', [AdminController::class, 'updateAdminEmail'])->name('update.email');
    Route::post('/update-password', [AdminController::class, 'updateAdminPassword'])->name('update.password');

    // Notification routes
    Route::get('/notifications', [NotificationsController::class, 'getUserNotifications'])->name('notifications.get');
    Route::post('/notifications/{id}/read', [NotificationsController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationsController::class, 'markAllAsRead'])->name('notifications.read-all');

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
        Route::get('/', [HealthMonitoringController::class, 'index'])->name('index');
    });

    // Care Worker Performance
    Route::prefix('care-worker-performance')->name('careworker.performance.')->group(function () {
        Route::get('/', [CareWorkerPerformanceController::class, 'index'])->name('index');
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

    // AI Summary
    Route::prefix('ai-summary')->name('ai-summary.')->group(function () {
        Route::get('/', [AiSummaryController::class, 'index'])->name('index');
        Route::get('/search', [AiSummaryController::class, 'search'])->name('search');
        Route::get('/care-plan/{id}', [AiSummaryController::class, 'getCarePlan'])->name('getCarePlan');
        Route::post('/summarize', [AiSummaryController::class, 'summarize'])->name('summarize');
        Route::put('/update/{id}', [AiSummaryController::class, 'updateSummary'])->name('update');
        Route::put('/finalize/{id}', [AiSummaryController::class, 'finalizeSummary'])->name('finalize');
        Route::post('/translate', [AiSummaryController::class, 'translate'])->name('translate');
        Route::post('/translate-sections', [AiSummaryController::class, 'translateSections'])->name('translate-sections');
    });

    // Shift Histories
    Route::prefix('shift-histories')->name('shift.histories.')->group(function () {
        Route::get('/', [ShiftHistoryController::class, 'index'])->name('index');
        // Route::get('/archived', [ShiftHistoryController::class, 'archived'])->name('archived');
        Route::get('/shift-details/{shiftId}', [ShiftHistoryController::class, 'shiftDetails'])->name('shiftDetails');
        Route::get('/{shiftId}/export-pdf', [ShiftHistoryController::class, 'exportShiftPdf'])->name('exportPdf');
    });

});

// Route::get('/admin/viewProfile', function () {
//     return view('components.viewProfile');
// })->name('viewProfile');
