<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\CareManagerApiController;
use App\Http\Controllers\Api\CareWorkerApiController;
use App\Http\Controllers\Api\BeneficiaryApiController;
use App\Http\Controllers\Api\FamilyMemberApiController;
use App\Http\Controllers\Api\MunicipalityApiController;
use App\Http\Controllers\Api\PortalAccountApiController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\ReportsApiController;
use App\Http\Controllers\Api\ViewAccountProfileApiController;
use App\Http\Controllers\Api\WeeklyCarePlanApiController;
use App\Http\Controllers\Api\MedicationScheduleApiController;
use App\Http\Controllers\Api\InternalAppointmentsApiController;
use App\Http\Controllers\Api\VisitationApiController;
use App\Http\Controllers\Api\MessagingApiController;
use App\Http\Controllers\Api\ShiftApiController;
use App\Http\Controllers\Api\ShiftTrackApiController;
use App\Http\Controllers\Api\RecordsManagementApiController;

// Public routes
Route::get('/public-test', function () {
    return response(['message' => 'Public API is working!'], 200);
});

// Authentication Routes
Route::post('/login', [AuthApiController::class, 'login']);


// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth/User Profile
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/user', [AuthApiController::class, 'user']);
    // Route::put('/profile', [UserApiController::class, 'updateProfile']); OLD

    // Mobile Account Profile API 
    Route::get('/account-profile', [ViewAccountProfileApiController::class, 'show']);
    Route::patch('/account-profile/email', [ViewAccountProfileApiController::class, 'updateEmail']);
    Route::patch('/account-profile/password', [ViewAccountProfileApiController::class, 'updatePassword']);

    // Beneficiary Management
    Route::get('/beneficiaries', [BeneficiaryApiController::class, 'index']);
    Route::get('/beneficiaries/{id}', [BeneficiaryApiController::class, 'show']);
    Route::post('/beneficiaries', [BeneficiaryApiController::class, 'store']);
    Route::put('/beneficiaries/{id}', [BeneficiaryApiController::class, 'update']);
    Route::patch('/beneficiaries/{id}/status', [BeneficiaryApiController::class, 'changeStatus']);
    Route::delete('/beneficiaries/{id}', [BeneficiaryApiController::class, 'destroy']);
    Route::post('/beneficiaries/{id}/restore', [BeneficiaryApiController::class, 'restore']);
    Route::get('/beneficiaries/export', [BeneficiaryApiController::class, 'export']);

    // Family Member Management
    Route::get('/family-members', [FamilyMemberApiController::class, 'index']);
    Route::get('/family-members/{id}', [FamilyMemberApiController::class, 'show']);
    Route::post('/family-members', [FamilyMemberApiController::class, 'store']);
    Route::put('/family-members/{id}', [FamilyMemberApiController::class, 'update']);
    Route::patch('/family-members/{id}/status', [FamilyMemberApiController::class, 'changeStatus']);
    Route::delete('/family-members/{id}', [FamilyMemberApiController::class, 'destroy']);

    // Admin Management (Full CRUD + status + restore)
    Route::get('/admins', [AdminApiController::class, 'index']);
    Route::get('/admins/{id}', [AdminApiController::class, 'show']);
    Route::post('/admins', [AdminApiController::class, 'store']);
    Route::put('/admins/{id}', [AdminApiController::class, 'update']);
    Route::delete('/admins/{id}', [AdminApiController::class, 'destroy']);
    Route::patch('/admins/{id}/status', [AdminApiController::class, 'changeStatus']);
    Route::post('/admins/{id}/restore', [AdminApiController::class, 'restore']);

    // Care Manager Read-Only
    Route::get('/care-managers', [CareManagerApiController::class, 'index']);
    Route::get('/care-managers/{id}', [CareManagerApiController::class, 'show']);

    // Care Worker Read-Only
    Route::get('/care-workers', [CareWorkerApiController::class, 'index']);
    Route::get('/care-workers/{id}', [CareWorkerApiController::class, 'show']);

    // Municipality (initial)
    Route::get('/municipalities', [MunicipalityApiController::class, 'index']);
    Route::get('/municipalities/{id}', [MunicipalityApiController::class, 'show']);
    Route::get('/provinces', [MunicipalityApiController::class, 'provinces']);

    // Portal Account (initial)
    Route::get('/portal-account/{id}/users', [PortalAccountApiController::class, 'getPortalAccountUsers']);
    Route::post('/portal-account/select-user', [PortalAccountApiController::class, 'selectPortalUser']);

    // Notifications (initial)
    // Route::post('/mobile-notifications', [MobileNotificationApiController::class, 'store']);

    // Reports Management API
    Route::get('/reports', [ReportsApiController::class, 'index']);
    Route::get('/reports/{id}', [ReportsApiController::class, 'show']);
    Route::put('/reports/{id}', [ReportsApiController::class, 'update']);

    // Weekly Care Plan (WCP) API
    // Create only with store
    Route::post('/weekly-care-plans', [WeeklyCarePlanApiController::class, 'store']);

    // Uploads (move back inside middleware after testing)
    // Moved backed inside middleware in sk-74/family-api-fix
    Route::post('/upload', [UploadController::class, 'upload']);

    // Medication Schedule API
    // GET /medication-schedules - List all medication schedules (used for the Medication Schedule Management page in the web)
    Route::get('/medication-schedules', [MedicationScheduleApiController::class, 'index']);
    Route::get('/medication-schedules/{id}', [MedicationScheduleApiController::class, 'show']);
    Route::post('/medication-schedules', [MedicationScheduleApiController::class, 'store']);
    Route::put('/medication-schedules/{id}', [MedicationScheduleApiController::class, 'update']);
    Route::delete('/medication-schedules/{id}', [MedicationScheduleApiController::class, 'destroy']);

    // Internal Appointments API (Staff Only, Full CRUD)
    Route::get('/internal-appointments', [InternalAppointmentsApiController::class, 'index']);
    // Route::post('/internal-appointments/{id}/cancel', [InternalAppointmentsApiController::class, 'cancel']);
    Route::get('/internal-appointments/calendar-events', [InternalAppointmentsApiController::class, 'calendarEvents']);
    Route::get('/internal-appointments/types', [InternalAppointmentsApiController::class, 'listAppointmentTypes']);
    Route::get('/internal-appointments/staff', [InternalAppointmentsApiController::class, 'listStaff']);
    Route::get('/internal-appointments/{id}', [InternalAppointmentsApiController::class, 'show']);

    // REMOVE these lines (do not expose beneficiary/family endpoints for internal appointments):
    // Route::get('/internal-appointments/beneficiaries', ...);
    // Route::get('/internal-appointments/family-members', ...);

    // Visitation API (Read-Only for Mobile)
    Route::get('/visitations', [VisitationApiController::class, 'index']);
    Route::get('/visitations/calendar-events', [VisitationApiController::class, 'calendarEvents']);
    Route::get('/visitations/beneficiaries', [VisitationApiController::class, 'listBeneficiaries']);
    Route::get('/visitations/{id}', [VisitationApiController::class, 'show']);
    Route::get('/visitations/beneficiary/{id}', [VisitationApiController::class, 'showBeneficiary']);

    // Messaging API (for mobile, Supabase sockets)
    Route::post('/messaging/thread', [MessagingApiController::class, 'createThread']);
    Route::get('/messaging/thread', [MessagingApiController::class, 'listThreads']);
    Route::delete('/messaging/thread', [MessagingApiController::class, 'deleteThread']);
    Route::get('/messaging/thread/{id}/messages', [MessagingApiController::class, 'getThreadMessages']);
    Route::post('/messaging/thread/{id}/message', [MessagingApiController::class, 'sendMessage']);

    // Messaging Group management endpoints
    Route::get('/messaging/thread/{id}/members', [MessagingApiController::class, 'getThreadMembers']);
    Route::post('/messaging/thread/{id}/add-member', [MessagingApiController::class, 'addThreadMember']);
    Route::post('/messaging/thread/{id}/leave', [MessagingApiController::class, 'leaveThread']);

    // Records Management API
    // Weekly Care Plans
    Route::get('/records/weekly-care-plans', [RecordsManagementApiController::class, 'listWeekly']);
    Route::get('/records/weekly-care-plans/{id}', [RecordsManagementApiController::class, 'showWeekly']);
    Route::patch('/records/weekly-care-plans/{id}', [RecordsManagementApiController::class, 'updateWeekly']);

    // General Care Plans REMOVED
    // Route::get('/records/general-care-plans', [\App\Http\Controllers\Api\RecordsManagementApiController::class, 'listGeneral']);
    // Route::get('/records/general-care-plans/{id}', [\App\Http\Controllers\Api\RecordsManagementApiController::class, 'showGeneral']);
    // Route::put('/records/general-care-plans/{id}', [\App\Http\Controllers\Api\RecordsManagementApiController::class, 'updateGeneral']);

    // Shifts API
    Route::get('/shifts', [ShiftApiController::class, 'index']);
    Route::post('/shifts', [ShiftApiController::class, 'store']);
    Route::patch('/shifts/{shift}', [ShiftApiController::class, 'update']);
    Route::get('/shifts/{shift}', [ShiftApiController::class, 'show']);

    // Shift Tracks API
    Route::get('/shifts/{shift}/tracks', [ShiftTrackApiController::class, 'index']);
    Route::post('/shifts/{shift}/tracks', [ShiftTrackApiController::class, 'store']);
    Route::post('/shifts/{shift}/tracks/bulk', [ShiftTrackApiController::class, 'bulkStore']);
});