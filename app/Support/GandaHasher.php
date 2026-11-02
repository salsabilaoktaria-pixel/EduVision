<?php

namespace App\Support;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Hashing\BcryptHasher;

/**
 * DOUBLE HASHING untuk password (dan nilai rahasia lain yang disimpan lewat Hash::make).
 *
 * Alurnya dua lapis:
 *   1) Lapis 1 — HMAC-SHA256 dengan kunci rahasia server ("pepper" = APP_KEY).
 *      Hasilnya selalu 32 byte tetap, jadi password panjang tidak terpotong oleh batas
 *      72 byte milik bcrypt, dan hash yang bocor dari database TIDAK bisa diuji/dibobol
 *      tanpa APP_KEY milik server.
 *   2) Lapis 2 — bcrypt (berbiaya tinggi + salt acak per password) atas hasil lapis 1.
 *
 * Dipasang sebagai driver "ganda" (lihat config/hashing.php & AppServiceProvider), sehingga
 * Hash::make(), Hash::check(), Auth::attempt(), dan cast 'hashed' otomatis memakainya —
 * tidak ada controller yang perlu diubah satu per satu.
 */
class GandaHasher implements Hasher
{
    private BcryptHasher $bcrypt;

    public function __construct(array $opsiBcrypt = [])
    {
        $this->bcrypt = new BcryptHasher($opsiBcrypt);
    }

    // Lapis 1: HMAC-SHA256 berkunci APP_KEY, dikodekan base64 (44 karakter, aman untuk bcrypt).
    private function lapisPertama(string $nilai): string
    {
        return base64_encode(hash_hmac('sha256', $nilai, (string) config('app.key'), true));
    }

    public function info($hashedValue)
    {
        return $this->bcrypt->info($hashedValue);
    }

    public function make($value, array $options = [])
    {
        return $this->bcrypt->make($this->lapisPertama((string) $value), $options);
    }

    public function check($value, $hashedValue, array $options = [])
    {
        if ($hashedValue === null || $hashedValue === '') {
            return false;
        }
        return $this->bcrypt->check($this->lapisPertama((string) $value), $hashedValue, $options);
    }

    public function needsRehash($hashedValue, array $options = [])
    {
        return $this->bcrypt->needsRehash($hashedValue, $options);
    }
}
