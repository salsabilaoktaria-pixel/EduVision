<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $fillable = [
        'siswa_id', 'tanggal', 'jam_masuk', 'status', 'keterangan_detail',
        'foto_wajah', 'latitude', 'longitude', 'terverifikasi',
        'jarak_ke_sekolah_meter', 'di_area_sekolah',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Hitung jarak dua koordinat GPS pakai rumus Haversine, hasil dalam meter.
     * Dipakai untuk mengecek apakah siswa absen dari area sekolah atau tidak.
     */
    public static function hitungJarakMeter($lat1, $lon1, $lat2, $lon2)
    {
        $bumiRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($bumiRadius * $c, 2);
    }

    public function tautanGoogleMaps()
    {
        if (!$this->latitude || !$this->longitude) return null;
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }
}
