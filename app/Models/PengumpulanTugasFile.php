<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengumpulanTugasFile extends Model
{
    protected $table = 'pengumpulan_tugas_file';
    protected $fillable = ['pengumpulan_tugas_id', 'file_path', 'file_nama_asli'];

    public function pengumpulanTugas()
    {
        return $this->belongsTo(PengumpulanTugas::class);
    }

    public function ekstensiFile(): string
    {
        return strtoupper(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }

    public function isGambar(): bool
    {
        return in_array($this->ekstensiFile(), ['JPG', 'JPEG', 'PNG', 'GIF', 'WEBP']);
    }

    public function isVideo(): bool
    {
        return in_array($this->ekstensiFile(), ['MP4', 'MOV', 'AVI', 'WEBM', 'MKV']);
    }
}
