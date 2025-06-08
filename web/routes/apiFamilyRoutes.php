<?php
// filepath: web/routes/apiFamilyRoutes.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Portal\ProfileApiController;
use App\Http\Controllers\Api\Portal\VisitationScheduleApiController;
use App\Http\Controllers\Api\Portal\MedicationScheduleApiController;
use App\Http\Controllers\Api\Portal\EmergencyServiceRequestApiController;
use App\Http\Controllers\Api\Portal\CarePlanApiController;
use App\Http\Controllers\Api\Portal\PortalFamilyMembersApiController;
use App\Http\Controllers\Api\NotificationsApiController;
use App\Http\Controllers\Api\Portal\MessagingApiController;
use App\Http\Controllers\Api\Portal\FaqApiController;
use App\Http\Middleware\RoleMiddleware;

// All routes in this group require family member authentication
Route::middleware(['auth:sanctum', \App\Http\Middleware\RoleMiddleware::class . ':family_member'])->prefix('portal/family')->group(function () {
    // Profile
    Route::get('/profile', [ProfileApiController::class, 'show']);
    Route::post('/profile/update-email', [ProfileApiController::class, 'updateEmail']);
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
    Route::put('/emergency-service/emergency/{id}', [EmergencyServiceRequestApiController::class, 'updateEmergency']);
    Route::put('/emergency-service/service/{id}', [EmergencyServiceRequestApiController::class, 'updateService']);
    Route::delete('/emergency-service/emergency/{id}', [EmergencyServiceRequestApiController::class, 'deleteEmergency']);
    Route::delete('/emergency-service/service/{id}', [EmergencyServiceRequestApiController::class, 'deleteService']);
    Route::post('/emergency-service/cancel', [EmergencyServiceRequestApiController::class, 'cancel']);
    Route::get('/emergency-service/emergency/{id}', [EmergencyServiceRequestApiController::class, 'emergencyDetails']);
    Route::get('/emergency-service/service/{id}', [EmergencyServiceRequestApiController::class, 'serviceDetails']);

    // Care Plan
    Route::get('/care-plan', [CarePlanApiController::class, 'index']);
    Route::get('/care-plan/statistics', [CarePlanApiController::class, 'statistics']);
    Route::get('/care-plan/view/{id}', [CarePlanApiController::class, 'view']);
    Route::post('/care-plan/acknowledge/{id}', [CarePlanApiController::class, 'acknowledge']);

    // Family Members
    Route::get('/index', [PortalFamilyMembersApiController::class, 'index']);

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
});