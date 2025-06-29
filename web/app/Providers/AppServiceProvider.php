<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User; // This is for cose_users
use App\Observers\CoseUserObserver;
use App\Models\Beneficiary; 
use App\Observers\BeneficiaryObserver;
use App\Models\FamilyMember;
use App\Observers\FamilyMemberObserver;
use App\Models\LanguagePreference;
use Illuminate\Support\Facades\URL; 
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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

        // Add language preference to all views
        View::composer('*', function ($view) {
            try {
                $useTagalog = false;
                
                // Check for authenticated users
                if (Auth::guard('web')->check()) {
                    $useTagalog = \App\Models\LanguagePreference::where('user_type', 'cose_user')
                        ->where('user_id', Auth::guard('web')->id())
                        ->exists();
                } elseif (Auth::guard('beneficiary')->check()) {
                    $useTagalog = \App\Models\LanguagePreference::where('user_type', 'beneficiary')
                        ->where('user_id', Auth::guard('beneficiary')->user()->beneficiary_id)
                        ->exists();
                } elseif (Auth::guard('family')->check()) {
                    $useTagalog = \App\Models\LanguagePreference::where('user_type', 'family_member')
                        ->where('user_id', Auth::guard('family')->user()->family_member_id)
                        ->exists();
                } else {
                    // Check cookie for guest users
                    $useTagalog = request()->cookie('use_tagalog') === '1';
                }
                
                $view->with('useTagalog', $useTagalog);
            } catch (\Exception $e) {
                \Log::error('Error in language preference view composer: ' . $e->getMessage());
                $view->with('useTagalog', false);
            }
        });
    }
}
