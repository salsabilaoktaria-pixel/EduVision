<?php

namespace App\Models;

use App\Support\Tenant;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $table = 'sekolah';
    protected $fillable = [
        'nama', 'npsn', 'jenjang', 'alamat', 'kota', 'telepon', 'email', 'kepala_sekolah',
        'nip_kepala_sekolah', 'logo', 'latitude', 'longitude', 'radius_meter',
    ];

    // Nama lama (dipakai beberapa tampilan): $sekolah->nama_sekolah
    public function getNamaSekolahAttribute(): string
    {
        return $this->nama;
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    /** Sekolah milik akun yang sedang login (atau bawaan dari config kalau belum ada sama sekali). */
    public static function saatIni(): self
    {
        $id = Tenant::id();
        $data = $id ? static::find($id) : null;
        if ($data) {
            return $data;
        }

        return new static([
            'nama' => 'EduVision',
            'latitude' => config('sekolah.latitude'),
            'longitude' => config('sekolah.longitude'),
            'radius_meter' => config('sekolah.radius_meter'),
        ]);
    }

    public function admin()
    {
        return $this->hasMany(User::class, 'sekolah_id')->where('role', 'admin');
    }
}
