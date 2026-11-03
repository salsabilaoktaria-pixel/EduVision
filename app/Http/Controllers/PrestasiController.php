<?php

namespace App\Http\Controllers;

use App\Models\Prestasi;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PrestasiController extends Controller
{
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
            $siswaSemua = Siswa::with('kelas')->get();
        }

        $siswaPerKelas = $siswaSemua
            ->sortBy(fn ($s) => sprintf(
                '%02d-%s-%s',
                $s->kelas->tingkat ?? 99,
                $s->kelas->nama_kelas ?? 'zzz',
                $s->nama ?? ''
            ))
            ->groupBy(fn ($s) => $s->kelas->nama_kelas ?? 'Tanpa Kelas');

        return view('prestasi.index', compact('siswaPerKelas', 'prestasiPerSiswa'));
    }

    public function create()
    {
        $siswa = Siswa::all();
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

        return redirect()->route('prestasi.index')->with('sukses', 'Prestasi berhasil dicatat.');
    }

    public function edit(Prestasi $prestasi)
    {
        $siswa = Siswa::all();
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
