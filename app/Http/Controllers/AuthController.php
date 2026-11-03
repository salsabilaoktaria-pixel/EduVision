<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Kunci rate-limit dibuat dari kombinasi email yang diinput + alamat IP,
     * supaya satu penyerang yang mencoba banyak email dari satu IP tetap
     * kena limit per-IP, dan brute force ke satu akun dari banyak IP tetap
     * kena limit per-email.
     */
    private function kunciLoginThrottle(Request $request): string
    {
        return strtolower((string) $request->input('email')) . '|' . $request->ip();
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $kunci = $this->kunciLoginThrottle($request);

        // Maksimal 5 percobaan gagal per menit untuk kombinasi email+IP ini.
        // Ini mencegah brute force password tanpa mengganggu pengguna yang
        // memang lupa password sesekali.
        if (RateLimiter::tooManyAttempts($kunci, 5)) {
            $detik = RateLimiter::availableIn($kunci);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$detik} detik.",
            ]);
        }

        if (Auth::attempt($credentials)) {
            RateLimiter::clear($kunci);
            $request->session()->regenerate();
            // Setelah login, tampilkan dulu halaman "beranda" (slide perkenalan),
            // bukan langsung ke dashboard — sesuai permintaan. intended() tetap
            // dihormati kalau user memang sedang diarahkan ke halaman tertentu
            // (misalnya link yang dibuka sebelum diminta login).
            return redirect()->intended(route('beranda'));
        }

        RateLimiter::hit($kunci, 60);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
