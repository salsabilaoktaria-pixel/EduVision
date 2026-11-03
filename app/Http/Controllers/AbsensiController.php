<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Kelas;
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
        return view('absensi.monitor', compact('absensiPerKelas', 'kelasList', 'tanggal'));
    }

    // Halaman absen mandiri untuk siswa (ambil foto via webcam)
    public function formAbsen()
    {
        $siswa = Auth::user()->siswa;
        $sudahAbsen = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', now())
            ->exists();
        $sekolah = \App\Models\PengaturanSekolah::ambil()->toArray();
        return view('absensi.form', compact('siswa', 'sudahAbsen', 'sekolah'));
    }

    public function simpanAbsen(Request $request)
    {
        $request->validate([
            'foto_base64' => 'required|string',
            'keterangan' => 'required|in:Hadir,Izin,Sakit,Lainnya',
            'keterangan_detail' => 'required_if:keterangan,Lainnya|nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ], [
            'keterangan.required' => 'Keterangan wajib diisi sebelum mengirim absensi.',
            'keterangan_detail.required_if' => 'Karena memilih "Lainnya", tuliskan dulu keterangannya sebelum mengirim absensi.',
        ]);

        $siswa = Auth::user()->siswa;

        if (Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', now())->exists()) {
            return back()->with('gagal', 'Anda sudah absen hari ini.');
        }

        // Simpan foto hasil capture kamera (base64 dari JavaScript) sebagai file
        $fotoData = $request->foto_base64;
        $fotoData = str_replace('data:image/png;base64,', '', $fotoData);
        $fotoData = str_replace(' ', '+', $fotoData);
        $namaFile = 'absensi/' . Str::random(20) . '.png';
        Storage::disk('public')->put($namaFile, base64_decode($fotoData));

        // ==== VALIDASI GPS: cek apakah siswa absen dari area sekolah ====
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
