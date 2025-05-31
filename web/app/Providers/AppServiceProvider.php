<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User; // This is for cose_users
use App\Observers\CoseUserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UserManagementService::class, function ($app) {
            return new UserManagementService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(CoseUserObserver::class); // This observes cose_users

        // Force HTTPS in production
        if (config('app.env') === 'production' || config('app.env') === 'staging') {
            \URL::forceScheme('https');
        }
    }
}
