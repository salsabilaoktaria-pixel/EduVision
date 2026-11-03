<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengumpulanTugas extends Model
{
    protected $table = 'pengumpulan_tugas';
    protected $fillable = [
        'tugas_id', 'siswa_id', 'tipe', 'file_path', 'file_nama_asli',
        'link', 'catatan', 'dikumpulkan_pada', 'nilai', 'catatan_guru',
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
    // sekaligus); file_path/file_nama_asli lama tetap ada untuk pengumpulan LAMA
    // (sebelum fitur multi-file) yang cuma punya 1 file.
    public function files()
    {
        return $this->hasMany(PengumpulanTugasFile::class);
    }

    public function terlambat(): bool
    {
        if (!$this->tugas) {
            return false;
        }
        return $this->dikumpulkan_pada->greaterThan($this->tugas->deadline);
    }

    public function ekstensiFile(): string
    {
        if (!$this->file_path) {
            return '';
        }
        return strtoupper(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }
}
