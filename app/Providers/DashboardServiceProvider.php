<?php

declare(strict_types=1);

namespace App\Providers;


use App\Domain\Dashboard\Contracts\DashboardRepositoryInterface;
use App\Domain\Dashboard\Repositories\DashboardRepository;
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