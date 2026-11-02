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
        // ---- Sinkronisasi data: perubahan oleh admin/guru langsung tercermin di akun siswa/guru ----
        \App\Models\Siswa::saved(function ($siswa) {
            if (app()->runningInConsole()) { return; } // seeder/artisan tidak mengirim notifikasi
            if ($siswa->user_id) {
                \App\Models\User::whereKey($siswa->user_id)->update(['name' => $siswa->nama]);
                if ($siswa->wasChanged('kelas_id') && !$siswa->wasRecentlyCreated) {
                    \App\Models\Notifikasi::kirim($siswa->user_id, 'info', 'Data kelasmu diperbarui', 'Kelasmu sekarang: ' . ($siswa->kelas->nama_kelas ?? '-'), route('dashboard'));
                }
            }
        });
        \App\Models\Guru::saved(function ($guru) {
            if ($guru->user_id) {
                \App\Models\User::whereKey($guru->user_id)->update(['name' => $guru->nama]);
            }
        });
        \App\Models\Nilai::saved(function ($n) {
            if (app()->runningInConsole()) { return; } // seeder/artisan tidak mengirim notifikasi
            \App\Models\Notifikasi::kirim($n->siswa->user_id ?? null, 'nilai', 'Nilai diperbarui: ' . ($n->mataPelajaran->nama_mapel ?? 'mapel'), 'Nilai akhir: ' . $n->nilai_akhir, route('rapor.saya'));
        });
        \App\Models\Prestasi::created(function ($p) {
            if (app()->runningInConsole()) { return; } // seeder/artisan tidak mengirim notifikasi
            \App\Models\Notifikasi::kirim($p->siswa->user_id ?? null, 'info', 'Prestasi baru tercatat', $p->nama_prestasi, route('dashboard'));
        });
        \App\Models\JadwalPelajaran::saved(function ($j) {
            if (app()->runningInConsole()) { return; } // seeder/artisan tidak mengirim notifikasi
            \App\Models\Notifikasi::kirimKeKelas($j->kelas_id, 'info', 'Jadwal pelajaran diperbarui', 'Cek jadwal terbaru kelasmu.', route('jadwal.saya'));
        });
    }
}
