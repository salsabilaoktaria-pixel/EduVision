<?php

namespace App\Http\Controllers;

use App\Models\Kurikulum;
use Illuminate\Http\Request;

class KurikulumController extends Controller
{
    public function index()
    {
        $kurikulum = Kurikulum::withCount('mataPelajaran')->orderByDesc('aktif')->orderByDesc('tahun_berlaku')->get();
        return view('kurikulum.index', compact('kurikulum'));
    }

    public function create()
    {
        return view('kurikulum.form', ['kurikulum' => new Kurikulum(['aktif' => false])]);
    }

    private function aturan(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'kode' => 'nullable|string|max:30',
            'tahun_berlaku' => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string|max:2000',
            'aktif' => 'nullable|boolean',
        ];
    }

    private function simpan(Kurikulum $k, array $data): void
    {
        $data['aktif'] = !empty($data['aktif']);
        $k->fill($data)->save();
        if ($k->aktif) { // hanya satu kurikulum aktif per sekolah
            Kurikulum::where('id', '!=', $k->id)->update(['aktif' => false]);
        }
    }

    public function store(Request $request)
    {
        $this->simpan(new Kurikulum(), $request->validate($this->aturan()));
        return redirect()->route('kurikulum.index')->with('sukses', 'Kurikulum berhasil ditambahkan.');
    }

    public function show(Kurikulum $kurikulum)
    {
        $kurikulum->load('mataPelajaran.guru');
        $perSemester = [
            'Ganjil' => $kurikulum->mataPelajaran->whereIn('semester', ['Ganjil', 'Semua'])->sortBy('nama_mapel')->values(),
            'Genap' => $kurikulum->mataPelajaran->whereIn('semester', ['Genap', 'Semua'])->sortBy('nama_mapel')->values(),
        ];
        return view('kurikulum.show', compact('kurikulum', 'perSemester'));
    }

    public function edit(Kurikulum $kurikulum)
    {
        return view('kurikulum.form', compact('kurikulum'));
    }

    public function update(Request $request, Kurikulum $kurikulum)
    {
        $this->simpan($kurikulum, $request->validate($this->aturan()));
        return redirect()->route('kurikulum.index')->with('sukses', 'Kurikulum berhasil diperbarui.');
    }

    public function destroy(Kurikulum $kurikulum)
    {
        $kurikulum->mataPelajaran()->update(['kurikulum_id' => null]);
        $kurikulum->delete();
        return redirect()->route('kurikulum.index')->with('sukses', 'Kurikulum berhasil dihapus. Mapel di dalamnya tetap ada tanpa kurikulum.');
    }
}
