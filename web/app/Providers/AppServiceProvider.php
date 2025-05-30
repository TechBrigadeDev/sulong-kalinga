<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User; // This is for cose_users
use App\Models\PortalAccount;
use App\Observers\CoseUserObserver;
use App\Observers\PortalAccountObserver;

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

        $this->app->register(\App\Providers\RouteServiceProvider::class);
        
        // Conditionally register Pail only if the class exists
        if (config('app.env') === 'local' && config('app.debug') && env('USE_PAIL', false) && class_exists(\Laravel\Pail\PailServiceProvider::class)) {
            $this->app->register(\Laravel\Pail\PailServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(CoseUserObserver::class); // This observes cose_users
        PortalAccount::observe(PortalAccountObserver::class);

        // Force HTTPS in production
        if (config('app.env') === 'production' || config('app.env') === 'staging') {
            \URL::forceScheme('https');
        }
    }
}
