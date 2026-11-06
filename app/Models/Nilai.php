<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use \App\Models\Concerns\BelongsToSekolah;

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

    /**
     * AKSES NILAI GURU: guru hanya boleh melihat/mengubah nilai MAPEL YANG DIA AMPU.
     * Pengecualian: wali kelas boleh mengakses SEMUA mapel, tetapi hanya untuk siswa di KELAS WALINYA.
     * Admin tidak dibatasi; peran lain tidak punya akses.
     */
    public function scopeDapatDiakses($q, $user)
    {
        if ($user->isAdmin()) {
            return $q;
        }
        $guruId = $user->guru->id ?? 0;
        return $q->where(function ($w) use ($guruId) {
            $w->whereHas('mataPelajaran', fn ($m) => $m->where('guru_id', $guruId))
              ->orWhereHas('siswa.kelas', fn ($k) => $k->where('wali_kelas_id', $guruId));
        });
    }

    public function bolehDiakses($user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return static::whereKey($this->id)->dapatDiakses($user)->exists();
    }

    /** Boleh membuat nilai untuk pasangan siswa-mapel ini? (guru mapel itu, atau wali kelas siswa tsb) */
    public static function bolehPasangan($user, Siswa $siswa, MataPelajaran $mapel): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        $guruId = $user->guru->id ?? 0;
        return $guruId && ($mapel->guru_id === $guruId || ($siswa->kelas && $siswa->kelas->wali_kelas_id === $guruId));
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
