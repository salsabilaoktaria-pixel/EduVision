<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{
    public function gantiPassword()
    {
        return view('akun.ganti-password');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8|confirmed|different:password_lama',
        ], [
            'password_baru.different' => 'Password baru harus berbeda dari password lama.',
        ]);

        if (!Hash::check($validated['password_lama'], $user->password)) {
            return back()->withInput()->withErrors(['password_lama' => 'Password lama yang kamu masukkan salah.']);
        }

        $user->password = Hash::make($validated['password_baru']);
        $user->password_awal = null; // pemilik sudah memegang password sendiri
        $user->gagal_login = 0;
        $user->dikunci_sampai = null;
        $user->save();
        \App\Models\LogKeamanan::catat('ganti_password', 'Pemilik akun mengganti password.');

        return back()->with('sukses', 'Password berhasil diganti. Gunakan password baru ini untuk login berikutnya.');
    }

    // Profil lengkap milik sendiri (semua peran): biodata, akun, dan ringkasan.
    public function profil()
    {
        $user = Auth::user()->load('sekolah');
        $siswa = $user->isSiswa() ? $user->siswa()->with('kelas.waliKelas')->first() : null;
        $guru = $user->isGuru() ? $user->guru()->with('mataPelajaran', 'kelasWali')->first() : null;
        $sekolah = $user->sekolah_id ? \App\Models\Sekolah::withoutGlobalScopes()->find($user->sekolah_id) : null;

        $tgl = fn ($d) => $d ? \Carbon\Carbon::parse($d)->translatedFormat('d F Y') : '-';
        $peran = ['super_admin' => 'Super Admin', 'admin' => 'Admin Sekolah', 'guru' => 'Guru', 'wali_kelas' => 'Guru / Wali Kelas', 'siswa' => 'Siswa'][$user->role] ?? ucfirst($user->role);

        $akun = ['Nama' => $user->name, 'Email (username login)' => $user->email, 'Peran' => $peran, 'Sekolah' => $sekolah->nama ?? 'Semua sekolah (pemilik platform)'];
        $biodata = [];
        $ringkas = [];
        if ($siswa) {
            $biodata = [
                'NISN' => $siswa->nisn, 'Kelas' => $siswa->kelas->nama_kelas ?? '-',
                'Wali Kelas' => $siswa->kelas->waliKelas->nama ?? '-',
                'Jenis Kelamin' => $siswa->jenis_kelamin ?: '-', 'Tanggal Lahir' => $tgl($siswa->tanggal_lahir),
                'Alamat' => $siswa->alamat ?: '-', 'Nama Wali / Orang Tua' => $siswa->nama_wali ?: '-', 'No. HP Wali' => $siswa->no_hp_wali ?: '-',
            ];
            $ringkas = [
                'Rata-rata nilai (' . \App\Support\Semester::label(\App\Support\Semester::terpilih()) . ')' => $siswa->rataRataNilai(),
                'Kehadiran semester ini' => $siswa->persentaseKehadiran() . '%',
                'Jumlah prestasi' => $siswa->prestasi()->count(),
            ];
        } elseif ($guru) {
            $biodata = [
                'NIP' => $guru->nip, 'Jenis Kelamin' => $guru->jenis_kelamin ?: '-', 'Tanggal Lahir' => $tgl($guru->tanggal_lahir),
                'No. HP' => $guru->no_hp ?: '-', 'Alamat' => $guru->alamat ?: '-',
                'Mata Pelajaran Diampu' => $guru->mataPelajaran->pluck('nama_mapel')->implode(', ') ?: '-',
                'Wali Kelas' => $guru->kelasWali->pluck('nama_kelas')->implode(', ') ?: 'Bukan wali kelas',
            ];
            $ringkas = ['Mapel diampu' => $guru->mataPelajaran->count(), 'Kelas diwalikan' => $guru->kelasWali->count()];
        } elseif ($sekolah) {
            $biodata = [
                'NPSN' => $sekolah->npsn ?: '-', 'Jenjang' => $sekolah->jenjang ?: '-', 'Alamat Sekolah' => $sekolah->alamat ?: '-',
                'Kota' => $sekolah->kota ?: '-', 'Telepon' => $sekolah->telepon ?: '-', 'Kepala Sekolah' => $sekolah->kepala_sekolah ?: '-',
            ];
        }
        $terdaftar = $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-';
        return view('akun.profil', compact('user', 'akun', 'biodata', 'ringkas', 'terdaftar'));
    }
}
