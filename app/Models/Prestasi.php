<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    protected $table = 'prestasi';
    protected $fillable = ['siswa_id', 'nama_prestasi', 'tingkat', 'peringkat', 'tanggal', 'file_bukti'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
