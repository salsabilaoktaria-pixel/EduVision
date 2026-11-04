<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $fillable = [
        'user_id', 'nisn', 'nama', 'kelas_id', 'jenis_kelamin',
        'tanggal_lahir', 'alamat', 'nama_wali', 'no_hp_wali', 'foto'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function spp()
    {
        return $this->hasMany(Spp::class);
    }

<<<<<<< HEAD

=======
>>>>>>> fec47dc2b440329cea55aa66de621f7c0558f476
    public function prestasi()
    {
        return $this->hasMany(Prestasi::class);
    }

    public function absensiMapel()
    {
        return $this->hasMany(AbsensiMapel::class, 'siswa_id');
    }

    // Sudah absen Face ID pagi dengan status Hadir hari ini?
    // Dipakai sebagai syarat sebelum siswa boleh scan QR per-mapel —
    // sesuai spek: "Sistem hanya menerima scan dari siswa yang pagi harinya
    // sudah lolos absen Face ID".
    public function sudahHadirFaceIdHariIni(): bool
    {
        return $this->absensi()
            ->whereDate('tanggal', now()->toDateString())
            ->where('status', 'Hadir')
<<<<<<< HEAD
            // Absen yang masih "menunggu"/"ditolak" TIDAK dihitung sebagai hadir.
            ->where('status_persetujuan', 'disetujui')
=======
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
            ->exists();
    }

    // Persentase kehadiran (dipakai untuk fitur prediksi)
    public function persentaseKehadiran()
    {
<<<<<<< HEAD
        // Absen yang masih menunggu persetujuan belum dihitung (belum final).
        $dasar = fn () => $this->absensi()->where('status_persetujuan', '!=', 'menunggu');
        $total = $dasar()->count();
        if ($total == 0) return 100;
        $hadir = $dasar()->where('status', 'Hadir')->count();
=======
        $total = $this->absensi()->count();
        if ($total == 0) return 100;
        $hadir = $this->absensi()->where('status', 'Hadir')->count();
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
        return round(($hadir / $total) * 100, 2);
    }

    // Rata-rata nilai akhir semua mapel
    public function rataRataNilai()
    {
        return round($this->nilai()->avg('nilai_akhir') ?? 0, 2);
    }
}
