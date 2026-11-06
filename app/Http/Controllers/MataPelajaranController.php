<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $semua = MataPelajaran::with('guru', 'kelas', 'kurikulum')->orderBy('nama_mapel')->get();
        // Dikelompokkan PER SEMESTER: mapel berlabel "Semua" muncul di Ganjil maupun Genap.
        $perSemester = [
            'Ganjil' => $semua->whereIn('semester', ['Ganjil', 'Semua'])->values(),
            'Genap' => $semua->whereIn('semester', ['Genap', 'Semua'])->values(),
        ];
        return view('mapel.index', compact('perSemester'));
    }

    public function create()
    {
        $guru = Guru::all();
        $kurikulum = \App\Models\Kurikulum::orderByDesc('aktif')->orderBy('nama')->get();
        return view('mapel.create', compact('guru', 'kurikulum'));
    }

    public function show(MataPelajaran $mapel)
    {
        $mapel->load('guru', 'kelas', 'kurikulum');
        return view('mapel.show', compact('mapel'));
    }

    // Menyimpan/menyinkronkan mapel ini diajarkan di kelas mana saja
    public function assignKelas(Request $request, MataPelajaran $mapel)
    {
        $request->validate(['kelas_ids' => 'nullable|array']);
        $mapel->kelas()->sync($request->input('kelas_ids', []));
        return back()->with('sukses', 'Penempatan mapel per kelas berhasil disimpan.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mapel' => ['required', \Illuminate\Validation\Rule::unique('mata_pelajaran', 'kode_mapel')->where('sekolah_id', \App\Support\Tenant::id())],
            'nama_mapel' => 'required|string|max:255',
            'guru_id' => 'nullable|exists:guru,id',
            'kkm' => 'required|integer|min:0|max:100',
            'kurikulum_id' => 'nullable|exists:kurikulum,id',
            'semester' => 'required|in:Ganjil,Genap,Semua',
        ]);
        MataPelajaran::create($validated);
        return redirect()->route('mapel.index')->with('sukses', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(MataPelajaran $mapel)
    {
        $guru = Guru::all();
        $kelas = Kelas::all();
        $kurikulum = \App\Models\Kurikulum::orderByDesc('aktif')->orderBy('nama')->get();
        $mapel->load('kelas');
        return view('mapel.edit', compact('mapel', 'guru', 'kelas', 'kurikulum'));
    }

    public function update(Request $request, MataPelajaran $mapel)
    {
        $validated = $request->validate([
            'kode_mapel' => ['required', \Illuminate\Validation\Rule::unique('mata_pelajaran', 'kode_mapel')->where('sekolah_id', \App\Support\Tenant::id())->ignore($mapel->id)],
            'nama_mapel' => 'required|string|max:255',
            'guru_id' => 'nullable|exists:guru,id',
            'kkm' => 'required|integer|min:0|max:100',
            'kurikulum_id' => 'nullable|exists:kurikulum,id',
            'semester' => 'required|in:Ganjil,Genap,Semua',
        ]);
        $mapel->update($validated);
        return redirect()->route('mapel.index')->with('sukses', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mapel)
    {
        $mapel->delete();
        return redirect()->route('mapel.index')->with('sukses', 'Mata pelajaran berhasil dihapus.');
    }
}
