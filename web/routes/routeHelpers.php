<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;

/**
 * Register messaging routes for a specific user role
 */
function registerMessagingRoutes($rolePrefix, $roleName) {
    // Use Route::group() instead of nested closures
    Route::group([
        'prefix' => $rolePrefix,
        'as' => "{$rolePrefix}.",
        'middleware' => ['auth', "App\Http\Middleware\CheckRole:{$roleName}"]
    ], function () {
        Route::prefix('messaging')->name('messaging.')->group(function () {
            Route::get('/', [MessageController::class, 'index'])->name('index');
            Route::get('/conversation/{id}', [MessageController::class, 'viewConversation'])->name('conversation');
            Route::post('/send-message', [MessageController::class, 'sendMessage'])->name('send');
            Route::post('/create-conversation', [MessageController::class, 'createConversation'])->name('create');
            Route::post('/create-group', [MessageController::class, 'createGroupConversation'])->name('create-group');
            Route::post('/mark-as-read', [MessageController::class, 'markAsRead'])->name('read');
            Route::get('/unread-count', [MessageController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/recent-messages', [MessageController::class, 'getRecentMessages'])->name('recent');
        });
    });
}