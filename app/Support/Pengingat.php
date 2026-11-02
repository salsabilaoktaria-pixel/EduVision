<?php

namespace App\Support;

use App\Models\Notifikasi;
use App\Models\PengumpulanTugas;
use App\Models\Spp;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;

/**
 * Pengingat otomatis (deadline tugas H-2, SPP belum lunas). Dibuat "malas" (lazy):
 * dijalankan saat pengguna membuka halaman / polling, maksimal sekali per 30 menit per akun,
 * dan tidak pernah dobel di hari yang sama (dicek lewat judul + tautan + tanggal).
 */
class Pengingat
{
    public static function jalankan(User $user): void
    {
        $kunci = 'pengingat_' . $user->id;
        if (cache()->has($kunci)) {
            return;
        }
        cache()->put($kunci, 1, now()->addMinutes(30));

        try {
            if ($user->isSiswa() && $user->siswa) {
                self::untukSiswa($user);
            } elseif ($user->isGuru() && $user->guru) {
                self::untukGuru($user);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private static function sudahHariIni(int $userId, string $judul): bool
    {
        return Notifikasi::where('user_id', $userId)->where('judul', $judul)
            ->whereDate('created_at', today())->exists();
    }

    private static function untukSiswa(User $user): void
    {
        $siswa = $user->siswa;

        // Tugas yang belum dikumpulkan dan deadline-nya <= 2 hari lagi (atau baru lewat hari ini).
        $sudah = PengumpulanTugas::where('siswa_id', $siswa->id)->pluck('tugas_id');
        $tugas = Tugas::where('kelas_id', $siswa->kelas_id)->whereNotIn('id', $sudah)
            ->whereNotNull('deadline')
            ->whereBetween('deadline', [now()->startOfDay(), now()->addDays(2)->endOfDay()])
            ->get();
        foreach ($tugas as $t) {
            $judul = 'Deadline tugas: ' . $t->judul;
            if (self::sudahHariIni($user->id, $judul)) {
                continue;
            }
            Notifikasi::kirim($user->id, 'tugas', $judul,
                'Batas pengumpulan ' . Carbon::parse($t->deadline)->translatedFormat('l, d F Y H:i') . ' — segera kumpulkan.',
                route('tugas.saya'));
        }

        // SPP belum lunas.
        $belum = Spp::where('siswa_id', $siswa->id)->where('status', 'Belum Lunas')->count();
        if ($belum > 0) {
            $judul = 'Pengingat SPP: ' . $belum . ' tagihan belum lunas';
            if (!self::sudahHariIni($user->id, $judul)) {
                Notifikasi::kirim($user->id, 'spp', $judul, 'Segera selesaikan pembayaran SPP-mu.', route('spp.saya'));
            }
        }
    }

    private static function untukGuru(User $user): void
    {
        // Tugas milik guru ini yang deadline-nya sudah lewat dan masih ada pengumpulan belum dinilai.
        $tugas = Tugas::where('guru_id', $user->guru->id)->get();
        foreach ($tugas as $t) {
            $belumDinilai = PengumpulanTugas::where('tugas_id', $t->id)->whereNull('nilai')->count();
            if ($belumDinilai > 0) {
                $judul = 'Belum dinilai: ' . $t->judul;
                if (!self::sudahHariIni($user->id, $judul)) {
                    Notifikasi::kirim($user->id, 'tugas', $judul, $belumDinilai . ' pengumpulan menunggu penilaianmu.', route('tugas.show', $t));
                }
            }
        }
    }
}
