<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiMapel extends Model
{
    protected $table = 'absensi_mapel';
    protected $fillable = ['sesi_absensi_mapel_id', 'siswa_id', 'status', 'waktu_scan', 'keterangan'];
    protected $casts = ['waktu_scan' => 'datetime'];

    public function sesi()
    {
        return $this->belongsTo(SesiAbsensiMapel::class, 'sesi_absensi_mapel_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
