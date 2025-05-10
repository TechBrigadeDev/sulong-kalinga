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


require_once __DIR__.'/routeHelpers.php';

// All routes with care_worker role check
Route::middleware(['auth', '\App\Http\Middleware\CheckRole:care_worker'])->prefix('care-worker')->name('care-worker.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $showWelcome = session()->pull('show_welcome', false);
        return view('careWorker.workerdashboard', ['showWelcome' => $showWelcome]);
    })->name('dashboard');
    
    // Beneficiary Management (add, edit, view only - no delete or status change)
    Route::prefix('beneficiaries')->name('beneficiaries.')->group(function () {
        Route::get('/', [BeneficiaryController::class, 'index'])->name('index');
        Route::get('/add-beneficiary', [BeneficiaryController::class, 'create'])->name('create');
        Route::post('/add-beneficiary', [BeneficiaryController::class, 'storeBeneficiary'])->name('store');
        Route::get('/edit-beneficiary/{id}', [BeneficiaryController::class, 'editBeneficiary'])->name('edit');
        Route::put('/edit-beneficiary/{id}', [BeneficiaryController::class, 'updateBeneficiary'])->name('update');
        Route::post('/view-beneficiary-details', [BeneficiaryController::class, 'viewProfileDetails'])->name('view-details');
        Route::post('/view', [BeneficiaryController::class, 'viewBeneficiary'])->name('view');
    });
    
    // Family Member Management (view, add, edit for assigned beneficiaries only - no delete)
    Route::prefix('families')->name('families.')->group(function () {
        Route::get('/', [FamilyMemberController::class, 'index'])->name('index');
        Route::get('/add', [FamilyMemberController::class, 'create'])->name('create');
        Route::post('/store', [FamilyMemberController::class, 'storeFamily'])->name('store');
        Route::get('/{id}/edit', [FamilyMemberController::class, 'editFamilyMember'])->name('edit');
        Route::put('/{id}', [FamilyMemberController::class, 'updateFamilyMember'])->name('update');
        Route::post('/view-details', [FamilyMemberController::class, 'viewFamilyDetails'])->name('view');
    });
    
    // Weekly Care Plans (add, view and edit authored plans only - no delete)
    Route::prefix('weekly-care-plans')->name('weeklycareplans.')->group(function () {
        Route::get('/', [WeeklyCareController::class, 'index'])->name('index');
        Route::get('/create', [WeeklyCareController::class, 'create'])->name('create');
        Route::post('/store', [WeeklyCareController::class, 'store'])->name('store');
        Route::get('/{id}', [WeeklyCareController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [WeeklyCareController::class, 'edit'])->name('edit');
        Route::put('/{id}', [WeeklyCareController::class, 'update'])->name('update');
        Route::get('/beneficiary/{id}', [WeeklyCareController::class, 'getBeneficiaryDetails'])->name('beneficiaryDetails');
    });

    // Reports Management (only authored reports)
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    
    // Password validation route
    Route::post('/validate-password', [UserController::class, 'validatePassword'])->name('validate-password');

    // Check beneficiary permission for editing GCP
    Route::get('/check-beneficiary-permission/{id}', [BeneficiaryController::class, 'checkBeneficiaryPermission'])
    ->name('check-beneficiary-permission');

    // Exports (only for beneficiaries and families)
    Route::prefix('exports')->name('exports.')->group(function () {
        Route::post('/beneficiaries-pdf', [ExportController::class, 'exportBeneficiariesToPdf'])->name('beneficiaries-pdf');
        Route::post('/family-pdf', [ExportController::class, 'exportFamilyToPdf'])->name('family-pdf');
        Route::post('/beneficiaries-excel', [ExportController::class, 'exportBeneficiariesToExcel'])->name('beneficiaries-excel');
        Route::post('/family-excel', [ExportController::class, 'exportFamilyMembersToExcel'])->name('family-excel');
    });

    // Notification routes
    Route::get('/notifications', [App\Http\Controllers\NotificationsController::class, 'getUserNotifications'])->name('notifications.get');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationsController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationsController::class, 'markAllAsRead'])->name('notifications.read-all');

    // View Account Profile
    Route::prefix('account-profile')->name('account.profile.')->group(function () {
        Route::get('/', [ViewAccountProfileController::class, 'careWorkerIndex'])->name('index');
        Route::get('/settings', [ViewAccountProfileController::class, 'careWorkerSettings'])->name('settings');
    });

    // Update email and password
    Route::post('/update-email', [CareWorkerController::class, 'updateCareWorkerEmail'])->name('update.email');
    Route::post('/update-password', [CareWorkerController::class, 'updateCareWorkerPassword'])->name('update.password');

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

    // Care Worker Appointments
    Route::prefix('careworker-appointments')->name('careworker.appointments.')->group(function () {
        Route::get('/', [VisitationController::class, 'index'])->name('index');
        Route::get('/get-visitations', [VisitationController::class, 'getVisitations'])->name('get');
        Route::get('/beneficiaries', [VisitationController::class, 'getBeneficiaries'])->name('beneficiaries');
        Route::get('/beneficiary/{id}', [VisitationController::class, 'getBeneficiaryDetails'])->name('beneficiary');
        Route::get('/beneficiary/{id}', [VisitationController::class, 'getBeneficiaryDetails'])->name('beneficiary.details');
    });

});