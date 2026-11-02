<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPengumpulanTugas extends Model
{
    protected $table = 'riwayat_pengumpulan_tugas';
    protected $fillable = [
        'tugas_id', 'siswa_id', 'tipe', 'file_path', 'file_nama_asli',
        'link', 'catatan', 'dikumpulkan_pada',
    ];
    protected $casts = ['dikumpulkan_pada' => 'datetime'];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Banyak file sekarang disimpan di tabel anak ini (siswa bisa kumpul >1 file
    // sekaligus); file_path/file_nama_asli lama tetap ada untuk riwayat LAMA
    // (sebelum fitur multi-file) yang cuma punya 1 file.
    public function files()
    {
        return $this->hasMany(RiwayatPengumpulanTugasFile::class, 'riwayat_pengumpulan_tugas_id');
    }

    public function ekstensiFile(): string
    {
        if (!$this->file_path) {
            return '';
        }
        return strtoupper(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }
}
