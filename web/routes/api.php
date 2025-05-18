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

// Public routes
Route::get('/public-test', function () {
    return response(['message' => 'Public API is working!'], 200);
});

// Authentication Routes
Route::post('/login', [AuthApiController::class, 'login']);

// Uploads (move back inside middleware after testing)
Route::post('/upload', [UploadController::class, 'upload']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth/User Profile
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/user', [AuthApiController::class, 'user']);
    Route::put('/profile', [UserApiController::class, 'updateProfile']);

    // Beneficiary Management
    Route::get('/beneficiaries', [BeneficiaryApiController::class, 'index']);
    Route::get('/beneficiaries/{id}', [BeneficiaryApiController::class, 'show']);
    Route::post('/beneficiaries', [BeneficiaryApiController::class, 'store']);
    Route::put('/beneficiaries/{id}', [BeneficiaryApiController::class, 'update']);
    Route::patch('/beneficiaries/{id}/status', [BeneficiaryApiController::class, 'changeStatus']);
    Route::delete('/beneficiaries/{id}', [BeneficiaryApiController::class, 'destroy']);

    // Family Member Management
    Route::get('/family-members', [FamilyMemberApiController::class, 'index']);
    Route::get('/family-members/{id}', [FamilyMemberApiController::class, 'show']);
    Route::post('/family-members', [FamilyMemberApiController::class, 'store']);
    Route::put('/family-members/{id}', [FamilyMemberApiController::class, 'update']);
    Route::delete('/family-members/{id}', [FamilyMemberApiController::class, 'destroy']);

    // Admin Read-Only
    Route::get('/admins', [AdminApiController::class, 'index']);
    Route::get('/admins/{id}', [AdminApiController::class, 'show']);

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
});