<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SesiAbsensiMapel extends Model
{
    protected $table = 'sesi_absensi_mapel';
    protected $fillable = [
        'jadwal_pelajaran_id', 'kelas_id', 'mata_pelajaran_id', 'guru_id',
        'tanggal', 'jam_ke', 'qr_token', 'qr_diperbarui_pada',
        'dibuka_pada', 'ditutup_pada', 'status',
    ];
    protected $casts = [
        'jam_ke' => 'array',
        'tanggal' => 'date',
        'qr_diperbarui_pada' => 'datetime',
        'dibuka_pada' => 'datetime',
        'ditutup_pada' => 'datetime',
    ];

    // QR diganti tiap sekian detik supaya tidak bisa di-screenshot lalu disebar
    // ke grup WhatsApp — screenshot lama otomatis kadaluarsa dalam hitungan detik.
    const DETIK_UMUR_QR = 8;

    // Kalau dalam sekian menit pertama sejak sesi dibuka siswa belum scan,
    // otomatis ditandai "Tanpa Keterangan" (indikasi membolos).
    const MENIT_BATAS_TIMEOUT = 5;

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_pelajaran_id');
    }

    public function absensiMapel()
    {
        return $this->hasMany(AbsensiMapel::class, 'sesi_absensi_mapel_id');
    }

    public function labelJamKe(): string
    {
        $jam = $this->jam_ke;
        sort($jam);
        if (count($jam) > 1 && $jam === range($jam[0], end($jam))) {
            return $jam[0] . '-' . end($jam);
        }
        return implode(', ', $jam);
    }

    // Buat token acak baru & catat waktu pembaruannya. Dipanggil saat sesi
    // dibuka pertama kali, dan tiap kali frontend guru meminta token baru
    // (lihat AbsensiMapelController::refreshToken).
    public function terbitkanTokenBaru(): void
    {
        // 8 karakter huruf besar+angka — konsisten dengan token awal saat sesi
        // dibuka (lihat AbsensiMapelController::buka) dan dengan panjang kode
        // yang dijanjikan di halaman input manual siswa.
        $this->qr_token = Str::upper(Str::random(8));
        $this->qr_diperbarui_pada = now();
        $this->save();
    }

    // Sisa umur QR saat ini dalam detik (bilangan BULAT, bukan pecahan —
    // ini yang memperbaiki bug "6.189622 detik" di versi sebelumnya).
    public function sisaDetikQr(): int
    {
        $detikBerlalu = $this->qr_diperbarui_pada->diffInSeconds(now());
        return max(0, self::DETIK_UMUR_QR - $detikBerlalu);
    }

    public function waktuTimeout(): Carbon
    {
        return $this->dibuka_pada->copy()->addMinutes(self::MENIT_BATAS_TIMEOUT);
    }

    public function sudahLewatTimeout(): bool
    {
        return now()->greaterThan($this->waktuTimeout());
    }
}
