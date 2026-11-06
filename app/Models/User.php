<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'foto', 'sekolah_id', 'password_awal', 'gagal_login', 'dikunci_sampai'];
    protected $casts = ['dikunci_sampai' => 'datetime'];

    // Catatan: User sengaja TIDAK memakai filter sekolah otomatis (dipakai untuk memuat akun login).
    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->sekolah_id) && $user->role !== 'super_admin' && ($id = \App\Support\Tenant::id())) {
                $user->sekolah_id = $id;
            }
        });
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }
    protected $hidden = ['password', 'remember_token', 'password_awal'];

    /** Simpan password awal terenkripsi (APP_KEY) supaya admin bisa membagikannya ke pemilik akun. */
    public static function enkripsiAwal(string $password): string
    {
        return \Illuminate\Support\Facades\Crypt::encryptString($password);
    }

    public function passwordAwalTerbaca(): ?string
    {
        if (!$this->password_awal) {
            return null;
        }
        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($this->password_awal);
        } catch (\Throwable $e) {
            return null;
        }
    }

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
