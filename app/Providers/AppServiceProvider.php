<?php

namespace App\Providers;

<<<<<<< HEAD
use App\Support\GandaHasher;
use Illuminate\Support\Facades\Hash;
=======
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
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
<<<<<<< HEAD
        // Driver hash "ganda" (double hashing: HMAC-SHA256 berkunci APP_KEY -> bcrypt).
        // Dipakai sebagai default lewat config/hashing.php.
        Hash::extend('ganda', fn ($app) => new GandaHasher($app['config']->get('hashing.bcrypt', [])));
=======
        //
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
    }
}
