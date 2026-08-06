<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Dashboard\Contracts\DashboardRepositoryInterface;
use App\Repositories\Dashboard\DashboardRepository;
use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DashboardRepositoryInterface::class,
            DashboardRepository::class,
        );
    }

    public function boot(): void
    {
        //
    }
}