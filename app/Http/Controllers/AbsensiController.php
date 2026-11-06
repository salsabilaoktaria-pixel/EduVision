<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AbsensiController extends Controller
{
    /**
     * Tanggal bawaan mengikuti semester yang dipilih di navbar: hari ini bila masih di dalam semester itu,
     * selain itu hari sekolah terakhir yang punya data absensi pada semester tsb.
     */
    private function tanggalDefault(Request $request): string
    {
        if ($request->filled('tanggal')) {
            return $request->input('tanggal');
        }
        [$mulai, $selesai] = \App\Support\Semester::rentang(\App\Support\Semester::terpilih());
        if (now()->between($mulai, $selesai)) {
            return now()->toDateString();
        }
        $terakhir = Absensi::whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])->max('tanggal');
        return $terakhir ? \Carbon\Carbon::parse($terakhir)->toDateString() : $mulai->toDateString();
    }

    public function index(Request $request)
    {
        $tanggal = $this->tanggalDefault($request);
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
        $tanggal = $this->tanggalDefault($request);
        $query = Absensi::with('siswa.kelas')->whereDate('tanggal', $tanggal);
        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', fn ($q) => $q->where('kelas_id', $request->kelas_id));
        }

        // Sama seperti rekap: dipisah PER KELAS supaya tidak campur aduk.
        $absensiPerKelas = $query->get()
            ->groupBy(fn ($a) => $a->siswa->kelas->nama_kelas ?? 'Tanpa Kelas')
            ->map(fn ($grup) => $grup->sortByDesc('jam_masuk'));

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $sekolah = Sekolah::saatIni();
        return view('absensi.monitor', compact('absensiPerKelas', 'kelasList', 'tanggal', 'sekolah'));
    }

    // Halaman absen mandiri untuk siswa (ambil foto via webcam)
    public function formAbsen()
    {
        $siswa = Auth::user()->siswa;
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
        $sekolah = Sekolah::saatIni()->toArray();
        return view('absensi.form', compact('siswa', 'sudahAbsen', 'absensiHariIni', 'absensiDitolak', 'sekolah'));
    }

    public function simpanAbsen(Request $request)
    {
        $request->validate([
            'foto_base64' => 'required|string',
            'keterangan' => 'required|in:Hadir,Izin,Sakit,Lainnya',
            'keterangan_detail' => 'required_if:keterangan,Lainnya|nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'akurasi' => 'nullable|numeric|min:0',
        ], [
            'keterangan.required' => 'Keterangan wajib diisi sebelum mengirim absensi.',
            'keterangan_detail.required_if' => 'Karena memilih "Lainnya", tuliskan dulu keterangannya sebelum mengirim absensi.',
        ]);

        $siswa = Auth::user()->siswa;

        $sudahAda = Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', now())
            ->where('status_persetujuan', '!=', 'ditolak')->exists();
        if ($sudahAda) {
            return back()->with('gagal', 'Anda sudah absen hari ini.');
        }
        // Absen yang ditolak admin/guru diganti dengan kiriman baru ini.
        Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', now())
            ->where('status_persetujuan', 'ditolak')->delete();

        // Simpan foto hasil capture kamera (base64 dari JavaScript) sebagai file
        $fotoData = $request->foto_base64;
        $fotoData = str_replace('data:image/png;base64,', '', $fotoData);
        $fotoData = str_replace(' ', '+', $fotoData);
        $namaFile = 'absensi/' . Str::random(20) . '.png';
        Storage::disk('public')->put($namaFile, base64_decode($fotoData));

        // ==== VALIDASI GPS: cek apakah siswa absen dari area sekolah ====
        // Memakai rumus jarak efektif yang SAMA dengan JavaScript di form & halaman
        // monitor (lihat Absensi::jarakEfektif), jadi angkanya selalu sinkron.
        $sekolah = Sekolah::saatIni();
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

        $absenBaru = Absensi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => now()->toDateString(),
            'jam_masuk' => now()->toTimeString(),
            'status' => $status,
            'keterangan_detail' => $request->keterangan === 'Lainnya' ? $request->keterangan_detail : null,
            'foto_wajah' => $namaFile,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'jarak_ke_sekolah_meter' => $jarak,
            'akurasi_meter' => $akurasi,
            'di_area_sekolah' => $diAreaSekolah,
            'terverifikasi' => $otomatisDisetujui,
            'status_persetujuan' => $otomatisDisetujui ? 'disetujui' : 'menunggu',
            'alasan_menunggu' => $alasan,
        ]);

        if (!$otomatisDisetujui) {
            \App\Models\Notifikasi::kirimKePeran(['admin', 'guru', 'wali_kelas'], 'absensi',
                'Absen menunggu persetujuan',
                ($siswa->nama ?? 'Siswa') . ' — ' . $alasan, route('absensi.persetujuan'));
        }

        if ($otomatisDisetujui) {
            return redirect()->route('absensi.form')->with('sukses', 'Absen berhasil! Lokasi terkonfirmasi di area sekolah.');
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

        $sekolah = Sekolah::saatIni();
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
        \App\Models\Notifikasi::kirim($absensi->siswa->user_id ?? null, 'absensi', 'Absenmu disetujui',
            'Absen tanggal ' . \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d F Y') . ' sudah disetujui.', route('absensi.form'));
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
        \App\Models\Notifikasi::kirim($absensi->siswa->user_id ?? null, 'absensi', 'Absenmu ditolak',
            $request->catatan_persetujuan ?: 'Silakan kirim ulang absenmu.', route('absensi.form'));
        return back()->with('sukses', 'Absen ' . ($absensi->siswa->nama ?? 'siswa') . ' ditolak. Siswa bisa mengirim ulang absennya.');
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
