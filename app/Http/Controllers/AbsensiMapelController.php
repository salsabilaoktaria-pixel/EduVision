<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use App\Models\SesiAbsensiMapel;
use App\Models\AbsensiMapel;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AbsensiMapelController extends Controller
{
    // Nama hari Indonesia untuk hari ini, dipakai mencocokkan ke kolom `hari`
    // di tabel jadwal_pelajaran (Senin..Sabtu).
    private function namaHariIni(): string
    {
        $peta = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        return $peta[now()->dayOfWeek];
    }

    // ==================== SISI GURU ====================

    // Halaman utama guru: daftar jadwal mengajar HARI INI, dengan tombol
    // "Buka Absen QR" / "Lanjutkan Sesi" di tiap barisnya.
    public function guruIndex()
    {
        $user = Auth::user();
        $hariIni = $this->namaHariIni();

        $query = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'guru'])->where('hari', $hariIni);
        if ($user->isGuru() && !$user->isAdmin() && $user->guru) {
            $query->where('guru_id', $user->guru->id);
        }
        $jadwalHariIni = $query->orderBy('kelas_id')->get();

        // Sesi yang sudah dibuka HARI INI untuk tiap jadwal (kalau ada), supaya
        // tombolnya berubah jadi "Lanjutkan" bukan "Buka" lagi.
        $sesiHariIni = SesiAbsensiMapel::whereDate('tanggal', now()->toDateString())
            ->whereIn('jadwal_pelajaran_id', $jadwalHariIni->pluck('id'))
            ->get()
            ->keyBy('jadwal_pelajaran_id');

        return view('absensimapel.guru_index', compact('jadwalHariIni', 'sesiHariIni', 'hariIni'));
    }

    // Buka (atau lanjutkan) sesi absen QR untuk 1 jadwal pelajaran hari ini.
    public function buka(JadwalPelajaran $jadwal)
    {
        $existing = SesiAbsensiMapel::where('jadwal_pelajaran_id', $jadwal->id)
            ->whereDate('tanggal', now()->toDateString())
            ->where('status', 'dibuka')
            ->first();

        if ($existing) {
            return redirect()->route('absensimapel.layar', $existing);
        }

        $sesi = SesiAbsensiMapel::create([
            'jadwal_pelajaran_id' => $jadwal->id,
            'kelas_id' => $jadwal->kelas_id,
            'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
            'guru_id' => $jadwal->guru_id,
            'tanggal' => now()->toDateString(),
            'jam_ke' => $jadwal->jam_ke,
            'qr_token' => Str::upper(Str::random(8)),
            'qr_diperbarui_pada' => now(),
            'dibuka_pada' => now(),
            'status' => 'dibuka',
        ]);

        return redirect()->route('absensimapel.layar', $sesi);
    }

    // Layar besar untuk ditampilkan ke proyektor/layar laptop guru di depan kelas.
    public function layar(SesiAbsensiMapel $sesi)
    {
        $this->terapkanTimeout($sesi);
        $siswaSekelas = Siswa::where('kelas_id', $sesi->kelas_id)->orderBy('nama')->get();
        return view('absensimapel.layar', compact('sesi', 'siswaSekelas'));
    }

    // AJAX: dipanggil berkala oleh halaman layar untuk memutar QR-nya.
    // Token HANYA diganti kalau umur token sekarang sudah habis, supaya semua
    // klien (kalau ada lebih dari satu tab) tetap lihat QR yang sama selama
    // durasi hidupnya.
    public function token(SesiAbsensiMapel $sesi)
    {
        if ($sesi->status !== 'dibuka') {
            return response()->json(['ditutup' => true]);
        }

        $this->terapkanTimeout($sesi);

        if ($sesi->sisaDetikQr() <= 0) {
            $sesi->terbitkanTokenBaru();
        }

        return response()->json([
            'ditutup' => false,
            'token' => $sesi->qr_token,
            'sisa_detik' => $sesi->sisaDetikQr(),
            'umur_detik' => SesiAbsensiMapel::DETIK_UMUR_QR,
            'url_konfirmasi' => route('absensimapel.konfirmasi', ['sesi' => $sesi->id, 'token' => $sesi->qr_token]),
        ]);
    }

    // AJAX: dipanggil berkala oleh halaman layar untuk memperbarui daftar nama
    // yang berubah hijau begitu siswa selesai scan (real-time via polling).
    public function status(SesiAbsensiMapel $sesi)
    {
        $this->terapkanTimeout($sesi);

        $rekap = AbsensiMapel::where('sesi_absensi_mapel_id', $sesi->id)->get()->keyBy('siswa_id');
        $siswaSekelas = Siswa::where('kelas_id', $sesi->kelas_id)->orderBy('nama')->get(['id', 'nama']);

        $data = $siswaSekelas->map(function ($s) use ($rekap) {
            $r = $rekap->get($s->id);
            return [
                'id' => $s->id,
                'nama' => $s->nama,
                'status' => $r->status ?? 'Belum Absen',
            ];
        });

        $ringkasan = [
            'hadir' => $data->where('status', 'Hadir')->count(),
            'belum' => $data->where('status', 'Belum Absen')->count(),
            'tanpa_keterangan' => $data->where('status', 'Tanpa Keterangan')->count(),
            'izin_sakit' => $data->whereIn('status', ['Izin', 'Sakit'])->count(),
            'total' => $data->count(),
        ];

        return response()->json([
            'status_sesi' => $sesi->status,
            'siswa' => $data->values(),
            'ringkasan' => $ringkasan,
        ]);
    }

    // Guru menekan "Tutup Absen": siswa yang masih belum absen langsung
    // difinalisasi jadi "Tanpa Keterangan" (tidak menunggu timeout 5 menit lagi).
    public function tutup(SesiAbsensiMapel $sesi)
    {
        $siswaIds = Siswa::where('kelas_id', $sesi->kelas_id)->pluck('id');
        $sudahAda = AbsensiMapel::where('sesi_absensi_mapel_id', $sesi->id)->pluck('siswa_id');
        foreach ($siswaIds->diff($sudahAda) as $sid) {
            AbsensiMapel::create([
                'sesi_absensi_mapel_id' => $sesi->id,
                'siswa_id' => $sid,
                'status' => 'Tanpa Keterangan',
            ]);
        }

        $sesi->update(['status' => 'ditutup', 'ditutup_pada' => now()]);

        return redirect()->route('absensimapel.index')->with('sukses', 'Sesi absen berhasil ditutup.');
    }

    // Guru mengubah status manual (mis. siswa yang izin/sakit dari pagi,
    // jadi tidak wajib scan QR lagi).
    public function manual(Request $request, SesiAbsensiMapel $sesi, Siswa $siswa)
    {
        $request->validate(['status' => 'required|in:Hadir,Izin,Sakit,Tanpa Keterangan']);

        AbsensiMapel::updateOrCreate(
            ['sesi_absensi_mapel_id' => $sesi->id, 'siswa_id' => $siswa->id],
            ['status' => $request->status, 'keterangan' => 'Diisi manual oleh guru']
        );

        return back()->with('sukses', "Status {$siswa->nama} diubah jadi {$request->status}.");
    }

    // Timeout otomatis: kalau 5 menit pertama sejak sesi dibuka sudah lewat,
    // siswa yang BELUM py catatan apapun di sesi ini langsung ditandai
    // "Tanpa Keterangan" (indikasi membolos). Dipanggil "lazy" (bukan lewat
    // cron) setiap kali layar/status/token sesi ini diakses, supaya tetap
    // jalan otomatis selama layar guru terbuka tanpa perlu setup job terjadwal.
    private function terapkanTimeout(SesiAbsensiMapel $sesi): void
    {
        if ($sesi->status !== 'dibuka' || !$sesi->sudahLewatTimeout()) {
            return;
        }

        $siswaIds = Siswa::where('kelas_id', $sesi->kelas_id)->pluck('id');
        $sudahAda = AbsensiMapel::where('sesi_absensi_mapel_id', $sesi->id)->pluck('siswa_id');
        foreach ($siswaIds->diff($sudahAda) as $sid) {
            AbsensiMapel::create([
                'sesi_absensi_mapel_id' => $sesi->id,
                'siswa_id' => $sid,
                'status' => 'Tanpa Keterangan',
            ]);
        }
    }

    // ==================== SISI SISWA ====================

    // Diakses lewat LINK hasil scan QR (kamera bawaan HP siswa otomatis
    // membuka link ini begitu QR terdeteksi — tidak perlu scanner khusus
    // di dalam aplikasi). Langsung diproses begitu link dibuka.
    public function konfirmasi(Request $request, SesiAbsensiMapel $sesi, string $token)
    {
        $siswa = Auth::user()->siswa;
        [$berhasil, $pesan] = $this->prosesKehadiran($sesi, $siswa, $token);
        return view('absensimapel.hasil_scan', compact('berhasil', 'pesan', 'sesi'));
    }

    // Halaman fallback: input kode manual 8 karakter yang tampil di layar guru.
    // Ini juga cara PALING PRAKTIS untuk testing di 1 laptop (2 tab browser)
    // sebelum aplikasi di-hosting — tidak perlu kamera maupun HP kedua sama
    // sekali, tinggal salin kode yang tampil di tab guru ke tab siswa.
    public function formManual()
    {
        return view('absensimapel.manual');
    }

    public function prosesManual(Request $request)
    {
        $request->validate(['kode' => 'required|string']);
        $kode = Str::upper(trim($request->kode));

        $sesi = SesiAbsensiMapel::where('qr_token', $kode)->where('status', 'dibuka')->first();
        if (!$sesi) {
            return back()->withInput()->with('gagal', 'Kode salah, sudah kadaluarsa, atau sesi sudah ditutup. Minta guru menampilkan kode/QR terbaru (kodenya berganti tiap beberapa detik).');
        }

        $siswa = Auth::user()->siswa;
        [$berhasil, $pesan] = $this->prosesKehadiran($sesi, $siswa, $kode);

        return $berhasil
            ? redirect()->route('absensimapel.formManual')->with('sukses', $pesan)
            : redirect()->route('absensimapel.formManual')->with('gagal', $pesan);
    }

    // Logika inti pencatatan kehadiran, dipakai baik oleh jalur QR maupun
    // jalur input kode manual supaya aturannya konsisten di kedua jalur.
    private function prosesKehadiran(SesiAbsensiMapel $sesi, ?Siswa $siswa, string $token): array
    {
        if (!$siswa) {
            return [false, 'Akun ini tidak terhubung ke data siswa manapun.'];
        }
        if ($sesi->status !== 'dibuka') {
            return [false, 'Sesi absen ini sudah ditutup oleh guru.'];
        }
        if ((string) $token !== (string) $sesi->qr_token) {
            return [false, 'Kode/QR ini sudah tidak berlaku (kodenya berganti tiap beberapa detik). Minta guru menampilkan yang terbaru.'];
        }
        if ((int) $siswa->kelas_id !== (int) $sesi->kelas_id) {
            return [false, 'Sesi absen ini bukan untuk kelasmu.'];
        }
        if (!$siswa->sudahHadirFaceIdHariIni()) {
            return [false, 'Kamu belum tercatat Hadir di absen Face ID pagi ini, jadi belum bisa absen mapel. Silakan hubungi guru piket/BK.'];
        }

        $existing = AbsensiMapel::where('sesi_absensi_mapel_id', $sesi->id)->where('siswa_id', $siswa->id)->first();
        if ($existing && $existing->status === 'Hadir') {
            return [true, 'Kamu sudah tercatat Hadir di sesi ini sebelumnya.'];
        }

        AbsensiMapel::updateOrCreate(
            ['sesi_absensi_mapel_id' => $sesi->id, 'siswa_id' => $siswa->id],
            ['status' => 'Hadir', 'waktu_scan' => now()]
        );

        return [true, "Absen {$sesi->mataPelajaran->nama_mapel} berhasil dicatat! Selamat mengikuti pelajaran."];
    }

    // ==================== DETEKSI MEMBOLOS (Guru BK/Piket) ====================

    // Siswa yang Hadir di Face ID pagi TAPI "Tanpa Keterangan" di salah satu
    // sesi mapel hari ini -> diberi flag "Terindikasi Membolos Jam Ke-X".
    public function deteksiBolos()
    {
        $tanggal = now()->toDateString();

        $flagged = AbsensiMapel::with(['siswa.kelas', 'sesi.mataPelajaran'])
            ->where('status', 'Tanpa Keterangan')
            ->whereHas('sesi', fn ($q) => $q->whereDate('tanggal', $tanggal))
            ->whereHas('siswa', fn ($q) => $q->whereHas('absensi', fn ($qq) =>
                $qq->whereDate('tanggal', $tanggal)->where('status', 'Hadir')
            ))
            ->get()
            ->groupBy(fn ($a) => $a->siswa_id);

        return view('absensimapel.bolos', compact('flagged', 'tanggal'));
    }
}
