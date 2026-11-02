<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use \App\Models\Concerns\BelongsToSekolah;

    protected $table = 'mata_pelajaran';
    protected $fillable = ['kode_mapel', 'nama_mapel', 'guru_id', 'kkm', 'kurikulum_id', 'semester'];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
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
