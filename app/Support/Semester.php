<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

/**
 * Semester & tahun ajaran. Ganjil = Juli-Desember, Genap = Januari-Juni.
 * Semester yang sedang DILIHAT pengguna disimpan di sesi (dipilih lewat navbar);
 * bawaannya semester berjalan menurut tanggal hari ini.
 */
class Semester
{
    public const SESI = 'semester_dipilih';
    public const BULAN_GANJIL = ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    public const BULAN_GENAP = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];

    public static function dari($tanggal): array
    {
        $t = Carbon::parse($tanggal);
        if ($t->month >= 7) {
            return ['semester' => 'Ganjil', 'tahun_ajaran' => $t->year . '/' . ($t->year + 1)];
        }
        return ['semester' => 'Genap', 'tahun_ajaran' => ($t->year - 1) . '/' . $t->year];
    }

    public static function aktif(): array
    {
        return self::dari(now());
    }

    public static function terpilih(): array
    {
        try {
            $kunci = Session::get(self::SESI);
        } catch (\Throwable $e) {
            $kunci = null; // tanpa sesi (API/konsol) -> semester berjalan
        }
        if ($kunci && str_contains($kunci, '|')) {
            [$s, $ta] = explode('|', $kunci, 2);
            if (in_array($s, ['Ganjil', 'Genap'], true) && preg_match('/^\d{4}\/\d{4}$/', $ta)) {
                return ['semester' => $s, 'tahun_ajaran' => $ta];
            }
        }
        return self::aktif();
    }

    public static function kunci(array $s): string
    {
        return $s['semester'] . '|' . $s['tahun_ajaran'];
    }

    public static function label(array $s): string
    {
        return 'Semester ' . $s['semester'] . ' ' . $s['tahun_ajaran'];
    }

    /** Urutan kronologis semester (Ganjil 2026/2027 < Genap 2026/2027 < Ganjil 2027/2028). */
    public static function urut(array $s): int
    {
        return ((int) substr($s['tahun_ajaran'], 0, 4)) * 2 + ($s['semester'] === 'Genap' ? 1 : 0);
    }

    /** Semester otomatis: semester berjalan dan 5 semester sebelumnya. */
    public static function otomatis(int $jumlah = 6): array
    {
        $daftar = [];
        $s = self::aktif();
        for ($i = 0; $i < $jumlah; $i++) {
            $daftar[] = $s;
            [$a, $b] = array_map('intval', explode('/', $s['tahun_ajaran']));
            $s = $s['semester'] === 'Ganjil'
                ? ['semester' => 'Genap', 'tahun_ajaran' => $a . '/' . $b]
                : ['semester' => 'Ganjil', 'tahun_ajaran' => ($a - 1) . '/' . ($b - 1)];
        }
        return $daftar;
    }

    /**
     * Daftar semester yang bisa dipilih, SINKRON dengan data: semester berjalan + semua semester yang
     * sudah punya data (nilai, tugas, materi, jadwal, absensi, SPP, prestasi, aspirasi) + semester
     * tambahan yang didaftarkan admin (menu Kelola Semester). Terbaru di atas.
     */
    public static function opsi(): array
    {
        static $cache = [];
        $kunciCache = (string) \App\Support\Tenant::id();
        if (isset($cache[$kunciCache])) {
            return $cache[$kunciCache];
        }

        // Dihitung dari ±14 query, jadi hasilnya disimpan 2 menit per sekolah (halaman tidak lagi lambat).
        // Dihapus otomatis saat admin menambah/menghapus semester (lihat lupakanCache()).
        return $cache[$kunciCache] = \Illuminate\Support\Facades\Cache::remember(
            self::kunciCache(), 120, fn () => self::hitungOpsi()
        );
    }

    public static function kunciCache(): string
    {
        return 'semester_opsi_' . (\App\Support\Tenant::id() ?: 'semua');
    }

    public static function lupakanCache(): void
    {
        \Illuminate\Support\Facades\Cache::forget(self::kunciCache());
    }

    private static function hitungOpsi(): array
    {
        $daftar = [];
        $tambah = function (?string $sem, ?string $ta) use (&$daftar) {
            if (in_array($sem, ['Ganjil', 'Genap'], true) && $ta && preg_match('/^\d{4}\/\d{4}$/', $ta)) {
                $daftar[$sem . '|' . $ta] = ['semester' => $sem, 'tahun_ajaran' => $ta];
            }
        };
        $tambah(self::aktif()['semester'], self::aktif()['tahun_ajaran']);

        try {
            foreach ([\App\Models\Nilai::class, \App\Models\Tugas::class, \App\Models\Materi::class, \App\Models\JadwalPelajaran::class, \App\Models\SemesterSekolah::class] as $model) {
                foreach ($model::query()->select('semester', 'tahun_ajaran')->distinct()->get() as $r) {
                    $tambah($r->semester, $r->tahun_ajaran);
                }
            }
            // Data yang hanya punya tanggal: ambil semester dari bulan-tahunnya.
            $dariTanggal = function ($kolom, $model) use ($tambah) {
                $d = $model::query()->selectRaw("MIN($kolom) as awal, MAX($kolom) as akhir")->first();
                if ($d && $d->awal) {
                    for ($t = \Carbon\Carbon::parse($d->awal)->startOfMonth(); $t->lte(\Carbon\Carbon::parse($d->akhir)); $t->addMonths(6)) {
                        $x = self::dari($t);
                        $tambah($x['semester'], $x['tahun_ajaran']);
                    }
                    $x = self::dari($d->akhir);
                    $tambah($x['semester'], $x['tahun_ajaran']);
                }
            };
            $dariTanggal('tanggal', \App\Models\Absensi::class);
            $dariTanggal('tanggal', \App\Models\Prestasi::class);
            $dariTanggal('created_at', \App\Models\Aspirasi::class);
            foreach (\App\Models\Spp::query()->select('bulan', 'tahun')->distinct()->get() as $r) {
                $idx = array_search($r->bulan, array_merge(self::BULAN_GANJIL, self::BULAN_GENAP), true);
                if ($idx !== false && is_numeric($r->tahun)) {
                    $th = (int) $r->tahun;
                    $tambah($idx < 6 ? 'Ganjil' : 'Genap', $idx < 6 ? $th . '/' . ($th + 1) : ($th - 1) . '/' . $th);
                }
            }
        } catch (\Throwable $e) {
            // tabel belum ada (sebelum migrasi) — cukup semester berjalan
        }

        $daftar = array_values($daftar);
        usort($daftar, fn ($x, $y) => self::urut($y) <=> self::urut($x));
        return $daftar;
    }

    /** [mulai, selesai] tanggal semester tsb. */
    public static function rentang(array $s): array
    {
        [$a, $b] = array_map('intval', explode('/', $s['tahun_ajaran']));
        return $s['semester'] === 'Ganjil'
            ? [Carbon::create($a, 7, 1)->startOfDay(), Carbon::create($a, 12, 31)->endOfDay()]
            : [Carbon::create($b, 1, 1)->startOfDay(), Carbon::create($b, 6, 30)->endOfDay()];
    }

    /** Batasi query SPP (kolom bulan + tahun) ke semester tertentu. */
    public static function filterSpp($query, array $s)
    {
        [$a, $b] = array_map('intval', explode('/', $s['tahun_ajaran']));
        return $s['semester'] === 'Ganjil'
            ? $query->where('tahun', (string) $a)->whereIn('bulan', self::BULAN_GANJIL)
            : $query->where('tahun', (string) $b)->whereIn('bulan', self::BULAN_GENAP);
    }

    /** Batasi query ke kolom semester + tahun_ajaran (baris lama tanpa semester tetap ikut tampil). */
    public static function filterKolom($query, array $s, bool $sertakanKosong = false)
    {
        return $query->where(function ($q) use ($s, $sertakanKosong) {
            $q->where(function ($x) use ($s) {
                $x->where('semester', $s['semester'])->where('tahun_ajaran', $s['tahun_ajaran']);
            });
            if ($sertakanKosong) {
                $q->orWhereNull('semester');
            }
        });
    }

    public static function filterTanggal($query, array $s, string $kolom)
    {
        [$mulai, $selesai] = self::rentang($s);
        return $query->whereBetween($kolom, [$mulai, $selesai]);
    }
}
