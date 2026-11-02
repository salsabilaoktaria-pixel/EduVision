<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/** Kelola banyak sekolah — HANYA super_admin (pemilik platform). */
class SekolahController extends Controller
{
    private function aturan(?Sekolah $sekolah = null): array
    {
        return [
            'nama' => 'required|string|max:255',
            'npsn' => 'nullable|string|max:20',
            'jenjang' => 'required|in:SD,SMP,SMA,SMK,MA',
            'alamat' => 'nullable|string|max:500',
            'kota' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'kepala_sekolah' => 'nullable|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:30',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ];
    }

    public function index()
    {
        $sekolah = Sekolah::orderBy('nama')->get();
        $hitung = fn ($model) => $model::withoutGlobalScopes()->selectRaw('sekolah_id, count(*) as n')->groupBy('sekolah_id')->pluck('n', 'sekolah_id');
        $jumlah = ['siswa' => $hitung(Siswa::class), 'guru' => $hitung(Guru::class), 'kelas' => $hitung(Kelas::class)];
        return view('sekolah.index', compact('sekolah', 'jumlah'));
    }

    public function create()
    {
        return view('sekolah.form', ['sekolah' => new Sekolah(['jenjang' => 'SMA'])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan() + [
            'admin_nama' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($request, $data) {
            $logo = $request->hasFile('logo') ? $request->file('logo')->store('logo-sekolah', 'public') : null;
            $sekolah = Sekolah::create(collect($data)->only(array_keys($this->aturan()))->except('logo')->all() + ['logo' => $logo]);
            User::create([
                'name' => $data['admin_nama'], 'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']), 'role' => 'admin', 'sekolah_id' => $sekolah->id,
            ]);
        });

        return redirect()->route('sekolah.index')->with('sukses', 'Sekolah baru dan akun admin-nya berhasil dibuat.');
    }

    public function show(Sekolah $sekolah)
    {
        $statistik = [
            'Siswa' => Siswa::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->count(),
            'Guru' => Guru::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->count(),
            'Kelas' => Kelas::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->count(),
        ];
        $admin = User::where('sekolah_id', $sekolah->id)->where('role', 'admin')->get();
        return view('sekolah.show', compact('sekolah', 'statistik', 'admin'));
    }

    public function edit(Sekolah $sekolah)
    {
        return view('sekolah.form', compact('sekolah'));
    }

    public function update(Request $request, Sekolah $sekolah)
    {
        $data = $request->validate($this->aturan($sekolah));
        if ($request->hasFile('logo')) {
            if ($sekolah->logo) {
                Storage::disk('public')->delete($sekolah->logo);
            }
            $data['logo'] = $request->file('logo')->store('logo-sekolah', 'public');
        } else {
            unset($data['logo']);
        }
        $sekolah->update($data);
        return redirect()->route('sekolah.show', $sekolah)->with('sukses', 'Data sekolah diperbarui.');
    }

    public function destroy(Sekolah $sekolah)
    {
        // Urutan hapus: anak dulu, induk belakangan, supaya tidak melanggar relasi antar tabel.
        $urutan = ['absensi_mapel', 'sesi_absensi_mapel', 'pengumpulan_tugas', 'tugas', 'materi', 'bank_soal', 'jadwal_pelajaran',
            'nilai', 'absensi', 'spp', 'prestasi', 'aspirasi', 'komite_pembayaran', 'api_klien', 'siswa', 'mata_pelajaran', 'kelas', 'guru'];
        try {
            DB::transaction(function () use ($sekolah, $urutan) {
                $idUser = User::where('sekolah_id', $sekolah->id)->pluck('id');
                DB::table('notifikasi')->whereIn('user_id', $idUser)->delete();
                foreach ($urutan as $tabel) {
                    DB::table($tabel)->where('sekolah_id', $sekolah->id)->delete();
                }
                User::where('sekolah_id', $sekolah->id)->delete();
                if ($sekolah->logo) {
                    Storage::disk('public')->delete($sekolah->logo);
                }
                $sekolah->delete();
            });
        } catch (\Throwable $e) {
            return back()->with('gagal', 'Sekolah belum bisa dihapus karena masih ada data yang saling terkait.');
        }
        return redirect()->route('sekolah.index')->with('sukses', 'Sekolah beserta seluruh datanya dihapus.');
    }
}
