<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CekHash extends Command
{
    protected $signature = 'keamanan:cek-hash';
    protected $description = 'Memeriksa bahwa password memakai double hashing (HMAC-SHA256 berkunci APP_KEY lalu bcrypt).';

    public function handle(): int
    {
        $hasher = Hash::driver();
        $this->line('Driver hash aktif : ' . get_class($hasher));
        $h = Hash::make('contoh-password');
        $this->line('Contoh hasil hash : ' . $h);
        $polos = password_verify('contoh-password', $h);
        $ganda = Hash::check('contoh-password', $h);
        $this->line('bcrypt polos cocok? ' . ($polos ? 'YA (BERBAHAYA, hanya 1 lapis)' : 'tidak (benar: hash tidak bisa diuji tanpa APP_KEY)'));
        $this->line('Hash::check cocok? ' . ($ganda ? 'ya' : 'TIDAK (bermasalah)'));
        $this->line('Hash salah ditolak? ' . (Hash::check('salah', $h) ? 'TIDAK (bermasalah)' : 'ya'));

        $lama = User::withoutGlobalScopes()->get()->filter(fn ($u) => $u->password && !Hash::check('password', $u->password) && password_verify('password', $u->password))->count();
        if ($lama > 0) {
            $this->warn($lama . ' akun masih memakai hash 1 lapis lama. Jalankan: php artisan migrate:fresh --seed (atau minta pemilik akun mengganti password).');
        }
        return ($ganda && !$polos) ? self::SUCCESS : self::FAILURE;
    }
}
