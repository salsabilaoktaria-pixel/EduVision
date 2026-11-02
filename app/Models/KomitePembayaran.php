<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomitePembayaran extends Model
{
    use \App\Models\Concerns\BelongsToSekolah;

    protected $table = 'komite_pembayaran';
    protected $fillable = ['siswa_id', 'periode', 'nominal', 'status', 'referensi', 'dibayar_pada'];
    protected $casts = ['dibayar_pada' => 'datetime'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
