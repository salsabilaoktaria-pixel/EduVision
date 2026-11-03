<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\TugasLampiran;
use App\Models\PengumpulanTugas;
use App\Models\PengumpulanTugasFile;
use App\Models\RiwayatPengumpulanTugas;
use App\Models\RiwayatPengumpulanTugasFile;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\TugasHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TugasController extends Controller
{
    // ===== Sisi GURU/ADMIN: kelola tugas =====

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Tugas::with(['mataPelajaran', 'kelas', 'guru', 'pengumpulan']);

        if ($user->isGuru() && !$user->isAdmin() && $user->guru) {
            // Guru biasa cuma lihat tugas yang DIA buat sendiri.
            $query->where('guru_id', $user->guru->id);
        }

        $tugas = $query->latest()->get();
        return view('tugas.index', compact('tugas'));
    }

    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $mapel = MataPelajaran::orderBy('nama_mapel')->get();
        return view('tugas.create', compact('kelas', 'mapel'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => 'required|string|max:20',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date|after:now',
            'lampiran_file.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,txt,mp4,mov,avi,webm|max:20480',
            'lampiran_link' => 'nullable|string',
        ], [
            'deadline.after' => 'Deadline harus di waktu yang akan datang.',
            'lampiran_file.*.mimes' => 'Lampiran harus berformat .pdf, .doc, .docx, gambar, .txt, atau video.',
            'lampiran_file.*.max' => 'Setiap lampiran maksimal 20MB.',
        ]);

        $user = Auth::user();
        $validated['guru_id'] = $user->guru->id ?? null;

        $tugas = Tugas::create($validated);
        $this->simpanLampiranDariRequest($request, $tugas);

        return redirect()->route('tugas.index')->with('sukses', 'Tugas berhasil dibuat.');
    }

    /**
     * Simpan lampiran tugas dari guru: boleh berupa beberapa file sekaligus
     * (.docx, .pdf, gambar, .txt, video) DAN/ATAU beberapa link (satu per baris
     * di textarea), supaya guru bisa mengirimkan materi tugas dalam format apa pun.
     */
    private function simpanLampiranDariRequest(Request $request, Tugas $tugas): void
    {
        if ($request->hasFile('lampiran_file')) {
            foreach ($request->file('lampiran_file') as $file) {
                if (!$file) {
                    continue;
                }
                TugasLampiran::create([
                    'tugas_id' => $tugas->id,
                    'tipe' => 'file',
                    'file_path' => $file->store('tugas-lampiran', 'public'),
                    'file_nama_asli' => $file->getClientOriginalName(),
                ]);
            }
        }

        $linkMentah = (string) $request->input('lampiran_link', '');
        foreach (preg_split('/\r\n|\r|\n/', $linkMentah) as $baris) {
            $baris = trim($baris);
            if ($baris !== '' && str_contains($baris, '://')) {
                TugasLampiran::create([
                    'tugas_id' => $tugas->id,
                    'tipe' => 'link',
                    'link' => $baris,
                ]);
            }
        }
    }

    public function edit(Tugas $tugas)
    {
        $this->pastikanBolehKelola($tugas);
        $tugas->load('lampiran');
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $mapel = MataPelajaran::orderBy('nama_mapel')->get();
        return view('tugas.edit', compact('tugas', 'kelas', 'mapel'));
    }

    public function update(Request $request, Tugas $tugas)
    {
        $this->pastikanBolehKelola($tugas);

        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => 'required|string|max:20',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date',
            'lampiran_file.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,txt,mp4,mov,avi,webm|max:20480',
            'lampiran_link' => 'nullable|string',
        ], [
            'lampiran_file.*.mimes' => 'Lampiran harus berformat .pdf, .doc, .docx, gambar, .txt, atau video.',
            'lampiran_file.*.max' => 'Setiap lampiran maksimal 20MB.',
        ]);

        $tugas->update($validated);

        // Lampiran baru (file/link) ditambahkan, lampiran lama yang sudah ada
        // TIDAK dihapus otomatis — hapus manual lewat tombol "Hapus" di halaman edit.
        $this->simpanLampiranDariRequest($request, $tugas);

        // Samakan juga nama tugas di baris nilai tugas harian yang sudah pernah
        // tersalur otomatis dari tugas ini (lihat beriNilai()), supaya namanya
        // tetap konsisten kalau judul tugas ini diganti.
        TugasHarian::where('tugas_id', $tugas->id)->update(['nama_tugas' => $tugas->judul]);

        return redirect()->route('tugas.index')->with('sukses', 'Tugas berhasil diperbarui.');
    }

    /**
     * Hapus satu lampiran tugas (file atau link) yang pernah ditambahkan guru.
     */
    public function hapusLampiran(TugasLampiran $lampiran)
    {
        $this->pastikanBolehKelola($lampiran->tugas);

        if ($lampiran->file_path) {
            Storage::disk('public')->delete($lampiran->file_path);
        }
        $lampiran->delete();

        return back()->with('sukses', 'Lampiran berhasil dihapus.');
    }

    public function show(Tugas $tugas)
    {
        $this->pastikanBolehKelola($tugas);

        $tugas->load(['mataPelajaran', 'kelas.siswa', 'pengumpulan.siswa', 'pengumpulan.files', 'lampiran']);

        // Gabungkan semua siswa di kelas itu dengan status pengumpulannya (biar
        // yang belum kumpul pun kelihatan sebagai "Belum Dikumpulkan").
        $pengumpulanPerSiswa = $tugas->pengumpulan->keyBy('siswa_id');
        $daftarSiswa = $tugas->kelas->siswa->map(function ($siswa) use ($pengumpulanPerSiswa) {
            return [
                'siswa' => $siswa,
                'pengumpulan' => $pengumpulanPerSiswa->get($siswa->id),
            ];
        })->sortBy(fn ($d) => $d['siswa']->nama);

        return view('tugas.show', compact('tugas', 'daftarSiswa'));
    }

    public function destroy(Tugas $tugas)
    {
        $this->pastikanBolehKelola($tugas);

        $tugas->delete();
        return redirect()->route('tugas.index')->with('sukses', 'Tugas berhasil dihapus.');
    }

    // Hanya guru PEMILIK tugas (atau admin, kalau suatu saat diberi akses lagi)
    // yang boleh melihat/mengubah/menghapus tugas ini.
    private function pastikanBolehKelola(Tugas $tugas): void
    {
        $user = Auth::user();
        if ($user->isGuru() && !$user->isAdmin() && $tugas->guru_id !== ($user->guru->id ?? null)) {
            abort(403, 'Kamu tidak punya akses ke tugas ini.');
        }
    }

    /**
     * Memberi nilai pengumpulan tugas siswa — SEKALIGUS otomatis menyalurkan
     * nilai itu sebagai satu baris "Nilai Tugas Harian" di fitur Input Nilai,
     * pada mapel+semester+tahun ajaran yang sama dengan tugas ini, dengan
     * TANGGAL yang disamakan dengan waktu siswa MENGUMPULKAN tugasnya (bukan
     * tanggal hari ini saat guru menilai). Guru tidak perlu input manual lagi
     * ke halaman Input Nilai — begitu nilai di sini disimpan, rata-rata Nilai
     * Tugas siswa itu langsung ikut diperbarui.
     */
    public function beriNilai(Request $request, PengumpulanTugas $pengumpulan)
    {
        $validated = $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'catatan_guru' => 'nullable|string|max:500',
        ]);

        $pengumpulan->update($validated);
        $pengumpulan->load('tugas', 'siswa');
        $tugas = $pengumpulan->tugas;

        if ($tugas && $pengumpulan->siswa) {
            // Cari (atau buat) data Nilai untuk siswa+mapel+semester+tahun ajaran tugas ini.
            $nilai = Nilai::firstOrCreate(
                [
                    'siswa_id' => $pengumpulan->siswa_id,
                    'mata_pelajaran_id' => $tugas->mata_pelajaran_id,
                    'semester' => $tugas->semester,
                    'tahun_ajaran' => $tugas->tahun_ajaran,
                ],
                [
                    'nilai_tugas' => 0,
                    'nilai_uts' => 0,
                    'nilai_uas' => 0,
                    'nilai_akhir' => 0,
                ]
            );

            // updateOrCreate berdasarkan tugas_id supaya kalau guru menilai ULANG
            // pengumpulan yang sama, baris tugas hariannya di-UPDATE, tidak dobel.
            TugasHarian::updateOrCreate(
                ['nilai_id' => $nilai->id, 'tugas_id' => $tugas->id],
                [
                    'nama_tugas' => $tugas->judul,
                    'skor' => $validated['nilai'],
                    'tanggal' => $pengumpulan->dikumpulkan_pada?->toDateString(),
                ]
            );

            // Langsung terapkan rata-rata terbaru sebagai Nilai Tugas & hitung ulang Nilai Akhir,
            // sesuai permintaan: "tanpa harus input manual" di halaman Input Nilai.
            $rataRata = $nilai->rataRataTugasHarian();
            $nilai->nilai_tugas = $rataRata;
            $nilai->nilai_akhir = Nilai::hitungNilaiAkhir($rataRata, $nilai->nilai_uts, $nilai->nilai_uas);
            $nilai->save();
        }

        return back()->with('sukses', 'Nilai berhasil disimpan untuk ' . ($pengumpulan->siswa->nama ?? 'siswa') . ', dan otomatis masuk ke Input Nilai.');
    }

    // ===== Sisi SISWA: lihat & kumpulkan tugas =====

    public function tugasSaya()
    {
        $siswa = Auth::user()->siswa;
        $tugas = Tugas::with(['mataPelajaran', 'guru'])
            ->where('kelas_id', $siswa->kelas_id)
            ->with(['pengumpulan' => fn ($q) => $q->where('siswa_id', $siswa->id)])
            ->latest('deadline')
            ->get();

        return view('tugas.saya', compact('tugas', 'siswa'));
    }

    public function formKumpulkan(Tugas $tugas)
    {
        $siswa = Auth::user()->siswa;
        if ($tugas->kelas_id !== $siswa->kelas_id) {
            abort(403, 'Tugas ini bukan untuk kelasmu.');
        }

        $tugas->load('lampiran');

        $pengumpulanSaya = PengumpulanTugas::with('files')
            ->where('tugas_id', $tugas->id)->where('siswa_id', $siswa->id)->first();

        // Riwayat semua kali siswa ini mengumpulkan tugas ini (termasuk yang sudah
        // "ditimpa" oleh pengumpulan yang lebih baru), terbaru di atas.
        $riwayat = RiwayatPengumpulanTugas::with('files')
            ->where('tugas_id', $tugas->id)
            ->where('siswa_id', $siswa->id)
            ->latest('dikumpulkan_pada')
            ->get();

        return view('tugas.kumpulkan', compact('tugas', 'pengumpulanSaya', 'riwayat'));
    }

    public function prosesKumpulkan(Request $request, Tugas $tugas)
    {
        $siswa = Auth::user()->siswa;
        if ($tugas->kelas_id !== $siswa->kelas_id) {
            abort(403, 'Tugas ini bukan untuk kelasmu.');
        }

        // Siswa sekarang bisa mengumpulkan LEBIH DARI SATU file sekaligus
        // (foto, dokumen, video, bahkan .zip), bukan cuma 1 file seperti sebelumnya.
        $validated = $request->validate([
            'tipe' => 'required|in:file,link',
            'file' => 'required_if:tipe,file|nullable|array|max:5',
            'file.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,txt,mp4,mov,avi,webm,zip,rar|max:25600',
            'link' => 'required_if:tipe,link|nullable|url',
            'catatan' => 'nullable|string|max:500',
        ], [
            'file.required_if' => 'Silakan pilih minimal 1 file untuk dikumpulkan.',
            'file.max' => 'Maksimal 5 file sekali kumpul.',
            'link.required_if' => 'Silakan isi link tugasnya.',
            'link.url' => 'Link tidak valid, pastikan diawali dengan http:// atau https://',
            'file.*.mimes' => 'File harus berformat .pdf, .doc, .docx, gambar, .txt, video, atau .zip/.rar.',
            'file.*.max' => 'Setiap file maksimal 25MB.',
        ]);

        $data = [
            'tipe' => $validated['tipe'],
            'catatan' => $validated['catatan'] ?? null,
            'dikumpulkan_pada' => now(),
            'link' => $validated['tipe'] === 'link' ? $validated['link'] : null,
            // Kolom file_path/file_nama_asli lama tidak lagi dipakai untuk pengumpulan
            // baru (lihat tabel pengumpulan_tugas_file), dikosongkan supaya tidak rancu.
            'file_path' => null,
            'file_nama_asli' => null,
        ];

        // updateOrCreate supaya siswa bisa submit ULANG (ganti jawaban) tanpa
        // menumpuk baris baru di tabel "status terkini" — baris yang sama di-update,
        // dan ini yang dipakai guru untuk menilai.
        $pengumpulan = PengumpulanTugas::updateOrCreate(
            ['tugas_id' => $tugas->id, 'siswa_id' => $siswa->id],
            $data
        );

        // Ganti isi file "status terkini": file-file dari pengumpulan sebelumnya
        // dibuang (tidak dipakai lagi untuk dinilai), diganti file-file yang baru.
        $pengumpulan->files()->delete();

        // Tapi tetap catat jejaknya di tabel riwayat (append-only), supaya siswa
        // bisa melihat SEMUA file yang pernah dia kumpulkan untuk tugas ini,
        // bukan cuma yang terakhir.
        $riwayat = RiwayatPengumpulanTugas::create([
            'tugas_id' => $tugas->id,
            'siswa_id' => $siswa->id,
            'tipe' => $data['tipe'],
            'link' => $data['link'],
            'catatan' => $data['catatan'],
            'dikumpulkan_pada' => $data['dikumpulkan_pada'],
        ]);

        if ($validated['tipe'] === 'file' && $request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                if (!$file) {
                    continue;
                }
                $path = $file->store('tugas', 'public');
                $namaAsli = $file->getClientOriginalName();

                $pengumpulan->files()->create(['file_path' => $path, 'file_nama_asli' => $namaAsli]);
                $riwayat->files()->create(['file_path' => $path, 'file_nama_asli' => $namaAsli]);
            }
        }

        return redirect()->route('tugas.formKumpulkan', $tugas)->with('sukses', 'Tugas berhasil dikumpulkan.');
    }

    // ===== Kalender tugas (dipakai siswa & guru/admin) =====

    public function kalender(Request $request)
    {
        $user = Auth::user();
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        $query = Tugas::with(['mataPelajaran', 'kelas']);
        if ($user->isSiswa() && $user->siswa) {
            $query->where('kelas_id', $user->siswa->kelas_id);
        } elseif ($user->isGuru() && !$user->isAdmin() && $user->guru) {
            $query->where('guru_id', $user->guru->id);
        }

        $awalBulan = Carbon::create($tahun, $bulan, 1);
        $akhirBulan = $awalBulan->copy()->endOfMonth();

        $tugasBulanIni = $query->whereBetween('deadline', [$awalBulan->copy()->startOfDay(), $akhirBulan->copy()->endOfDay()])
            ->get()
            ->groupBy(fn ($t) => $t->deadline->format('Y-m-d'));

        // Bangun grid kalender (Minggu - Sabtu), termasuk sisa tanggal bulan lalu/depan
        // supaya grid selalu persegi (kelipatan 7 hari).
        $mulaiGrid = $awalBulan->copy()->startOfWeek(Carbon::SUNDAY);
        $akhirGrid = $akhirBulan->copy()->endOfWeek(Carbon::SATURDAY);

        $hariGrid = [];
        $kursor = $mulaiGrid->copy();
        while ($kursor->lte($akhirGrid)) {
            $hariGrid[] = $kursor->copy();
            $kursor->addDay();
        }

        $bulanSebelumnya = $awalBulan->copy()->subMonth();
        $bulanSesudahnya = $awalBulan->copy()->addMonth();

        return view('tugas.kalender', [
            'hariGrid' => $hariGrid,
            'tugasBulanIni' => $tugasBulanIni,
            'awalBulan' => $awalBulan,
            'bulanSebelumnya' => $bulanSebelumnya,
            'bulanSesudahnya' => $bulanSesudahnya,
        ]);
    }
}
