<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Skip view caching in production to avoid missing component errors
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\Config::set('view.cache', false);
        }
    }
}
