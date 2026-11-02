<?php

namespace App\Http\Controllers;

use App\Models\Prestasi;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PrestasiController extends Controller
{
    public function show(Prestasi $prestasi)
    {
        $prestasi->load('siswa.kelas');
        $bukti = $prestasi->file_bukti ? asset('storage/' . $prestasi->file_bukti) : null;
        $gambar = $bukti && preg_match('/\.(jpg|jpeg|png)$/i', $prestasi->file_bukti);
        return view('detail', [
            'judul' => 'Detail Prestasi',
            'kembali' => route('prestasi.index'),
            'baris' => [
                'Nama Siswa' => $prestasi->siswa->nama ?? '-',
                'Kelas' => $prestasi->siswa->kelas->nama_kelas ?? '-',
                'Prestasi' => $prestasi->nama_prestasi,
                'Tingkat' => $prestasi->tingkat,
                'Peringkat' => $prestasi->peringkat,
                'Tanggal' => $prestasi->tanggal ? \Carbon\Carbon::parse($prestasi->tanggal)->translatedFormat('d F Y') : null,
            ],
            'gambar' => $gambar ? $bukti : null,
            'tambahan' => $bukti ? [['label' => $gambar ? 'Buka Sertifikat' : 'Lihat Berkas', 'url' => $bukti, 'ikon' => 'image', 'baru' => true]] : [],
            'edit' => route('prestasi.edit', $prestasi),
            'hapus' => route('prestasi.destroy', $prestasi),
        ]);
    }

    public function index(Request $request)
    {
        $query = Prestasi::with('siswa.kelas');

        // BUG LAMA: pencarian hanya mengecek nama_prestasi, padahal user sering
        // mencari berdasarkan NAMA SISWA. Sekarang keduanya dicek sekaligus.
        if ($request->filled('cari')) {
            $kata = $request->cari;
            $query->where(function ($q) use ($kata) {
                $q->where('nama_prestasi', 'like', '%' . $kata . '%')
                  ->orWhereHas('siswa', function ($qs) use ($kata) {
                      $qs->where('nama', 'like', '%' . $kata . '%');
                  });
            });
        }

        // Urutkan berdasarkan TANGGAL PRESTASI asli (bukan kapan datanya disimpan),
        // supaya urutan tetap konsisten berdasarkan bulan & tanggal sebenarnya
        // walau data diinput tidak berurutan.
        \App\Support\Semester::filterTanggal($query, \App\Support\Semester::terpilih(), 'tanggal');
        $prestasi = $query->orderByDesc('tanggal')->orderByDesc('created_at')->get();
        $prestasiPerSiswa = $prestasi->groupBy('siswa_id');

        // Dikelompokkan per KELAS sama seperti halaman SPP: kalau sedang tidak
        // mencari, SEMUA siswa ditampilkan (termasuk yang belum punya prestasi
        // sama sekali) supaya wali kelas bisa lihat seisi kelasnya sekaligus.
        // Kalau sedang mencari, hanya siswa yang hasil pencariannya cocok yang
        // ditampilkan (siswa yang namanya cocok, atau siswa pemilik prestasi
        // yang judulnya cocok).
        if ($request->filled('cari')) {
            $siswaSemua = Siswa::with('kelas')->whereIn('id', $prestasiPerSiswa->keys())->get();
        } else {
            $siswaSemua = Siswa::with('kelas')->urutKelas()->get();
        }

        $siswaPerKelas = $siswaSemua
            ->sortBy(fn ($s) => sprintf(
                '%02d-%s-%s',
                $s->kelas->tingkat ?? 99,
                $s->kelas->nama_kelas ?? 'zzz',
                $s->nama ?? ''
            ))
            ->groupBy(fn ($s) => $s->kelas->nama_kelas ?? 'Tanpa Kelas');

        // REKAP per siswa (SEMUA waktu): satu siswa bisa punya banyak prestasi; yang lama tidak pernah
        // tertimpa, setiap prestasi baru hanya menambah hitungan.
        $semuaPrestasi = Prestasi::with('siswa.kelas')->orderByDesc('tanggal')->get();
        $rekap = $semuaPrestasi->groupBy('siswa_id')
            ->map(fn ($g) => ['siswa' => $g->first()->siswa, 'jumlah' => $g->count(), 'daftar' => $g])
            ->filter(fn ($r) => $r['siswa'])
            ->sortByDesc('jumlah')->values();
        $totalPerSiswa = $rekap->mapWithKeys(fn ($r) => [$r['siswa']->id => $r['jumlah']]);
        $ringkas = [
            'semester' => $prestasi->count(),
            'siswa' => $prestasiPerSiswa->count(),
            'total' => $semuaPrestasi->count(),
        ];

        return view('prestasi.index', compact('siswaPerKelas', 'prestasiPerSiswa', 'rekap', 'totalPerSiswa', 'ringkas'));
    }

    public function create()
    {
        $siswa = Siswa::with('kelas')->urutKelas()->get();
        return view('prestasi.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'nama_prestasi' => 'required|string|max:255',
            'tingkat' => 'required|string',
            'peringkat' => 'nullable|string',
            'tanggal' => 'required|date',
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        if ($request->hasFile('file_bukti')) {
            $validated['file_bukti'] = $request->file('file_bukti')->store('prestasi', 'public');
        }

        Prestasi::create($validated);

        // Tampilkan semester tempat prestasi baru itu berada supaya langsung terlihat di rekap.
        $kunci = \App\Support\Semester::kunci(\App\Support\Semester::dari($validated['tanggal']));
        if (collect(\App\Support\Semester::opsi())->contains(fn ($s) => \App\Support\Semester::kunci($s) === $kunci)) {
            $request->session()->put(\App\Support\Semester::SESI, $kunci);
        }

        return redirect()->route('prestasi.index')->with('sukses', 'Prestasi berhasil dicatat dan rekap sudah diperbarui.');
    }

    public function edit(Prestasi $prestasi)
    {
        $siswa = Siswa::with('kelas')->urutKelas()->get();
        return view('prestasi.edit', compact('prestasi', 'siswa'));
    }

    public function update(Request $request, Prestasi $prestasi)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'nama_prestasi' => 'required|string|max:255',
            'tingkat' => 'required|string',
            'peringkat' => 'nullable|string',
            'tanggal' => 'required|date',
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        if ($request->hasFile('file_bukti')) {
            $validated['file_bukti'] = $request->file('file_bukti')->store('prestasi', 'public');
        }

        $prestasi->update($validated);

        return redirect()->route('prestasi.index')->with('sukses', 'Prestasi berhasil diperbarui.');
    }

    public function destroy(Prestasi $prestasi)
    {
        $prestasi->delete();
        return redirect()->route('prestasi.index')->with('sukses', 'Data prestasi dihapus.');
    }
}
