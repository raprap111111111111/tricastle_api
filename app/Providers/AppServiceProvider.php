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
        // 🎯 Force HTTPS safely in Production / Staging or behind HTTPS reverse proxies (Render, AWS, Heroku)
        if ($this->app->environment('production', 'staging') || $this->isHttpsRequest()) {
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

    /**
     * Safely check if current request came via HTTPS (CLI-safe)
     */
    private function isHttpsRequest(): bool
    {
        if ($this->app->runningInConsole()) {
            return false;
        }

        return request()->header('X-Forwarded-Proto') === 'https' 
            || request()->secure();
    }
}