<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';
    protected $fillable = [
        'mata_pelajaran_id', 'guru_id', 'kelas_id', 'judul', 'deskripsi',
        'tipe', 'link', 'file_path', 'file_nama_asli',
    ];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    // Ubah link YouTube biasa (watch?v=, youtu.be/, shorts/) jadi URL embed,
    // supaya videonya bisa ditampilkan langsung di halaman lewat <iframe>.
    public function linkEmbedYoutube(): ?string
    {
        if ($this->tipe !== 'link_youtube' || !$this->link) return null;

        $id = null;
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|shorts\/|embed\/))([A-Za-z0-9_-]{6,})/', $this->link, $m)) {
            $id = $m[1];
        }
        return $id ? "https://www.youtube.com/embed/{$id}" : null;
    }

    // Ekstensi file (dipakai untuk pilih ikon di tampilan)
    public function ekstensiFile(): ?string
    {
        if (!$this->file_nama_asli) return null;
        return strtolower(pathinfo($this->file_nama_asli, PATHINFO_EXTENSION));
    }
}
