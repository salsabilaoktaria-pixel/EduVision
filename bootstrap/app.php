<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Kalau sesi/CSRF token kadaluarsa (419 Page Expired) — misalnya karena
        // tab dibiarkan terbuka lama, atau membuka halaman dari cache browser
        // setelah logout — user diarahkan balik ke halaman login dengan pesan,
        // bukan ditampilkan halaman error mentah "419 Page Expired".
        // CATATAN: Laravel mengubah TokenMismatchException menjadi HttpException
        // berstatus 419 SEBELUM callback render dipanggil, jadi yang ditangkap
        // di sini adalah HttpException dengan status 419 (bukan class aslinya).
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null; // error HTTP lain tetap ditangani seperti biasa
            }
            return redirect()->route('login')->with('gagal', 'Sesi kamu sudah berakhir, silakan masuk lagi.');
        });
    })->create();
