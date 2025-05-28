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
    Route::put('/profile', [UserApiController::class, 'updateProfile']);

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

    // Internal Appointments API (Read-Only for Mobile)

    // List all internal appointments (with occurrences and participants)
    // Internal Appointments API (Read-Only for Mobile)

    // List all internal appointments (with occurrences and participants)
    Route::get('/internal-appointments', [InternalAppointmentsApiController::class, 'index']);

    // Show a single internal appointment with all details

    // Show a single internal appointment with all details
    Route::get('/internal-appointments/{id}', [InternalAppointmentsApiController::class, 'show']);

    // Flat list of appointment events for calendar display
    Route::get('/internal-appointments/calendar-events', [InternalAppointmentsApiController::class, 'calendarEvents']);

    // Get all appointment types for dropdowns/search
    Route::get('/internal-appointments/types', [InternalAppointmentsApiController::class, 'listAppointmentTypes']);

    // Get all staff users grouped by role (for participant selection)
    Route::get('/internal-appointments/staff', [InternalAppointmentsApiController::class, 'listStaff']);

    // Get all beneficiaries (for admin/care manager)
    Route::get('/internal-appointments/beneficiaries', [InternalAppointmentsApiController::class, 'listBeneficiaries']);

    // Get all family members (for admin/care manager)
    Route::get('/internal-appointments/family-members', [InternalAppointmentsApiController::class, 'listFamilyMembers']);
    //REMOVED NON-READ ONLY ENDPOINTS

    // Flat list of appointment events for calendar display
    Route::get('/internal-appointments/calendar-events', [InternalAppointmentsApiController::class, 'calendarEvents']);

    // Get all appointment types for dropdowns/search
    Route::get('/internal-appointments/types', [InternalAppointmentsApiController::class, 'listAppointmentTypes']);

    // Get all staff users grouped by role (for participant selection)
    Route::get('/internal-appointments/staff', [InternalAppointmentsApiController::class, 'listStaff']);

    // Get all beneficiaries (for admin/care manager)
    Route::get('/internal-appointments/beneficiaries', [InternalAppointmentsApiController::class, 'listBeneficiaries']);

    // Get all family members (for admin/care manager)
    Route::get('/internal-appointments/family-members', [InternalAppointmentsApiController::class, 'listFamilyMembers']);
    //REMOVED NON-READ ONLY ENDPOINTS
    // POST /internal-appointments - Create a new internal appointment
    // Route::post('/internal-appointments', [InternalAppointmentsApiController::class, 'store']);
    // Route::post('/internal-appointments', [InternalAppointmentsApiController::class, 'store']);
    // PUT /internal-appointments/{id} - Update an internal appointment
    // Route::put('/internal-appointments/{id}', [InternalAppointmentsApiController::class, 'update']);
    // Route::put('/internal-appointments/{id}', [InternalAppointmentsApiController::class, 'update']);
    // POST /internal-appointments/{id}/cancel - Cancel (archive) an internal appointment
    // Route::post('/internal-appointments/{id}/cancel', [InternalAppointmentsApiController::class, 'cancel']);

    // Visitation API (Read-Only for Mobile)
    Route::get('/visitations', [VisitationApiController::class, 'index']);
    Route::get('/visitations/{id}', [VisitationApiController::class, 'show']);
    Route::get('/visitations/calendar-events', [VisitationApiController::class, 'calendarEvents']);
    Route::get('/visitations/beneficiary/{id}', [VisitationApiController::class, 'showBeneficiary']);
    Route::get('/visitations/beneficiaries', [VisitationApiController::class, 'listBeneficiaries']);

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
});