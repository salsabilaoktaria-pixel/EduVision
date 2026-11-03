<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    protected $table = 'spp';
    protected $fillable = ['siswa_id', 'bulan', 'tahun', 'jumlah', 'status', 'tanggal_bayar'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
