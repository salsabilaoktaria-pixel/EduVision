<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\LogKeamanan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Admin melihat / mereset username (email) & password awal siswa dan guru
 * untuk dibagikan. Semua akses wajib konfirmasi password admin dan tercatat di log keamanan.
 */
class KredensialController extends Controller
{
    public function formKonfirmasi()
    {
        return view('akun.konfirmasi');
    }

    public function konfirmasi(Request $request)
    {
        $request->validate(['password' => 'required']);
        $kunci = 'konfirmasi|' . Auth::id();
        if (RateLimiter::tooManyAttempts($kunci, 5)) {
            return back()->withErrors(['password' => 'Terlalu banyak percobaan. Coba lagi dalam ' . RateLimiter::availableIn($kunci) . ' detik.']);
        }
        if (!Hash::check($request->input('password'), Auth::user()->password)) {
            RateLimiter::hit($kunci, 120);
            LogKeamanan::catat('konfirmasi_gagal', 'Konfirmasi password admin salah.');
            return back()->withErrors(['password' => 'Password salah.']);
        }
        RateLimiter::clear($kunci);
        $request->session()->put('konfirmasi_password_pada', now()->timestamp);
        return redirect($request->session()->pull('konfirmasi_tujuan', route('dashboard')));
    }

    private function akunSekolahIni(User $user): User
    {
        abort_unless(in_array($user->role, ['siswa', 'guru', 'wali_kelas'], true), 403);
        abort_unless($user->sekolah_id === Auth::user()->sekolah_id, 404);
        return $user;
    }

    public function lihat(User $user)
    {
        $this->akunSekolahIni($user);
        LogKeamanan::catat('lihat_password', 'Melihat kredensial ' . $user->email, $user->id);
        $user->load('siswa.kelas', 'guru');
        return view('akun.kredensial', ['akun' => $user, 'passwordAwal' => $user->passwordAwalTerbaca(), 'passwordBaru' => session('password_baru')]);
    }

    public function reset(User $user)
    {
        $this->akunSekolahIni($user);
        $baru = Str::upper(Str::random(2)) . random_int(1000, 9999) . Str::lower(Str::random(3));
        $user->forceFill([
            'password' => Hash::make($baru),
            'password_awal' => User::enkripsiAwal($baru),
            'gagal_login' => 0,
            'dikunci_sampai' => null,
        ])->save();
        LogKeamanan::catat('reset_password', 'Reset password ' . $user->email, $user->id);
        return redirect()->route('akun.kredensial', $user)->with('password_baru', $baru)->with('sukses', 'Password berhasil direset. Catat / cetak sekarang, lalu bagikan ke pemilik akun.');
    }

    public function kartu(Request $request)
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $kelasId = $request->integer('kelas_id');
        $daftar = collect();
        if ($kelasId) {
            LogKeamanan::catat('cetak_kartu_akun', 'Kartu akun kelas #' . $kelasId);
            $daftar = Siswa::urutKelas()->where('siswa.kelas_id', $kelasId)->with('user', 'kelas')->get();
        }
        return view('akun.kartu', compact('kelas', 'kelasId', 'daftar'));
    }
}
