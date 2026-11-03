<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Kelas;
<<<<<<< HEAD
use App\Models\PengaturanSekolah;
=======
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());
        $query = Absensi::with('siswa.kelas')->whereDate('tanggal', $tanggal);
        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', fn ($q) => $q->where('kelas_id', $request->kelas_id));
        }

        // Dipisah PER KELAS (bukan dicampur semua siswa jadi satu daftar),
        // supaya wali kelas/guru gampang menemukan siswanya sendiri.
        $absensiPerKelas = $query->get()
            ->groupBy(fn ($a) => $a->siswa->kelas->nama_kelas ?? 'Tanpa Kelas')
            ->map(fn ($grup) => $grup->sortBy(fn ($a) => $a->siswa->nama ?? ''));

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('absensi.index', compact('absensiPerKelas', 'kelasList', 'tanggal'));
    }

    // Halaman monitor real-time untuk admin/guru: lihat foto wajah + lokasi GPS tiap siswa yang absen
    public function monitor(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());
        $query = Absensi::with('siswa.kelas')->whereDate('tanggal', $tanggal);
        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', fn ($q) => $q->where('kelas_id', $request->kelas_id));
        }

        // Sama seperti rekap: dipisah PER KELAS supaya tidak campur aduk.
        $absensiPerKelas = $query->get()
            ->groupBy(fn ($a) => $a->siswa->kelas->nama_kelas ?? 'Tanpa Kelas')
            ->map(fn ($grup) => $grup->sortByDesc('jam_masuk'));

        $kelasList = Kelas::orderBy('nama_kelas')->get();
<<<<<<< HEAD
        $sekolah = PengaturanSekolah::ambil();
        return view('absensi.monitor', compact('absensiPerKelas', 'kelasList', 'tanggal', 'sekolah'));
