<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use \App\Models\Concerns\BelongsToSekolah;

    protected $table = 'siswa';
    protected $fillable = [
        'user_id', 'nisn', 'nama', 'kelas_id', 'jenis_kelamin',
        'tanggal_lahir', 'alamat', 'nama_wali', 'no_hp_wali', 'foto'
    ];

    /** Urut per KELAS (X, XI, XII lalu nama kelas), di dalam kelas urut abjad nama. Dipakai di semua daftar & pilihan nama. */
    public function scopeUrutKelas($q)
    {
        return $q->leftJoin('kelas as k_urut', 'k_urut.id', '=', 'siswa.kelas_id')
            ->select('siswa.*')
            ->orderByRaw("CASE k_urut.tingkat WHEN 'X' THEN 1 WHEN 'XI' THEN 2 WHEN 'XII' THEN 3 ELSE 9 END")
            ->orderBy('k_urut.nama_kelas')
            ->orderBy('siswa.nama');
    }

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
            // Absen yang masih "menunggu"/"ditolak" TIDAK dihitung sebagai hadir.
            ->where('status_persetujuan', 'disetujui')
            ->exists();
    }

    // Persentase kehadiran (dipakai untuk fitur prediksi)
    public function persentaseKehadiran(?array $sem = null)
    {
        $sem = $sem ?? \App\Support\Semester::terpilih();
        // Absen yang masih menunggu persetujuan belum dihitung (belum final). Dihitung per semester terpilih.
        $dasar = fn () => \App\Support\Semester::filterTanggal($this->absensi()->where('status_persetujuan', '!=', 'menunggu'), $sem, 'tanggal');
        $total = $dasar()->count();
        if ($total == 0) return 100;
        $hadir = $dasar()->where('status', 'Hadir')->count();
        return round(($hadir / $total) * 100, 2);
    }

    // Rata-rata nilai akhir semua mapel pada semester terpilih
    public function rataRataNilai(?array $sem = null)
    {
        $sem = $sem ?? \App\Support\Semester::terpilih();
        return round($this->nilai()->where('semester', $sem['semester'])->where('tahun_ajaran', $sem['tahun_ajaran'])->avg('nilai_akhir') ?? 0, 2);
    }
}
