<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TugasLampiran extends Model
{
    protected $table = 'tugas_lampiran';
    protected $fillable = ['tugas_id', 'tipe', 'file_path', 'file_nama_asli', 'link'];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function ekstensiFile(): string
    {
        if (!$this->file_path) {
            return '';
        }
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
