<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Lapisan keamanan kedua untuk aksi sensitif (lihat/reset password akun orang lain):
 * admin harus memasukkan ulang password miliknya. Konfirmasi berlaku 10 menit.
 */
class KonfirmasiPassword
{
    public function handle(Request $request, Closure $next)
    {
        $pada = (int) $request->session()->get('konfirmasi_password_pada', 0);
        if ($pada < now()->subMinutes(10)->timestamp) {
            $request->session()->put('konfirmasi_tujuan', $request->isMethod('get') ? $request->fullUrl() : url()->previous());
            return redirect()->route('password.konfirmasi');
        }
        return $next($request);
    }
}
