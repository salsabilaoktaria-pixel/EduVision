<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    use \App\Models\Concerns\BelongsToSekolah;

    protected $table = 'kurikulum';
    protected $fillable = ['nama', 'kode', 'tahun_berlaku', 'deskripsi', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function mataPelajaran()
    {
        return $this->hasMany(MataPelajaran::class, 'kurikulum_id');
    }
}
