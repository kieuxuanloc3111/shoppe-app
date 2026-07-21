<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Notifications\ResetPassword;
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
        //
        Paginator::useBootstrap();

        // reset link points to React SPA, not blade route
        ResetPassword::createUrlUsing(function ($user, string $token) {
            $frontend = env('FRONTEND_URL', 'http://localhost:3000');
            return "{$frontend}/reset-password?token={$token}&email=" . urlencode($user->getEmailForPasswordReset());
        });
    }
}
