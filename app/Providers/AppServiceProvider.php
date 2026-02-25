<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

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
        // Fix for MySQL key length on shared hosting
        Schema::defaultStringLength(191);

        // Set custom pagination view as default
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.custom');

        // Enforce strong password policy across the entire application
        Password::defaults(function () {
            return Password::min(12)     // Minimum 12 characters
                ->mixedCase()           // Upper + lowercase letters
                ->letters()             // At least one letter
                ->numbers()             // At least one number
                ->symbols()             // At least one special character
                ->uncompromised();      // Must not appear in known data breaches (HIBP)
        });

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
