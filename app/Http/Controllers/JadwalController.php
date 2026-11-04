<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    private const URUTAN_HARI = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'guru']);

        if ($request->filled('kelas_id')) {
            // Guru pilih kelas tertentu (mis. wali kelas mau lihat jadwal LENGKAP
            // kelasnya, bukan cuma jadwal mengajarnya sendiri) -> tampilkan semua
            // mapel di kelas itu, tidak dibatasi ke guru yang login.
            $query->where('kelas_id', $request->kelas_id);
        } elseif ($user->isGuru() && !$user->isAdmin() && $user->guru) {
            // Default untuk guru: langsung tampilkan JADWAL MENGAJARNYA SENDIRI,
            // supaya "tersambung" begitu login — tidak perlu cari-cari dulu.
            $query->where('guru_id', $user->guru->id);
        }

        $jadwal = $query->get()->sortBy(fn ($j) => self::URUTAN_HARI[$j->hari] ?? 9)->groupBy('hari');

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('jadwal.index', compact('jadwal', 'kelasList'));
    }

    // Siswa: jadwal pelajaran KELASNYA SENDIRI, satu minggu penuh.
    public function jadwalSaya()
    {
        $siswa = Auth::user()->siswa;
        $jadwal = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->where('kelas_id', $siswa->kelas_id)
            ->get()
            ->sortBy(fn ($j) => self::URUTAN_HARI[$j->hari] ?? 9)
            ->groupBy('hari');

        return view('jadwal.saya', compact('jadwal', 'siswa'));
    }

    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $mapel = MataPelajaran::orderBy('nama_mapel')->get();
        $guru = Guru::orderBy('nama')->get();
        return view('jadwal.create', compact('kelas', 'mapel', 'guru'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:guru,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_ke' => 'required|array|min:1',
            'jam_ke.*' => 'integer|min:1|max:12',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ], [
            'jam_ke.required' => 'Pilih minimal satu jam ke.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);

        $jamKe = collect($validated['jam_ke'])->map(fn ($j) => (int) $j)->sort()->values()->all();

        $bentrok = $this->cariBentrok($validated['kelas_id'], $validated['hari'], $jamKe);
        if ($bentrok) {
            return back()->withInput()->with('gagal', "Jam ke bentrok dengan jadwal {$bentrok->mataPelajaran->nama_mapel} yang sudah ada di hari {$validated['hari']} (jam ke {$bentrok->labelJamKe()}).");
        }

        JadwalPelajaran::create([
            'kelas_id' => $validated['kelas_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'guru_id' => $validated['guru_id'],
            'hari' => $validated['hari'],
            'jam_ke' => $jamKe,
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
        ]);

        return redirect()->route('jadwal.index')->with('sukses', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function edit(JadwalPelajaran $jadwal)
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $mapel = MataPelajaran::orderBy('nama_mapel')->get();
        $guru = Guru::orderBy('nama')->get();
        return view('jadwal.edit', compact('jadwal', 'kelas', 'mapel', 'guru'));
    }

    public function update(Request $request, JadwalPelajaran $jadwal)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:guru,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_ke' => 'required|array|min:1',
            'jam_ke.*' => 'integer|min:1|max:12',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ], [
            'jam_ke.required' => 'Pilih minimal satu jam ke.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);

        $jamKe = collect($validated['jam_ke'])->map(fn ($j) => (int) $j)->sort()->values()->all();

        $bentrok = $this->cariBentrok($validated['kelas_id'], $validated['hari'], $jamKe, $jadwal->id);
        if ($bentrok) {
            return back()->withInput()->with('gagal', "Jam ke bentrok dengan jadwal {$bentrok->mataPelajaran->nama_mapel} yang sudah ada di hari {$validated['hari']} (jam ke {$bentrok->labelJamKe()}).");
        }

        $jadwal->update([
            'kelas_id' => $validated['kelas_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'guru_id' => $validated['guru_id'],
            'hari' => $validated['hari'],
            'jam_ke' => $jamKe,
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
        ]);

        return redirect()->route('jadwal.index')->with('sukses', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy(JadwalPelajaran $jadwal)
    {
        $jadwal->delete();
        return back()->with('sukses', 'Jadwal berhasil dihapus.');
    }

    // Cegah bentrok: kelas yang sama, hari yang sama, jam ke yang tumpang tindih.
    // $kecualiId dipakai saat EDIT supaya jadwal itu sendiri tidak dianggap bentrok dengan dirinya sendiri.
    private function cariBentrok(int $kelasId, string $hari, array $jamKe, ?int $kecualiId = null)
    {
        return JadwalPelajaran::where('kelas_id', $kelasId)
            ->where('hari', $hari)
            ->when($kecualiId, fn ($q) => $q->where('id', '!=', $kecualiId))
            ->get()
            ->first(fn ($j) => count(array_intersect($j->jam_ke, $jamKe)) > 0);
    }
}
