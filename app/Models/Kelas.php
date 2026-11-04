<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $fillable = ['nama_kelas', 'tingkat', 'wali_kelas_id'];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsToMany(MataPelajaran::class, 'kelas_mata_pelajaran');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalPelajaran::class, 'kelas_id');
    }
}
