<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'foto'];
    protected $hidden = ['password', 'remember_token'];

    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    public function guru()
    {
        return $this->hasOne(Guru::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isGuru()
    {
        return $this->role === 'guru' || $this->role === 'wali_kelas';
    }

    public function isSiswa()
    {
        return $this->role === 'siswa';
    }

    /**
     * Foto profil akun ini. Diambil dari foto yang diupload ADMIN saat menambah
     * data siswa/guru (tabel siswa/guru), supaya tidak perlu upload ulang foto
     * khusus untuk akun — satu foto dipakai untuk data sekolah SEKALIGUS foto
     * profil. Mengembalikan null kalau belum ada foto sama sekali.
     */
    public function fotoPath(): ?string
    {
        return $this->siswa?->foto ?? $this->guru?->foto ?? $this->foto ?? null;
    }

    public function fotoUrl(): ?string
    {
        $path = $this->fotoPath();
        return $path ? asset('storage/'.$path) : null;
    }
}
