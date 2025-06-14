<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request;

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
        // Tambahkan macro untuk menghindari gethostbyaddr()
        Request::macro('getHostNameSafe', function () {
            return $_SERVER['REMOTE_ADDR'] ?? 'localhost';
        });
    }
}