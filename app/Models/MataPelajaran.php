<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';
    protected $fillable = ['kode_mapel', 'nama_mapel', 'guru_id', 'kkm'];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    public function bankSoal()
    {
        return $this->hasMany(BankSoal::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_mata_pelajaran');
    }
}
