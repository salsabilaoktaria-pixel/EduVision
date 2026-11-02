<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Absensi;
use App\Models\Spp;
use App\Models\Aspirasi;
use App\Models\Nilai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Halaman "slide" perkenalan yang ditampilkan SEKALI setiap kali user
     * berhasil login, SEBELUM masuk ke dashboard (lihat AuthController::login()).
     * Cuma halaman statis berisi slide panjang, tidak perlu ambil data apa-apa.
     */
    public function beranda()
    {
        $user = Auth::user();
        if ($user->isSuperAdmin()) {
            return redirect()->route('sekolah.index');
        }
        return view('beranda', compact('user'));
    }

    public function index()
    {
        $user = Auth::user();
        if ($user->isSuperAdmin()) {
            return redirect()->route('sekolah.index');
        }
        $data = [];
        $sem = \App\Support\Semester::terpilih();
        [$semMulai, $semSelesai] = \App\Support\Semester::rentang($sem);
        $akhirTampil = $semSelesai->lt(now()) ? $semSelesai->copy() : now();

        if ($user->isAdmin() || $user->isGuru()) {
            $data += [
                'totalSiswa' => Siswa::count(),
                'totalGuru' => Guru::count(),
                'totalKelas' => Kelas::count(),
                'totalMapel' => MataPelajaran::count(),
                'absenHariIni' => Absensi::whereDate('tanggal', now())->where('status_persetujuan', 'disetujui')->count(),
                'absenMenunggu' => Absensi::where('status_persetujuan', 'menunggu')->count(),
                'sppBelumLunas' => \App\Support\Semester::filterSpp(Spp::where('status', 'Belum Lunas'), $sem)->count(),
                'aspirasiBaru' => Aspirasi::where('status', 'Baru')->count(),
            ];

            // Grafik 1: Tren kehadiran 7 hari terakhir pada semester terpilih
            $labelHari = [];
            $dataHadir = [];
            for ($i = 6; $i >= 0; $i--) {
                $tgl = $akhirTampil->copy()->subDays($i);
                $labelHari[] = $tgl->translatedFormat('D, d M');
                $dataHadir[] = Absensi::whereDate('tanggal', $tgl)->where('status', 'Hadir')->where('status_persetujuan', 'disetujui')->count();
            }
            $data['grafikKehadiranLabel'] = $labelHari;
            $data['grafikKehadiranData'] = $dataHadir;

            // Grafik 2: Jumlah siswa per kelas
            $siswaPerKelas = Kelas::withCount('siswa')->get();
            $data['grafikKelasLabel'] = $siswaPerKelas->pluck('nama_kelas');
            $data['grafikKelasData'] = $siswaPerKelas->pluck('siswa_count');

            // Grafik 3: SPP lunas vs belum lunas
            $data['grafikSppLunas'] = \App\Support\Semester::filterSpp(Spp::where('status', 'Lunas'), $sem)->count();
            $data['grafikSppBelum'] = \App\Support\Semester::filterSpp(Spp::where('status', 'Belum Lunas'), $sem)->count();

            // Grafik 4: Rata-rata nilai per mapel
            $rataPerMapel = Nilai::where('semester', $sem['semester'])->where('tahun_ajaran', $sem['tahun_ajaran'])->select('mata_pelajaran_id', DB::raw('AVG(nilai_akhir) as rata'))
                ->groupBy('mata_pelajaran_id')->with('mataPelajaran')->get();
            $data['grafikMapelLabel'] = $rataPerMapel->pluck('mataPelajaran.nama_mapel');
            $data['grafikMapelData'] = $rataPerMapel->pluck('rata')->map(fn($v) => round($v, 1));
        }

        // Data khusus jika yang login adalah siswa
        if ($user->isSiswa() && $user->siswa) {
            $siswa = $user->siswa;
            $data['profilSiswa'] = $siswa;
            $data['rataRata'] = $siswa->rataRataNilai();
            $data['kehadiran'] = $siswa->persentaseKehadiran();

            // Grafik nilai per mapel milik siswa ini
            $nilaiSiswa = $siswa->nilai()->where('semester', $sem['semester'])->where('tahun_ajaran', $sem['tahun_ajaran'])->with('mataPelajaran')->get();
            $data['grafikNilaiLabel'] = $nilaiSiswa->pluck('mataPelajaran.nama_mapel');
            $data['grafikNilaiData'] = $nilaiSiswa->pluck('nilai_akhir');

            // Grafik rekap kehadiran (Hadir/Sakit/Izin/Alpa)
            $rekapAbsen = \App\Support\Semester::filterTanggal($siswa->absensi()->where('status_persetujuan', '!=', 'menunggu'), $sem, 'tanggal')->select('status', DB::raw('count(*) as jumlah'))->groupBy('status')->pluck('jumlah', 'status');
            $data['grafikAbsenLabel'] = $rekapAbsen->keys();
            $data['grafikAbsenData'] = $rekapAbsen->values();

            // Grafik tren nilai rata-rata per semester (urut kronologis: tahun ajaran lalu
            // Ganjil -> Genap, bukan urutan alfabet "Ganjil" sebelum "Genap" yang salah urut tahun).
            $trenSemester = $siswa->nilai()
                ->select('semester', 'tahun_ajaran', DB::raw('AVG(nilai_akhir) as rata'))
                ->groupBy('semester', 'tahun_ajaran')
                ->orderBy('tahun_ajaran')
                ->orderByRaw("CASE semester WHEN 'Ganjil' THEN 1 WHEN 'Genap' THEN 2 ELSE 3 END")
                ->get();
            $data['grafikSemesterLabel'] = $trenSemester->map(fn($r) => $r->semester . ' ' . $r->tahun_ajaran);
            $data['grafikSemesterData'] = $trenSemester->pluck('rata')->map(fn($v) => round($v, 1));
        }

        return view('dashboard', $data);
    }
}
