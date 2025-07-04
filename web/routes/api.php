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
use App\Http\Controllers\Api\NotificationsApiController;
use App\Http\Controllers\Api\FcmApiController;
use App\Http\Controllers\Api\ShiftApiController;
use App\Http\Controllers\Api\ShiftTrackApiController;
use App\Http\Controllers\Api\RecordsManagementApiController;
use App\Http\Middleware\RoleMiddleware;
use App\Services\NotificationService;

// Public routes
Route::get('/public-test', function () {
    return response(['message' => 'Public API is working!'], 200);
});

// Authentication Routes
Route::post('/login', [AuthApiController::class, 'login']);

// Route::get('/test-google-map', [\App\Http\Controllers\BeneficiaryController::class, 'testGoogleMap']);


// Protected Routes
Route::middleware('auth:sanctum', \App\Http\Middleware\RoleMiddleware::class . ':admin,care_manager,care_worker')->group(function () {
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
    Route::get('/beneficiaries/export', [BeneficiaryApiController::class, 'export']);
    Route::get('/beneficiaries/{id}', [BeneficiaryApiController::class, 'show']);
    Route::post('/beneficiaries', [BeneficiaryApiController::class, 'store']);
    Route::put('/beneficiaries/{id}', [BeneficiaryApiController::class, 'update']);
    Route::patch('/beneficiaries/{id}/status', [BeneficiaryApiController::class, 'changeStatus']);
    // Route::delete('/beneficiaries/{id}', [BeneficiaryApiController::class, 'destroy']);
    // Route::post('/beneficiaries/{id}/restore', [BeneficiaryApiController::class, 'restore']);

    // Family Member Management
    Route::get('/family-members', [FamilyMemberApiController::class, 'index']);
    Route::get('/family-members/export', [FamilyMemberApiController::class, 'export']);
    Route::get('/family-members/{id}', [FamilyMemberApiController::class, 'show']);
    Route::post('/family-members', [FamilyMemberApiController::class, 'store']);
    Route::put('/family-members/{id}', [FamilyMemberApiController::class, 'update']);
    // Route::delete('/family-members/{id}', [FamilyMemberApiController::class, 'destroy']);

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
    // Route::get('/portal-account/{id}/users', [PortalAccountApiController::class, 'getPortalAccountUsers']);
    // Route::post('/portal-account/select-user', [PortalAccountApiController::class, 'selectPortalUser']);

    // Notifications
    Route::get('/notifications', [NotificationsApiController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationsApiController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationsApiController::class, 'markAllAsRead']);

    // FCM Push Notifications
    Route::post('/fcm/register', [FcmApiController::class, 'register']);
    Route::get('/fcm/token', [FcmApiController::class, 'getToken']);

    // Reports Management API
    Route::get('/reports', [ReportsApiController::class, 'index']);
    Route::get('/reports/{id}', [ReportsApiController::class, 'show']);
    Route::put('/reports/{id}', [ReportsApiController::class, 'update']);

    // Weekly Care Plan (WCP) API
    // Create only with store
    Route::post('/weekly-care-plans', [WeeklyCarePlanApiController::class, 'store']);
    Route::get('/interventions/by-category', [WeeklyCarePlanApiController::class, 'getInterventionsByCategory']);

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
    Route::post('/records/weekly-care-plans/{id}', [RecordsManagementApiController::class, 'updateWeekly']);

    // General Care Plans REMOVED
    // Route::get('/records/general-care-plans', [\App\Http\Controllers\Api\RecordsManagementApiController::class, 'listGeneral']);
    // Route::get('/records/general-care-plans/{id}', [\App\Http\Controllers\Api\RecordsManagementApiController::class, 'showGeneral']);
    // Route::put('/records/general-care-plans/{id}', [\App\Http\Controllers\Api\RecordsManagementApiController::class, 'updateGeneral']);

    // Shifts API (refactored)
    Route::get('/shifts', [ShiftApiController::class, 'index']); // Now returns upcoming scheduled visitations for a care worker
    Route::get('/shifts/archived', [ShiftApiController::class, 'archived']); // New: completed shifts
    Route::post('/shifts/time-in', [ShiftApiController::class, 'timeIn']); // Renamed from store
    Route::get('/shifts/current', [ShiftApiController::class, 'current']);
    Route::patch('/shifts/{shift}/time-out', [ShiftApiController::class, 'timeOut']); // Renamed from update
    Route::get('/shifts/{shift}', [ShiftApiController::class, 'show']); // Show shift details including tracks and visitations

    // Assigned Visitations for a Care Worker (for the day)
    // Route::get('/assigned-visitations', [ShiftApiController::class, 'getAssignedVisitations']);

    // Shift Tracks API (arrival/departure events only)
    // Route::get('/shifts/{shift}/tracks', [ShiftTrackApiController::class, 'index']);
    Route::post('/shifts/{shift}/tracks/event', [ShiftTrackApiController::class, 'event']);

    // REMOVED
    // Route::post('/shifts/{shift}/tracks', [ShiftTrackApiController::class, 'store']);
    // Route::post('/shifts/{shift}/tracks/bulk', [ShiftTrackApiController::class, 'bulkStore']);

    
});
require __DIR__.'/apiBeneficiaryRoutes.php';
require __DIR__.'/apiFamilyRoutes.php';

// // Test route to register a token
// Route::post('/test/register-token', function (Request $request) {
//     $request->validate([
//         'user_id' => 'required|integer',
//         'role' => 'required|string|in:cose_staff,beneficiary,family_member',
//         'token' => 'required|string'
//     ]);
    
//     try {
//         $service = new NotificationService();
//         $result = $service->register($request->user_id, $request->role, $request->token);
        
//         return response()->json([
//             'success' => true,
//             'message' => 'Token registered successfully',
//             'data' => $result
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage()
//         ], 400);
//     }
// });

// // Test route to send push notification
// Route::post('/test/send-push', function (Request $request) {
//     $request->validate([
//         'user_id' => 'required|integer',
//         'role' => 'required|string|in:cose_staff,beneficiary,family_member',
//         'title' => 'required|string',
//         'message' => 'required|string'
//     ]);
    
//     try {
//         $service = new NotificationService();
        
//         switch ($request->role) {
//             case 'cose_staff':
//                 $result = $service->notifyStaff($request->user_id, $request->title, $request->message);
//                 break;
//             case 'beneficiary':
//                 $result = $service->notifyBeneficiary($request->user_id, $request->title, $request->message);
//                 break;
//             case 'family_member':
//                 $result = $service->notifyFamilyMember($request->user_id, $request->title, $request->message);
//                 break;
//         }
        
//         return response()->json([
//             'success' => true,
//             'message' => 'Notification sent successfully',
//             'data' => $result
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage()
//         ], 400);
//     }
// });

// // Test route to check if token exists
// Route::get('/test/check-token/{user_id}/{role}', function ($userId, $role) {
//     $service = new NotificationService();
//     $token = $service->getTokenByUser($userId, $role);
    
//     return response()->json([
//         'user_id' => $userId,
//         'role' => $role,
//         'token_exists' => $token ? true : false,
//         'token' => $token ? $token->token : null
//     ]);
// });
// Route::get('/debug/providers', function () {
//     return array_keys(app()->getLoadedProviders());
// });