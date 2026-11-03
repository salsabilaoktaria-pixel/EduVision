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
        $mapel = MataPelajaran::with('guru', 'kelas')->latest()->paginate(10);
        return view('mapel.index', compact('mapel'));
    }

    public function create()
    {
        $guru = Guru::all();
        return view('mapel.create', compact('guru'));
    }

    public function show(MataPelajaran $mapel)
    {
        $mapel->load('guru', 'kelas');
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
            'kode_mapel' => 'required|unique:mata_pelajaran,kode_mapel',
            'nama_mapel' => 'required|string|max:255',
            'guru_id' => 'nullable|exists:guru,id',
            'kkm' => 'required|integer|min:0|max:100',
        ]);
        MataPelajaran::create($validated);
        return redirect()->route('mapel.index')->with('sukses', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(MataPelajaran $mapel)
    {
        $guru = Guru::all();
        $kelas = Kelas::all();
        $mapel->load('kelas');
        return view('mapel.edit', compact('mapel', 'guru', 'kelas'));
    }

    public function update(Request $request, MataPelajaran $mapel)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|unique:mata_pelajaran,kode_mapel,' . $mapel->id,
            'nama_mapel' => 'required|string|max:255',
            'guru_id' => 'nullable|exists:guru,id',
            'kkm' => 'required|integer|min:0|max:100',
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
