<?php
// filepath: web/routes/apiBeneficiaryRoutes.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Portal\ProfileApiController;
use App\Http\Controllers\Api\Portal\VisitationScheduleApiController;
use App\Http\Controllers\Api\Portal\MedicationScheduleApiController;
use App\Http\Controllers\Api\Portal\EmergencyServiceRequestApiController;
use App\Http\Controllers\Api\Portal\CarePlanApiController;
use App\Http\Controllers\Api\Portal\PortalBeneficiariesApiController;
use App\Http\Controllers\Api\NotificationsApiController;
use App\Http\Controllers\Api\Portal\MessagingApiController;
use App\Http\Controllers\Api\Portal\FaqApiController;
use App\Http\Controllers\Api\Portal\RelativesApiController;
use App\Http\Middleware\RoleMiddleware;

// All routes in this group require beneficiary authentication
Route::middleware(['auth:sanctum', \App\Http\Middleware\RoleMiddleware::class . ':beneficiary'])->prefix('portal/beneficiary')->group(function () {
    // Profile
    Route::get('/profile', [ProfileApiController::class, 'show']);
    // Route::post('/profile/update-username', [ProfileApiController::class, 'updateUsername']); // DO NOT IMPLEMNT
    Route::post('/profile/update-password', [ProfileApiController::class, 'updatePassword']);

    // Visitation Schedule
    Route::get('/visitation-schedule/events', [VisitationScheduleApiController::class, 'events']);
    Route::get('/visitation-schedule/details/{id}', [VisitationScheduleApiController::class, 'details']);
    Route::get('/visitation-schedule/upcoming', [VisitationScheduleApiController::class, 'upcoming']);

    // Medication Schedule
    Route::get('/medication-schedule', [MedicationScheduleApiController::class, 'index']);
    Route::get('/medication-schedule/next', [MedicationScheduleApiController::class, 'next']);

    // Emergency & Service Requests
    Route::get('/emergency-service/active', [EmergencyServiceRequestApiController::class, 'active']);
    Route::post('/emergency-service/history', [EmergencyServiceRequestApiController::class, 'history']);
    Route::post('/emergency-service/emergency/submit', [EmergencyServiceRequestApiController::class, 'submitEmergency']);
    Route::post('/emergency-service/service/submit', [EmergencyServiceRequestApiController::class, 'submitService']);
    Route::put('/emergency-service/emergency/{id}', [EmergencyServiceRequestApiController::class, 'updateEmergency']); // NEW
    Route::put('/emergency-service/service/{id}', [EmergencyServiceRequestApiController::class, 'updateService']);     // NEW
    Route::delete('/emergency-service/emergency/{id}', [EmergencyServiceRequestApiController::class, 'deleteEmergency']); // NEW
    Route::delete('/emergency-service/service/{id}', [EmergencyServiceRequestApiController::class, 'deleteService']);     // NEW
    Route::post('/emergency-service/cancel', [EmergencyServiceRequestApiController::class, 'cancel']);
    Route::get('/emergency-service/emergency/{id}', [EmergencyServiceRequestApiController::class, 'emergencyDetails']);
    Route::get('/emergency-service/service/{id}', [EmergencyServiceRequestApiController::class, 'serviceDetails']);

    // Care Plan
    Route::get('/care-plan', [CarePlanApiController::class, 'index']);
    Route::get('/care-plan/statistics', [CarePlanApiController::class, 'statistics']);
    Route::get('/care-plan/view/{id}', [CarePlanApiController::class, 'view']);
    Route::post('/care-plan/acknowledge/{id}', [CarePlanApiController::class, 'acknowledge']);

    // Family Members
    // Route::get('/family-members', [FamilyMembersApiController::class, 'index']); DO NOT IMPLEMENT

    // Benefciaries
    Route::get('/index', [PortalBeneficiariesApiController::class, 'index']);

    // Notifications
    Route::get('/notifications', [NotificationsApiController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationsApiController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationsApiController::class, 'markAllAsRead']);

    // Messaging
    Route::get('/messaging/unread-count', [MessagingApiController::class, 'unreadCount']);
    Route::get('/messaging/recent-messages', [MessagingApiController::class, 'recentMessages']);
    Route::post('/messaging/read-all', [MessagingApiController::class, 'markAllAsRead']);

    // FAQ
    Route::get('/faq', [FaqApiController::class, 'index']);

    // Relatives (beneficiary/family member relationships)
    Route::get('/relatives', [RelativesApiController::class, 'index']);
});