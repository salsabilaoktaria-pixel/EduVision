<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('waliKelas', 'siswa')->latest()->paginate(10);
        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        $guru = Guru::all();
        return view('kelas.create', compact('guru'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
            'tingkat' => 'required|string',
            'wali_kelas_id' => 'nullable|exists:guru,id',
        ], [
            'nama_kelas.unique' => 'Nama kelas ini sudah ada, gunakan nama lain.',
        ]);
        Kelas::create($validated);
        return redirect()->route('kelas.index')->with('sukses', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas)
    {
        $guru = Guru::all();
        return view('kelas.edit', compact('kelas', 'guru'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas,' . $kelas->id,
            'tingkat' => 'required|string',
            'wali_kelas_id' => 'nullable|exists:guru,id',
        ], [
            'nama_kelas.unique' => 'Nama kelas ini sudah ada, gunakan nama lain.',
        ]);
        $kelas->update($validated);
        return redirect()->route('kelas.index')->with('sukses', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('kelas.index')->with('sukses', 'Kelas berhasil dihapus.');
    }
}
