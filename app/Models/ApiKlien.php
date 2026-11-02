<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKlien extends Model
{
    use \App\Models\Concerns\BelongsToSekolah;

    protected $table = 'api_klien';
    protected $fillable = ['nama', 'awalan_kunci', 'kunci_hash', 'aktif', 'terakhir_dipakai'];
    protected $casts = ['aktif' => 'boolean', 'terakhir_dipakai' => 'datetime'];

    public static function hash(string $kunci): string
    {
        return hash('sha256', $kunci);
    }

    /** Buat klien baru; mengembalikan [model, kunci_asli]. Kunci asli hanya ada di sini. */
    public static function buat(string $nama): array
    {
        $kunci = 'evk_' . Str::random(40);
        $klien = static::create([
            'nama' => $nama,
            'awalan_kunci' => substr($kunci, 0, 8),
            'kunci_hash' => static::hash($kunci),
        ]);
        return [$klien, $kunci];
    }
}
