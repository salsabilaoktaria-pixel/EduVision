<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $fillable = [
        'siswa_id', 'tanggal', 'jam_masuk', 'status', 'keterangan_detail',
        'foto_wajah', 'latitude', 'longitude', 'terverifikasi',
<<<<<<< HEAD
        'jarak_ke_sekolah_meter', 'akurasi_meter', 'di_area_sekolah',
        'status_persetujuan', 'alasan_menunggu', 'catatan_persetujuan',
        'diputuskan_oleh', 'diputuskan_pada',
    ];

    protected $casts = [
        'diputuskan_pada' => 'datetime',
        'terverifikasi' => 'boolean',
        'di_area_sekolah' => 'boolean',
    ];

    // Batas maksimal kompensasi akurasi GPS (meter). GPS HP/laptop jarang pas
    // sampai 0 meter, jadi sebagian selisih kecil dianggap "kekurangtelitian GPS"
    // bukan "siswa benar-benar berada di tempat lain". Dibatasi supaya tidak
    // bisa disalahgunakan dengan mengklaim akurasi buruk.
    const MAKS_KOMPENSASI_AKURASI = 50;

    /**
     * SATU-SATUNYA rumus "jarak efektif" & "di dalam radius" yang dipakai di mana pun
     * (server saat simpan absen, halaman monitor, dan JavaScript form siswa meniru
     * rumus yang sama persis), supaya angka jarak SELALU sinkron.
     * Jarak efektif = jarak GPS mentah dikurangi toleransi akurasi GPS (min 0).
     * Kalau siswa berdiri persis di titik sekolah, jarak efektif = 0 meter.
     */
    public static function jarakEfektif(float $jarakMentah, ?float $akurasi): float
    {
        $kompensasi = min(max((float) $akurasi, 0), self::MAKS_KOMPENSASI_AKURASI);
        return round(max(0, $jarakMentah - $kompensasi), 2);
    }

    // Jarak & status radius dihitung ULANG terhadap titik sekolah yang berlaku SEKARANG
    // (bukan angka lama yang tersimpan saat absen), supaya kalau admin memperbarui
    // titik sekolah, tampilan monitor langsung ikut sinkron.
    public function jarakTerhadap(PengaturanSekolah $sekolah): ?array
    {
        if (is_null($this->latitude) || is_null($this->longitude)) {
            return null;
        }
        $mentah = self::hitungJarakMeter($sekolah->latitude, $sekolah->longitude, $this->latitude, $this->longitude);
        $efektif = self::jarakEfektif($mentah, $this->akurasi_meter !== null ? (float) $this->akurasi_meter : null);
        return ['mentah' => $mentah, 'efektif' => $efektif, 'di_area' => $efektif <= $sekolah->radius_meter];
    }

    public function menunggu(): bool
    {
        return $this->status_persetujuan === 'menunggu';
    }

=======
        'jarak_ke_sekolah_meter', 'di_area_sekolah',
    ];

>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
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