=======
        return view('absensi.monitor', compact('absensiPerKelas', 'kelasList', 'tanggal'));
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
    }

    // Halaman absen mandiri untuk siswa (ambil foto via webcam)
    public function formAbsen()
    {
        $siswa = Auth::user()->siswa;
<<<<<<< HEAD
        // Absen yang DITOLAK tidak dihitung "sudah absen" — siswa boleh mengirim ulang.
        $absensiHariIni = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', now())
            ->where('status_persetujuan', '!=', 'ditolak')
            ->latest('id')->first();
        $absensiDitolak = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', now())
            ->where('status_persetujuan', 'ditolak')
            ->latest('id')->first();
        $sudahAbsen = (bool) $absensiHariIni;
        $sekolah = PengaturanSekolah::ambil()->toArray();
        return view('absensi.form', compact('siswa', 'sudahAbsen', 'absensiHariIni', 'absensiDitolak', 'sekolah'));
=======
        $sudahAbsen = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', now())
            ->exists();
        $sekolah = \App\Models\PengaturanSekolah::ambil()->toArray();
        return view('absensi.form', compact('siswa', 'sudahAbsen', 'sekolah'));
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
    }

    public function simpanAbsen(Request $request)
    {
        $request->validate([
            'foto_base64' => 'required|string',
            'keterangan' => 'required|in:Hadir,Izin,Sakit,Lainnya',
            'keterangan_detail' => 'required_if:keterangan,Lainnya|nullable|string|max:255',
<<<<<<< HEAD
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'akurasi' => 'nullable|numeric|min:0',
=======
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
        ], [
            'keterangan.required' => 'Keterangan wajib diisi sebelum mengirim absensi.',
            'keterangan_detail.required_if' => 'Karena memilih "Lainnya", tuliskan dulu keterangannya sebelum mengirim absensi.',
        ]);

        $siswa = Auth::user()->siswa;

<<<<<<< HEAD
        $sudahAda = Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', now())
            ->where('status_persetujuan', '!=', 'ditolak')->exists();
        if ($sudahAda) {
            return back()->with('gagal', 'Anda sudah absen hari ini.');
        }
        // Absen yang ditolak admin/guru diganti dengan kiriman baru ini.
        Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', now())
            ->where('status_persetujuan', 'ditolak')->delete();
=======
        if (Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', now())->exists()) {
            return back()->with('gagal', 'Anda sudah absen hari ini.');
        }
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8

        // Simpan foto hasil capture kamera (base64 dari JavaScript) sebagai file
        $fotoData = $request->foto_base64;
        $fotoData = str_replace('data:image/png;base64,', '', $fotoData);
        $fotoData = str_replace(' ', '+', $fotoData);
        $namaFile = 'absensi/' . Str::random(20) . '.png';
        Storage::disk('public')->put($namaFile, base64_decode($fotoData));

        // ==== VALIDASI GPS: cek apakah siswa absen dari area sekolah ====
<<<<<<< HEAD
        // Memakai rumus jarak efektif yang SAMA dengan JavaScript di form & halaman
        // monitor (lihat Absensi::jarakEfektif), jadi angkanya selalu sinkron.
        $sekolah = PengaturanSekolah::ambil();
        $jarak = null;
        $akurasi = $request->filled('akurasi') ? (float) $request->akurasi : null;
        $diAreaSekolah = false;

        if ($request->filled('latitude') && $request->filled('longitude')) {
            $mentah = Absensi::hitungJarakMeter(
                $sekolah->latitude, $sekolah->longitude,
                $request->latitude, $request->longitude
            );
            $jarak = Absensi::jarakEfektif($mentah, $akurasi);
            $diAreaSekolah = $jarak <= $sekolah->radius_meter;
        }

        // Status yang disimpan ke kolom `status` TETAP salah satu dari Hadir/Izin/Sakit
        // (supaya semua fitur lain yang bergantung pada enum ini tidak perlu diubah).
        // "Lainnya" disimpan sebagai "Izin" dengan alasan aslinya di keterangan_detail.
        $status = $request->keterangan === 'Lainnya' ? 'Izin' : $request->keterangan;

        // ATURAN PERSETUJUAN:
        // - Hadir + di dalam radius sekolah  -> langsung disetujui otomatis.
        // - Selain itu (Izin/Sakit/Lainnya, ATAU di luar radius, ATAU lokasi tidak
        //   terdeteksi) -> absen TETAP tersimpan, tapi berstatus "menunggu" sampai
        //   admin/guru menyetujui atau menolaknya.
        $otomatisDisetujui = ($status === 'Hadir' && $diAreaSekolah);
        $alasan = null;
        if (!$otomatisDisetujui) {
            if ($status !== 'Hadir') {
                $alasan = 'Siswa mengajukan keterangan "' . $request->keterangan . '" (tidak hadir di sekolah).';
            } elseif (is_null($jarak)) {
                $alasan = 'Lokasi siswa tidak terdeteksi saat absen.';
            } else {
                $alasan = 'Siswa berada di luar radius sekolah (' . number_format($jarak, 0, ',', '.') . ' m dari titik sekolah, batas ' . $sekolah->radius_meter . ' m).';
            }
        }

=======
        $sekolah = \App\Models\PengaturanSekolah::ambil()->toArray();
        $jarak = null;
        $diAreaSekolah = false;

        if ($request->filled('latitude') && $request->filled('longitude')) {
            $jarak = Absensi::hitungJarakMeter(
                $sekolah['latitude'], $sekolah['longitude'],
                $request->latitude, $request->longitude
            );
            $diAreaSekolah = $jarak <= $sekolah['radius_meter'];
        }

        // Status yang disimpan ke kolom `status` TETAP salah satu dari Hadir/Izin/Sakit
        // (supaya semua fitur lain yang sudah bergantung pada enum ini tidak perlu
        // diubah). Kalau siswa memilih "Lainnya", disimpan sebagai "Izin" dan alasan
        // aslinya yang ditulis manual disimpan lengkap di keterangan_detail.
        $status = $request->keterangan === 'Lainnya' ? 'Izin' : $request->keterangan;
        $terverifikasiOtomatis = ($status === 'Hadir' && $diAreaSekolah);

        // CATATAN JUJUR: penyimpanan foto + GPS di sini BUKAN sistem liveness-detection
        // anti-pemalsuan yang sudah teruji. Kombinasi "status Hadir + di dalam radius
        // sekolah" dipakai untuk auto-verifikasi, selain itu tetap perlu ditinjau manual.
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
        Absensi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => now()->toDateString(),
            'jam_masuk' => now()->toTimeString(),
            'status' => $status,
            'keterangan_detail' => $request->keterangan === 'Lainnya' ? $request->keterangan_detail : null,
            'foto_wajah' => $namaFile,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'jarak_ke_sekolah_meter' => $jarak,
<<<<<<< HEAD
            'akurasi_meter' => $akurasi,
            'di_area_sekolah' => $diAreaSekolah,
            'terverifikasi' => $otomatisDisetujui,
            'status_persetujuan' => $otomatisDisetujui ? 'disetujui' : 'menunggu',
            'alasan_menunggu' => $alasan,
        ]);

        if ($otomatisDisetujui) {
            return redirect()->route('absensi.form')->with('sukses', 'Absen berhasil! Lokasi terkonfirmasi di area sekolah ✅');
        }

        return redirect()->route('absensi.form')->with('gagal', 'Absen kamu sudah terkirim, tapi harus menunggu persetujuan admin/guru sebelum dihitung. ' . $alasan);
    }

    // Halaman antrean persetujuan absen (admin & guru/wali kelas).
    public function persetujuan(Request $request)
    {
        $menunggu = Absensi::with('siswa.kelas')
            ->where('status_persetujuan', 'menunggu')
            ->orderBy('tanggal')->orderBy('jam_masuk')
            ->get()
            ->groupBy(fn ($a) => $a->siswa->kelas->nama_kelas ?? 'Tanpa Kelas')
            ->sortKeys();

        $riwayat = Absensi::with('siswa.kelas')
            ->whereIn('status_persetujuan', ['disetujui', 'ditolak'])
            ->whereNotNull('diputuskan_pada')
            ->orderByDesc('diputuskan_pada')
            ->limit(15)->get();

        $sekolah = PengaturanSekolah::ambil();
        return view('absensi.persetujuan', compact('menunggu', 'riwayat', 'sekolah'));
    }

    public function setujui(Absensi $absensi)
    {
        $absensi->update([
            'status_persetujuan' => 'disetujui',
            'terverifikasi' => true, // dikunci: status tidak bisa diubah lagi
            'diputuskan_oleh' => Auth::id(),
            'diputuskan_pada' => now(),
        ]);
        return back()->with('sukses', 'Absen ' . ($absensi->siswa->nama ?? 'siswa') . ' disetujui & dikunci.');
    }

    public function tolak(Request $request, Absensi $absensi)
    {
        $request->validate(['catatan_persetujuan' => 'nullable|string|max:255']);
        $absensi->update([
            'status_persetujuan' => 'ditolak',
            'status' => 'Alpa',
            'terverifikasi' => true,
            'catatan_persetujuan' => $request->catatan_persetujuan,
            'diputuskan_oleh' => Auth::id(),
            'diputuskan_pada' => now(),
        ]);
        return back()->with('sukses', 'Absen ' . ($absensi->siswa->nama ?? 'siswa') . ' ditolak. Siswa bisa mengirim ulang absennya.');
=======
            'di_area_sekolah' => $diAreaSekolah,
            'terverifikasi' => $terverifikasiOtomatis,
        ]);

        if ($terverifikasiOtomatis) {
            return redirect()->route('absensi.form')->with('sukses', 'Absen berhasil! Lokasi terkonfirmasi di area sekolah ✅');
        }

        return redirect()->route('absensi.form')->with('gagal', "Absen dengan keterangan \"{$request->keterangan}\" tersimpan, menunggu verifikasi manual dari wali kelas/admin.");
    }

    public function verifikasi(Absensi $absensi)
    {
        $absensi->update(['terverifikasi' => true]);
        return back()->with('sukses', 'Absensi berhasil diverifikasi & dikunci — status tidak bisa diubah lagi.');
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
    }

    public function updateManual(Request $request, Absensi $absensi)
    {
        // PENTING: absensi yang sudah diverifikasi DIKUNCI, tidak boleh diubah lagi.
        // Ini mencegah tren kehadiran di dashboard naik-turun tidak jelas karena
        // status yang sudah final diotak-atik ulang setelah diverifikasi.
        if ($absensi->terverifikasi) {
            return back()->with('gagal', 'Absensi ini sudah diverifikasi dan dikunci, status tidak bisa diubah lagi.');
        }

        $request->validate(['status' => 'required|in:Hadir,Sakit,Izin,Alpa']);
        $absensi->update(['status' => $request->status]);
        return back()->with('sukses', 'Status absensi diperbarui.');
    }
}
