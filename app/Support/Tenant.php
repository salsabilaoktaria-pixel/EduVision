<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

/**
 * Sekolah yang sedang "aktif" pada permintaan ini.
 * - Akun sekolah (admin/guru/siswa): sekolah miliknya sendiri.
 * - super_admin: null -> tidak difilter (melihat semua).
 * - API / seeder: dipaksa lewat Tenant::paksa().
 */
class Tenant
{
    private static bool $dipaksa = false;
    private static ?int $idPaksa = null;

    public static function paksa(?int $sekolahId): void
    {
        self::$dipaksa = true;
        self::$idPaksa = $sekolahId;
    }

    public static function lepas(): void
    {
        self::$dipaksa = false;
        self::$idPaksa = null;
    }

    public static function id(): ?int
    {
        if (self::$dipaksa) {
            return self::$idPaksa;
        }
        // Model User sengaja TIDAK memakai filter sekolah, jadi memuat user di sini aman (tanpa rekursi).
        return Auth::check() ? (Auth::user()->sekolah_id ?: null) : null;
    }
}
