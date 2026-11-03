<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TugasHarian;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        // TAMPILAN BARU: dikelompokkan PER SISWA (satu siswa = satu blok, semua
        // mapelnya ditampilkan sekaligus di dalamnya) supaya nama siswa tidak
        // muncul dobel/berulang seperti sebelumnya. Lalu dikelompokkan lagi
        // per kelas, dan di dalam tiap kelas siswa diurutkan sesuai ABJAD nama.
        $query = Siswa::with(['kelas', 'nilai' => function ($q) {
            $q->with('mataPelajaran')->orderBy('mata_pelajaran_id');
        }])->whereHas('nilai');

        if ($request->filled('siswa_id')) {
            $query->where('id', $request->siswa_id);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $siswaPerKelas = $query->get()
            ->sortBy([
                fn ($a, $b) => strcasecmp($a->kelas->nama_kelas ?? 'ZZZ', $b->kelas->nama_kelas ?? 'ZZZ'),
                fn ($a, $b) => strcasecmp($a->nama, $b->nama),
            ])
            ->groupBy(fn ($s) => $s->kelas->nama_kelas ?? 'Tanpa Kelas');

        $siswaList = Siswa::orderBy('nama')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('nilai.index', compact('siswaPerKelas', 'siswaList', 'kelasList'));
    }

    public function create()
    {
        // Diurutkan per kelas lalu abjad nama, supaya nanti gampang dikelompokkan
        // pakai <optgroup> per kelas di form (tidak lagi campur semua siswa/tingkat).
        $siswa = Siswa::with('kelas')->get()
            ->sortBy([
                fn ($a, $b) => strcasecmp($a->kelas->nama_kelas ?? 'ZZZ', $b->kelas->nama_kelas ?? 'ZZZ'),
                fn ($a, $b) => strcasecmp($a->nama, $b->nama),
            ])
            ->groupBy(fn ($s) => $s->kelas->nama_kelas ?? 'Tanpa Kelas');
        $mapel = MataPelajaran::all();
        return view('nilai.create', compact('siswa', 'mapel'));
    }

    public function store(Request $request)
    {
        // ALUR BARU: saat pertama dibuat, belum ada nilai apapun yang diisi.
        // Guru diarahkan dulu ke halaman "Tugas Harian" untuk input nilai tugas
        // harian satu per satu, baru setelah itu lanjut isi UTS & UAS.
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'semester' => 'required|string',
            'tahun_ajaran' => 'required|string',
        ]);

        // Cegah data nilai dobel: siswa+mapel+semester+tahun ajaran yang sama
        // tidak boleh dibuat dua kali, kalau sudah ada arahkan ke data yang sudah ada.
        $sudahAda = Nilai::where('siswa_id', $validated['siswa_id'])
            ->where('mata_pelajaran_id', $validated['mata_pelajaran_id'])
            ->where('semester', $validated['semester'])
            ->where('tahun_ajaran', $validated['tahun_ajaran'])
            ->first();

        if ($sudahAda) {
            return redirect()->route('nilai.tugas', $sudahAda)
                ->with('sukses', 'Data nilai untuk siswa, mapel, semester, dan tahun ajaran ini sudah ada. Berikut datanya.');
        }

        $validated['nilai_tugas'] = 0;
        $validated['nilai_uts'] = 0;
        $validated['nilai_uas'] = 0;
        $validated['nilai_akhir'] = 0;

        $nilai = Nilai::create($validated);

        return redirect()->route('nilai.tugas', $nilai)
            ->with('sukses', 'Data nilai dibuat. Silakan input nilai tugas harian dulu di bawah ini.');
    }

    public function edit(Nilai $nilai)
    {
        $siswa = Siswa::all();
        $mapel = MataPelajaran::all();
        return view('nilai.edit', compact('nilai', 'siswa', 'mapel'));
    }

    public function update(Request $request, Nilai $nilai)
    {
        // ALUR BARU: Nilai Tugas TIDAK diedit manual di sini lagi — nilainya berasal
        // dari rata-rata Tugas Harian (kelola di halaman "Tugas Harian"). Di sini
        // guru hanya mengisi Nilai UTS dan Nilai UAS setelah tugas harian selesai.
        $validated = $request->validate([
            'semester' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'nilai_uts' => 'required|numeric|min:0|max:100',
            'nilai_uas' => 'required|numeric|min:0|max:100',
        ]);

        $nilai->semester = $validated['semester'];
        $nilai->tahun_ajaran = $validated['tahun_ajaran'];
        $nilai->nilai_uts = $validated['nilai_uts'];
        $nilai->nilai_uas = $validated['nilai_uas'];
        $nilai->nilai_akhir = Nilai::hitungNilaiAkhir($nilai->nilai_tugas, $nilai->nilai_uts, $nilai->nilai_uas);
        $nilai->save();

        return redirect()->route('nilai.index')->with('sukses', 'Nilai UTS/UAS berhasil disimpan, Nilai Akhir sudah dihitung ulang.');
    }

    public function destroy(Nilai $nilai)
    {
        $nilai->delete();
        return redirect()->route('nilai.index')->with('sukses', 'Nilai berhasil dihapus.');
    }

    /**
     * FITUR BARU: Kelola nilai tugas harian.
     * Guru mapel biasanya punya banyak nilai tugas harian per siswa (tugas 1, tugas 2, kuis, dst).
     * Daripada menghitung manual, di sini guru bisa menambahkan tiap nilai tugas satu per satu,
     * lalu sistem otomatis menghitung rata-ratanya dan bisa langsung diterapkan sebagai "Nilai Tugas".
     */
    public function kelolaTugas(Nilai $nilai)
    {
        $nilai->load('tugasHarian', 'siswa', 'mataPelajaran');
        return view('nilai.tugas', compact('nilai'));
    }

    public function simpanTugas(Request $request, Nilai $nilai)
    {
        $validated = $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'skor' => 'required|numeric|min:0|max:100',
            'tanggal' => 'nullable|date',
        ]);
        $validated['nilai_id'] = $nilai->id;
        TugasHarian::create($validated);

        return back()->with('sukses', 'Nilai tugas berhasil ditambahkan.');
    }

    public function hapusTugas(TugasHarian $tugasHarian)
    {
        $nilaiId = $tugasHarian->nilai_id;
        $tugasHarian->delete();
        return redirect()->route('nilai.tugas', $nilaiId)->with('sukses', 'Nilai tugas dihapus.');
    }

    // Menghitung rata-rata semua tugas harian, lalu menerapkannya jadi nilai_tugas & menghitung ulang nilai_akhir
    public function terapkanRataTugas(Nilai $nilai)
    {
        $rataRata = $nilai->rataRataTugasHarian();

        $nilai->nilai_tugas = $rataRata;
        $nilai->nilai_akhir = Nilai::hitungNilaiAkhir($rataRata, $nilai->nilai_uts, $nilai->nilai_uas);
        $nilai->save();

        return redirect()->route('nilai.tugas', $nilai)->with('sukses', "Rata-rata {$rataRata} berhasil diterapkan sebagai Nilai Tugas.");
    }
}
