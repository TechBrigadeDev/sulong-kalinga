<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User; // This is for cose_users
use App\Observers\CoseUserObserver;
use App\Models\Beneficiary;
use App\Observers\BeneficiaryObserver;
use App\Models\FamilyMember;
use App\Observers\FamilyMemberObserver;

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
        Beneficiary::observe(BeneficiaryObserver::class);
        FamilyMember::observe(FamilyMemberObserver::class);

        // Force HTTPS
        URL::forceScheme('https');
    }
}
