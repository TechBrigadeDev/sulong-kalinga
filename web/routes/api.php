<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\CareManagerApiController;
use App\Http\Controllers\Api\CareWorkerApiController;
use App\Http\Controllers\Api\BeneficiaryApiController;
use App\Http\Controllers\Api\MunicipalityApiController;
use App\Http\Controllers\Api\FamilyMemberApiController;

// Public routes
Route::get('/public-test', function () {
    return response(['message' => 'Public API is working!'], 200);
});

// Authentication Routes
Route::post('/login', [AuthApiController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth Routes
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/user', [AuthApiController::class, 'user']);
    
    // User Profile Routes
    Route::put('/profile', [UserApiController::class, 'updateProfile']);
    
    // Care Manager Routes
    Route::get('/care-managers', [CareManagerApiController::class, 'index']);
    Route::get('/care-managers/{id}', [CareManagerApiController::class, 'show']);
    Route::post('/care-managers', [CareManagerApiController::class, 'store']);
    Route::put('/care-managers/{id}', [CareManagerApiController::class, 'update']);
    Route::delete('/care-managers/{id}', [CareManagerApiController::class, 'destroy']);
    
    // Care Worker Routes
    Route::get('/care-workers', [CareWorkerApiController::class, 'index']);
    Route::get('/care-workers/{id}', [CareWorkerApiController::class, 'show']);
    Route::post('/care-workers', [CareWorkerApiController::class, 'store']);
    Route::put('/care-workers/{id}', [CareWorkerApiController::class, 'update']);
    Route::delete('/care-workers/{id}', [CareWorkerApiController::class, 'destroy']);
    
    // Beneficiary Routes
    Route::get('/beneficiaries', [BeneficiaryApiController::class, 'index']);
    Route::get('/beneficiaries/{id}', [BeneficiaryApiController::class, 'show']);
    Route::post('/beneficiaries', [BeneficiaryApiController::class, 'store']);
    Route::put('/beneficiaries/{id}', [BeneficiaryApiController::class, 'update']);
    Route::delete('/beneficiaries/{id}', [BeneficiaryApiController::class, 'destroy']);
    
    // Municipality Routes
    Route::get('/municipalities', [MunicipalityApiController::class, 'index']);
    Route::get('/municipalities/{id}', [MunicipalityApiController::class, 'show']);
    Route::get('/provinces', [MunicipalityApiController::class, 'provinces']);
    
    // Family Member Routes
    Route::get('/family-members', [FamilyMemberApiController::class, 'index']);
    Route::get('/family-members/{id}', [FamilyMemberApiController::class, 'show']);
    Route::post('/family-members', [FamilyMemberApiController::class, 'store']);
    Route::put('/family-members/{id}', [FamilyMemberApiController::class, 'update']);
    Route::delete('/family-members/{id}', [FamilyMemberApiController::class, 'destroy']);

    // Notification Routes
    Route::post('/mobile-notifications', [NotificationController::class, 'store']);
});