<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\App\AppLogger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Logger
        $this->app->singleton('AppLog', function () {
            return new AppLogger();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
