<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $table = 'nilai';
    protected $fillable = [
        'siswa_id', 'mata_pelajaran_id', 'semester', 'tahun_ajaran',
        'nilai_tugas', 'nilai_uts', 'nilai_uas', 'nilai_akhir'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function tugasHarian()
    {
        // Urutkan berdasarkan TANGGAL TUGAS asli (bukan urutan input),
        // supaya daftar tugas tetap kronologis walau diinput acak.
        return $this->hasMany(TugasHarian::class)->orderBy('tanggal')->orderBy('id');
    }

    // Rata-rata semua nilai tugas harian yang sudah diinput guru
    public function rataRataTugasHarian()
    {
        return round($this->tugasHarian()->avg('skor') ?? 0, 2);
    }

    /**
     * ALUR PERHITUNGAN NILAI AKHIR:
     * 1. Semua nilai tugas harian di-rata-ratakan dulu -> jadi satu "Nilai Tugas".
     * 2. Nilai Tugas (hasil rata-rata) itu dijumlahkan dengan Nilai UTS dan Nilai UAS.
     * 3. Hasil penjumlahan itu dirata-ratakan lagi (dibagi 3) -> jadi Nilai Akhir.
     *
     * Jadi bobotnya sama rata (tidak ada persentase berbeda antar komponen), sesuai
     * yang diminta: tugas harian dirata-rata dulu, baru digabung rata-rata dengan UTS+UAS.
     */
    public static function hitungNilaiAkhir($tugas, $uts, $uas)
    {
        return round(($tugas + $uts + $uas) / 3, 2);
    }
}
