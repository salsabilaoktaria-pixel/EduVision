<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TugasHarian extends Model
{
    protected $table = 'nilai_tugas_harian';
    protected $fillable = ['nilai_id', 'tugas_id', 'nama_tugas', 'skor', 'tanggal'];

    public function nilai()
    {
        return $this->belongsTo(Nilai::class, 'nilai_id');
    }

    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }
}
