<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with('kelas');

        if ($request->filled('cari')) {
            $query->where('nama', 'like', '%' . $request->cari . '%')
                  ->orWhere('nisn', 'like', '%' . $request->cari . '%');
        }

        // Dikelompokkan per tingkat (X, XI, XII) supaya tampilannya terpisah rapi,
        // masing-masing tingkat punya tab dan tabel data sendiri.
        //
        // PENTING: groupBy() di atas Eloquent Collection tetap menghasilkan Eloquent
        // Collection, padahal isinya bukan lagi model Siswa satuan tapi grup-grup.
        // Method except()/get() versi Eloquent Collection mengasumsikan isinya model
        // (butuh getKey()), jadi harus dikonversi dulu ke Support Collection biasa
        // pakai ->toBase() sebelum diotak-atik lebih lanjut — kalau tidak, akan
        // muncul error "Method ...Collection::getKey does not exist".
        $semuaSiswa = $query->latest()->get();
        $siswaPerTingkat = $semuaSiswa->groupBy(function ($s) {
            return $s->kelas->tingkat ?? 'Belum ada kelas';
        })->toBase();

        // Urutkan supaya X, XI, XII muncul rapi (bukan acak)
        $urutan = ['X', 'XI', 'XII'];
        $siswaPerTingkat = collect($urutan)
            ->mapWithKeys(fn($t) => [$t => $siswaPerTingkat->get($t, collect())])
            ->merge($siswaPerTingkat->except($urutan));

        return view('siswa.index', compact('siswaPerTingkat'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|unique:siswa,nisn',
            'nama' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'nama_wali' => 'nullable|string',
            'no_hp_wali' => 'nullable|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'foto' => 'nullable|image|max:2048',
        ], [
            'password.confirmed' => 'Konfirmasi password tidak sama dengan password.',
        ]);

        // Buat akun login untuk siswa sekaligus
        $user = User::create([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'siswa',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('siswa', 'public');
        }

        Siswa::create([
            'user_id' => $user->id,
            'nisn' => $validated['nisn'],
            'nama' => $validated['nama'],
            'kelas_id' => $validated['kelas_id'],
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'nama_wali' => $validated['nama_wali'] ?? null,
            'no_hp_wali' => $validated['no_hp_wali'] ?? null,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('siswa.index')->with('sukses', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa)
    {
        $siswa->load('kelas', 'nilai.mataPelajaran', 'absensi', 'spp', 'prestasi');
        return view('siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
        return view('siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nisn' => 'required|unique:siswa,nisn,' . $siswa->id,
            'nama' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'nama_wali' => 'nullable|string',
            'no_hp_wali' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        $siswa->update($validated);

        return redirect()->route('siswa.index')->with('sukses', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('siswa.index')->with('sukses', 'Data siswa berhasil dihapus.');
    }
}
