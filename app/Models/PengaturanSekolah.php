<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanSekolah extends Model
{
    protected $table = 'pengaturan_sekolah';
    protected $fillable = ['nama_sekolah', 'latitude', 'longitude', 'radius_meter'];

    // Ambil satu-satunya baris pengaturan; kalau admin belum pernah mengisinya
    // sama sekali, jatuhkan ke nilai di config/sekolah.php supaya aplikasi
    // tetap jalan (tidak error) sebelum admin sempat mengatur lokasi asli.
    public static function ambil(): self
    {
        $data = static::first();
        if ($data) {
            return $data;
        }

        return new static([
            'nama_sekolah' => 'Sekolah Kita (belum diatur)',
            'latitude' => config('sekolah.latitude'),
            'longitude' => config('sekolah.longitude'),
            'radius_meter' => config('sekolah.radius_meter'),
        ]);
    }
}
