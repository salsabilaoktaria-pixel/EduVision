<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $table = 'tugas';
    protected $fillable = ['mata_pelajaran_id', 'guru_id', 'kelas_id', 'semester', 'tahun_ajaran', 'judul', 'deskripsi', 'deadline'];
    protected $casts = ['deadline' => 'datetime'];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pengumpulan()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }

    // Lampiran yang disertakan guru saat memberi tugas ini (file/link, boleh lebih dari satu).
    public function lampiran()
    {
        return $this->hasMany(TugasLampiran::class);
    }

    public function sudahLewatDeadline(): bool
    {
        return now()->greaterThan($this->deadline);
    }
}
