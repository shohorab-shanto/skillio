<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\MentorAuth;
use App\Http\Middleware\UserAuth;

class RouteServiceProvider extends ServiceProvider
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
        // Register route middleware aliases
        Route::aliasMiddleware('admin_auth', AdminAuth::class);
        Route::aliasMiddleware('mentor_auth', MentorAuth::class);
        Route::aliasMiddleware('user_auth', UserAuth::class);
        Route::aliasMiddleware('onboarding_complete', \App\Http\Middleware\EnsureUserOnboardingComplete::class);
        Route::aliasMiddleware('set_locale', \App\Http\Middleware\SetLocale::class);
    }
}