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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(CoseUserObserver::class); // This observes cose_users
        PortalAccount::observe(PortalAccountObserver::class);
    }
}
