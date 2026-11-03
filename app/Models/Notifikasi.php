<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Notifikasi di dalam aplikasi (lonceng di navbar + halaman /notifikasi).
 * Dibuat lewat helper statis supaya pemanggilnya (controller) cukup satu baris.
 */
class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $fillable = ['user_id', 'jenis', 'judul', 'isi', 'tautan', 'dibaca_pada'];
    protected $casts = ['dibaca_pada' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBelumDibaca($q)
    {
        return $q->whereNull('dibaca_pada');
    }

    /** Ikon (nama untuk <x-ikon>) per jenis notifikasi. */
    public function namaIkon(): string
    {
        return [
            'absensi' => 'camera', 'tugas' => 'edit', 'spp' => 'credit-card',
            'nilai' => 'bar-chart', 'aspirasi' => 'message', 'info' => 'info',
        ][$this->jenis] ?? 'bell';
    }

    /** Kirim ke satu akun. Aman dipanggil dengan $userId null (diabaikan). */
    public static function kirim(?int $userId, string $jenis, string $judul, ?string $isi = null, ?string $tautan = null): void
    {
        if (!$userId) {
            return;
        }
        static::create(compact('jenis', 'judul', 'isi', 'tautan') + ['user_id' => $userId]);
    }

    /** Kirim ke semua akun dengan peran tertentu, mis. ['admin','guru','wali_kelas']. */
    public static function kirimKePeran(array $peran, string $jenis, string $judul, ?string $isi = null, ?string $tautan = null): void
    {
        $sekarang = now();
        $baris = User::whereIn('role', $peran)
            ->when(\App\Support\Tenant::id(), fn ($q, $sid) => $q->where('sekolah_id', $sid))
            ->pluck('id')->map(fn ($id) => [
            'user_id' => $id, 'jenis' => $jenis, 'judul' => $judul, 'isi' => $isi,
            'tautan' => $tautan, 'created_at' => $sekarang, 'updated_at' => $sekarang,
        ])->all();
        if ($baris) {
            static::insert($baris);
        }
    }

    /** Kirim ke semua akun siswa pada satu kelas. */
    public static function kirimKeKelas(?int $kelasId, string $jenis, string $judul, ?string $isi = null, ?string $tautan = null): void
    {
        if (!$kelasId) {
            return;
        }
        $sekarang = now();
        $baris = Siswa::where('kelas_id', $kelasId)->whereNotNull('user_id')->pluck('user_id')->map(fn ($id) => [
            'user_id' => $id, 'jenis' => $jenis, 'judul' => $judul, 'isi' => $isi,
            'tautan' => $tautan, 'created_at' => $sekarang, 'updated_at' => $sekarang,
        ])->all();
        if ($baris) {
            static::insert($baris);
        }
    }
}
