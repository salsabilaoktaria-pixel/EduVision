<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    use \App\Models\Concerns\BelongsToSekolah;

    protected $table = 'prestasi';
    protected $fillable = ['siswa_id', 'nama_prestasi', 'tingkat', 'peringkat', 'tanggal', 'file_bukti'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
