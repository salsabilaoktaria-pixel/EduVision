<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspirasi extends Model
{
    use \App\Models\Concerns\BelongsToSekolah;

    protected $table = 'aspirasi';
    protected $fillable = ['user_id', 'judul', 'isi', 'anonim', 'status', 'tanggapan', 'pemilik_hash'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Sidik jari pengirim untuk aspirasi anonim (HMAC-SHA256 berkunci APP_KEY, satu arah). */
    public static function hashPemilik(int $userId): string
    {
        return hash_hmac('sha256', 'aspirasi|' . $userId, (string) config('app.key'));
    }

    /** Aspirasi milik user ini, baik yang bernama maupun yang anonim. */
    public function scopeMilik($q, int $userId)
    {
        return $q->where(function ($x) use ($userId) {
            $x->where('user_id', $userId)->orWhere('pemilik_hash', static::hashPemilik($userId));
        });
    }
}
