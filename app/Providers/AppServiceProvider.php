<?php

namespace App\Providers;

use App\Models\Applicant;
use App\Models\User;
use App\Policies\ApplicantPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 🎯 Force HTTPS scheme on production/Render
        if (config('app.env') !== 'local' || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }
        
        // ============================================
        // ✅ Super Admin bypasses ALL policy checks
        // ============================================
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // ============================================
        // ✅ Register Policies
        // ============================================
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Applicant::class, ApplicantPolicy::class);
    }
}