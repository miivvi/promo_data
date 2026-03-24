<?php

namespace App\Providers;

use App\Repositories\ReportProcess\ReportProcessContract;
use App\Repositories\ReportProcess\ReportProcessRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ReportProcessContract::class, ReportProcessRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
