<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\SppController;
use App\Http\Controllers\AspirasiController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\BankSoalController;
use App\Http\Controllers\RaporController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\AbsensiMapelController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\TugasController;

// ================= AUTH =================
Route::get('/', function () {
    // Kalau sudah login, langsung ke dashboard. Kalau belum, tampilkan homepage.
    return auth()->check() ? redirect()->route('dashboard') : view('homepage');
})->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================= HALAMAN BUTUH LOGIN =================
Route::middleware('auth')->group(function () {

    // Halaman slide perkenalan yang tampil sekali setelah login, sebelum dashboard.
    Route::get('/beranda', [DashboardController::class, 'beranda'])->name('beranda');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---- Ganti Password: semua role login ----
    Route::get('/ganti-password', [AkunController::class, 'gantiPassword'])->name('akun.gantiPassword');
    Route::post('/ganti-password', [AkunController::class, 'updatePassword'])->name('akun.updatePassword');

    // ---- Kalender Tugas: semua role login (difilter di dalam controller) ----
    Route::get('/kalender-tugas', [TugasController::class, 'kalender'])->name('tugas.kalender');

    // ---- Master Data: hanya admin ----
    // PENTING soal URUTAN: rute literal seperti /siswa/create dan /mapel/create
    // HARUS didaftarkan SEBELUM rute "show" (/siswa/{siswa}, /mapel/{mapel}) di
    // bawah, karena Laravel mencocokkan rute sesuai urutan daftar — kalau wildcard
    // {siswa} didaftarkan lebih dulu, "create" akan "tertangkap" sebagai ID siswa.
    Route::middleware('role:admin')->group(function () {
        Route::resource('siswa', SiswaController::class)->except(['index', 'show']);
        Route::resource('guru', GuruController::class);

        // PENTING: ->parameters() memaksa nama parameter tetap "kelas",
        // karena Laravel salah menyingularkan "kelas" (dikira jamak) jadi "kela".
        Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);

        // Menambah mata pelajaran baru tetap khusus admin. Lihat/edit/hapus mapel
        // sudah dibuka untuk guru juga di grup "Data Siswa & Mata Pelajaran" di bawah.
        Route::resource('mapel', MataPelajaranController::class)->only(['create', 'store']);
        Route::post('/mapel/{mapel}/kelas', [MataPelajaranController::class, 'assignKelas'])->name('mapel.assignKelas');

        Route::resource('spp', SppController::class)->except(['show', 'edit', 'update']);
        Route::post('/spp/{spp}/bayar', [SppController::class, 'bayar'])->name('spp.bayar');
        Route::post('/absensi/{absensi}/verifikasi', [AbsensiController::class, 'verifikasi'])->name('absensi.verifikasi');

        // ---- Pengaturan Sekolah (lokasi Face ID) ----
        Route::get('/pengaturan-sekolah', [PengaturanController::class, 'edit'])->name('pengaturan.edit');
        Route::post('/pengaturan-sekolah', [PengaturanController::class, 'update'])->name('pengaturan.update');
    });

    // ---- Data Siswa: admin & guru bisa LIHAT, hanya admin yang bisa ubah.
    //      Mata Pelajaran: admin & guru bisa lihat+edit+hapus (tambah baru tetap admin saja).
    // (didaftarkan SETELAH grup admin di atas, lihat alasan urutan di komentar atasnya)
    Route::middleware('role:admin,guru,wali_kelas')->group(function () {
        Route::resource('siswa', SiswaController::class)->only(['index', 'show']);
        Route::resource('mapel', MataPelajaranController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    });

    // ---- Fitur yang HANYA untuk guru/wali kelas (TIDAK untuk admin lagi):
    //      Input Nilai, Bank Soal, Pengumpulan Tugas. Semua fungsinya tetap
    //      persis sama seperti sebelumnya, hanya saja admin tidak punya akses. ----
    Route::middleware('role:guru,wali_kelas')->group(function () {
        Route::resource('nilai', NilaiController::class)->except(['show']);
        Route::get('/nilai/{nilai}/tugas', [NilaiController::class, 'kelolaTugas'])->name('nilai.tugas');
        Route::post('/nilai/{nilai}/tugas', [NilaiController::class, 'simpanTugas'])->name('nilai.tugas.simpan');
        Route::delete('/nilai-tugas/{tugasHarian}', [NilaiController::class, 'hapusTugas'])->name('nilai.tugas.hapus');
        Route::post('/nilai/{nilai}/tugas/terapkan', [NilaiController::class, 'terapkanRataTugas'])->name('nilai.tugas.terapkan');

        Route::get('/banksoal', [BankSoalController::class, 'index'])->name('banksoal.index');
        Route::get('/banksoal/create', [BankSoalController::class, 'create'])->name('banksoal.create');
        Route::post('/banksoal', [BankSoalController::class, 'store'])->name('banksoal.store');
        Route::get('/banksoal/import', [BankSoalController::class, 'importForm'])->name('banksoal.importForm');
        Route::get('/banksoal/{banksoal}/edit', [BankSoalController::class, 'edit'])->name('banksoal.edit');
        Route::put('/banksoal/{banksoal}', [BankSoalController::class, 'update'])->name('banksoal.update');
        Route::delete('/banksoal/{banksoal}', [BankSoalController::class, 'destroy'])->name('banksoal.destroy');
        Route::post('/banksoal/import', [BankSoalController::class, 'import'])->name('banksoal.import');

        // ---- Pengumpulan Tugas (mirip Google Classroom): kelola tugas & nilai ----
        Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
        Route::get('/tugas/create', [TugasController::class, 'create'])->name('tugas.create');
        Route::post('/tugas', [TugasController::class, 'store'])->name('tugas.store');
        Route::get('/tugas/{tugas}', [TugasController::class, 'show'])->name('tugas.show');
        Route::get('/tugas/{tugas}/edit', [TugasController::class, 'edit'])->name('tugas.edit');
        Route::put('/tugas/{tugas}', [TugasController::class, 'update'])->name('tugas.update');
        Route::delete('/tugas/{tugas}', [TugasController::class, 'destroy'])->name('tugas.destroy');
        Route::delete('/tugas-lampiran/{lampiran}', [TugasController::class, 'hapusLampiran'])->name('tugas.lampiran.hapus');
        Route::post('/pengumpulan-tugas/{pengumpulan}/nilai', [TugasController::class, 'beriNilai'])->name('tugas.beriNilai');
    });

    // ---- Fitur yang masih dipakai bersama admin & guru/wali kelas ----
    Route::middleware('role:admin,guru,wali_kelas')->group(function () {
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::post('/absensi/{absensi}/status', [AbsensiController::class, 'updateManual'])->name('absensi.status');

        Route::get('/prestasi', [PrestasiController::class, 'index'])->name('prestasi.index');
        Route::get('/prestasi/create', [PrestasiController::class, 'create'])->name('prestasi.create');
        Route::post('/prestasi', [PrestasiController::class, 'store'])->name('prestasi.store');
        Route::get('/prestasi/{prestasi}/edit', [PrestasiController::class, 'edit'])->name('prestasi.edit');
        Route::put('/prestasi/{prestasi}', [PrestasiController::class, 'update'])->name('prestasi.update');
        Route::delete('/prestasi/{prestasi}', [PrestasiController::class, 'destroy'])->name('prestasi.destroy');

        Route::get('/rapor/{siswa}', [RaporController::class, 'show'])->name('rapor.show');
        Route::get('/rapor/{siswa}/pdf', [RaporController::class, 'pdf'])->name('rapor.pdf');

        Route::get('/aspirasi', [AspirasiController::class, 'index'])->name('aspirasi.index');
        Route::post('/aspirasi/{aspirasi}/tanggapi', [AspirasiController::class, 'tanggapi'])->name('aspirasi.tanggapi');

        Route::get('/monitor-absensi', [AbsensiController::class, 'monitor'])->name('absensi.monitor');

        // ---- Jadwal Pelajaran (dipakai juga untuk absen QR per mapel) ----
        Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
        Route::get('/jadwal/create', [JadwalController::class, 'create'])->name('jadwal.create');
        Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
        Route::get('/jadwal/{jadwal}/edit', [JadwalController::class, 'edit'])->name('jadwal.edit');
        Route::put('/jadwal/{jadwal}', [JadwalController::class, 'update'])->name('jadwal.update');
        Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

        // ---- Deteksi siswa terindikasi membolos (Guru BK/Piket) ----
        Route::get('/deteksi-bolos', [AbsensiMapelController::class, 'deteksiBolos'])->name('absensimapel.bolos');
    });

    // ---- Absen QR per-mapel: sisi GURU (buka sesi, tampilkan layar, tutup) ----
    // Admin tidak lagi punya akses ke absen per mapel ini, cukup guru/wali kelas & siswa.
    Route::middleware('role:guru,wali_kelas')->group(function () {
        Route::get('/absen-mapel', [AbsensiMapelController::class, 'guruIndex'])->name('absensimapel.index');
        Route::post('/absen-mapel/buka/{jadwal}', [AbsensiMapelController::class, 'buka'])->name('absensimapel.buka');
        Route::get('/absen-mapel/{sesi}/layar', [AbsensiMapelController::class, 'layar'])->name('absensimapel.layar');
        Route::get('/absen-mapel/{sesi}/token', [AbsensiMapelController::class, 'token'])->name('absensimapel.token');
        Route::get('/absen-mapel/{sesi}/status', [AbsensiMapelController::class, 'status'])->name('absensimapel.status');
        Route::post('/absen-mapel/{sesi}/tutup', [AbsensiMapelController::class, 'tutup'])->name('absensimapel.tutup');
        Route::post('/absen-mapel/{sesi}/manual/{siswa}', [AbsensiMapelController::class, 'manual'])->name('absensimapel.manual');
    });

    // ---- Materi Pembelajaran: sekarang HANYA guru/wali kelas (admin tidak lagi
    //      punya akses sama sekali ke fitur ini, baik lihat maupun kelola). ----
    Route::middleware('role:guru,wali_kelas')->group(function () {
        Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
        Route::get('/materi/create', [MateriController::class, 'create'])->name('materi.create');
        Route::post('/materi', [MateriController::class, 'store'])->name('materi.store');
        Route::delete('/materi/{materi}', [MateriController::class, 'destroy'])->name('materi.destroy');
    });

    // ---- Absensi mandiri: siswa ----
    Route::middleware('role:siswa')->group(function () {
        Route::get('/absen-saya', [AbsensiController::class, 'formAbsen'])->name('absensi.form');
        Route::post('/absen-saya', [AbsensiController::class, 'simpanAbsen'])->name('absensi.simpan');
        Route::get('/spp-saya', [SppController::class, 'statusSaya'])->name('spp.saya');
        Route::get('/rapor-saya', [RaporController::class, 'raporSaya'])->name('rapor.saya');
        Route::get('/rapor-saya/pdf', [RaporController::class, 'pdfSaya'])->name('rapor.pdfSaya');

        // ---- Absen QR per-mapel: sisi SISWA ----
        // Link hasil scan QR mengarah ke sini (dibuka otomatis oleh kamera HP siswa).
        Route::get('/absen-mapel/konfirmasi/{sesi}/{token}', [AbsensiMapelController::class, 'konfirmasi'])->name('absensimapel.konfirmasi');
        // Fallback input kode manual (juga dipakai untuk testing tanpa hosting).
        Route::get('/absen-mapel/manual', [AbsensiMapelController::class, 'formManual'])->name('absensimapel.formManual');
        Route::post('/absen-mapel/manual', [AbsensiMapelController::class, 'prosesManual'])->name('absensimapel.prosesManual');

        // ---- Materi Pembelajaran: sisi SISWA (lihat saja) ----
        Route::get('/materi-saya', [MateriController::class, 'materiSaya'])->name('materi.saya');

        // ---- Jadwal Pelajaran: sisi SISWA (lihat jadwal kelasnya sendiri) ----
        Route::get('/jadwal-saya', [JadwalController::class, 'jadwalSaya'])->name('jadwal.saya');

        // ---- Pengumpulan Tugas: sisi SISWA ----
        Route::get('/tugas-saya', [TugasController::class, 'tugasSaya'])->name('tugas.saya');
        Route::get('/tugas-saya/{tugas}/kumpulkan', [TugasController::class, 'formKumpulkan'])->name('tugas.formKumpulkan');
        Route::post('/tugas-saya/{tugas}/kumpulkan', [TugasController::class, 'prosesKumpulkan'])->name('tugas.prosesKumpulkan');
    });

    // ---- Aspirasi & bank soal latihan: semua role login ----
    Route::get('/aspirasi/kirim', [AspirasiController::class, 'create'])->name('aspirasi.create');
    Route::post('/aspirasi/kirim', [AspirasiController::class, 'store'])->name('aspirasi.store');
    Route::get('/aspirasi-saya', [AspirasiController::class, 'saya'])->name('aspirasi.saya');

    Route::get('/latihan-soal', [BankSoalController::class, 'latihan'])->name('banksoal.latihan');
    Route::post('/latihan-soal/koreksi', [BankSoalController::class, 'koreksi'])->name('banksoal.koreksi');
    Route::get('/latihan-soal/hasil', [BankSoalController::class, 'hasil'])->name('banksoal.hasil');
});
