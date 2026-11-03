<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MateriController extends Controller
{
    // Guru/Admin: daftar materi yang mereka unggah (guru cuma lihat miliknya sendiri,
    // admin lihat semua). Bisa difilter per mapel.
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Materi::with(['mataPelajaran', 'kelas', 'guru']);

        if ($user->isGuru() && !$user->isAdmin() && $user->guru) {
            $query->where('guru_id', $user->guru->id);
        }
        if ($request->filled('mapel_id')) {
            $query->where('mata_pelajaran_id', $request->mapel_id);
        }

        $materi = $query->latest()->get()->groupBy(fn ($m) => $m->mataPelajaran->nama_mapel ?? '-');
        $mapel = MataPelajaran::orderBy('nama_mapel')->get();

        return view('materi.index', compact('materi', 'mapel'));
    }

    public function create()
    {
        $user = Auth::user();
        $mapel = $user->isAdmin()
            ? MataPelajaran::orderBy('nama_mapel')->get()
            : MataPelajaran::where('guru_id', $user->guru->id ?? 0)->orderBy('nama_mapel')->get();
        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('materi.create', compact('mapel', 'kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:link_youtube,file',
            'link' => 'required_if:tipe,link_youtube|nullable|url',
            'file' => 'required_if:tipe,file|nullable|file|max:20480|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png',
        ], [
            'link.required_if' => 'Link YouTube wajib diisi.',
            'file.required_if' => 'File wajib diupload.',
        ]);

        $user = Auth::user();
        $data = [
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'guru_id' => $user->guru->id ?? null,
            'kelas_id' => $validated['kelas_id'] ?? null,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tipe' => $validated['tipe'],
        ];

        if ($validated['tipe'] === 'link_youtube') {
            $data['link'] = $validated['link'];
        } else {
            $file = $request->file('file');
            $data['file_path'] = $file->store('materi', 'public');
            $data['file_nama_asli'] = $file->getClientOriginalName();
        }

        if (!$data['guru_id'] && !$user->isAdmin()) {
            return back()->withInput()->with('gagal', 'Akun kamu tidak terhubung ke data guru manapun, tidak bisa mengunggah materi.');
        }
        // Admin boleh unggah materi tanpa terhubung ke guru manapun (guru_id
        // nullable khusus untuk kasus ini).

        Materi::create($data);
        return redirect()->route('materi.index')->with('sukses', 'Materi pembelajaran berhasil ditambahkan.');
    }

    public function destroy(Materi $materi)
    {
        $user = Auth::user();
        if ($user->isGuru() && !$user->isAdmin() && $materi->guru_id !== ($user->guru->id ?? null)) {
            abort(403, 'Kamu cuma bisa menghapus materi milikmu sendiri.');
        }
        $materi->delete();
        return back()->with('sukses', 'Materi berhasil dihapus.');
    }

    // Siswa: daftar materi untuk KELASNYA sendiri (materi khusus kelasnya,
    // atau materi umum yang berlaku untuk semua kelas / kelas_id kosong).
    public function materiSaya(Request $request)
    {
        $siswa = Auth::user()->siswa;
        $query = Materi::with(['mataPelajaran', 'guru'])
            ->where(function ($q) use ($siswa) {
                $q->where('kelas_id', $siswa->kelas_id)->orWhereNull('kelas_id');
            });

        if ($request->filled('mapel_id')) {
            $query->where('mata_pelajaran_id', $request->mapel_id);
        }

        $materi = $query->latest()->get()->groupBy(fn ($m) => $m->mataPelajaran->nama_mapel ?? '-');
        $mapel = MataPelajaran::orderBy('nama_mapel')->get();

        return view('materi.saya', compact('materi', 'mapel'));
    }
}
