<?php

namespace App\Providers;

use App\Support\GandaHasher;
use Illuminate\Support\Facades\Hash;
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
        // Driver hash "ganda" (double hashing: HMAC-SHA256 berkunci APP_KEY -> bcrypt).
        // Dipakai sebagai default lewat config/hashing.php.
        Hash::extend('ganda', fn ($app) => new GandaHasher($app['config']->get('hashing.bcrypt', [])));
    }
}
