<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankSoal extends Model
{
    protected $table = 'bank_soal';
    protected $fillable = [
        'mata_pelajaran_id', 'pertanyaan', 'gambar', 'pilihan_a', 'pilihan_b',
        'pilihan_c', 'pilihan_d', 'pilihan_e', 'jawaban_benar', 'tingkat_kesulitan'
    ];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }
}
