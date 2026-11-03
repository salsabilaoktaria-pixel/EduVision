<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogKeamanan extends Model
{
    protected $table = 'log_keamanan';
    protected $fillable = ['user_id', 'sekolah_id', 'aksi', 'keterangan', 'ip'];

    public static function catat(string $aksi, ?string $keterangan = null, ?int $userId = null): void
    {
        try {
            $u = auth()->user();
            static::create([
                'user_id' => $userId ?? ($u->id ?? null),
                'sekolah_id' => $u->sekolah_id ?? null,
                'aksi' => $aksi,
                'keterangan' => $keterangan,
                'ip' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // pencatatan tidak boleh mengganggu proses utama
        }
    }
}
