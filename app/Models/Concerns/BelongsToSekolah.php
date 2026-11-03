<?php

namespace App\Models\Concerns;

use App\Models\Sekolah;
use App\Support\Tenant;
use Illuminate\Database\Eloquent\Builder;

/**
 * Pasang di model yang datanya milik satu sekolah. Semua query otomatis dibatasi ke sekolah
 * akun yang sedang login, dan data baru otomatis diberi sekolah_id yang sama.
 */
trait BelongsToSekolah
{
    protected static function bootBelongsToSekolah(): void
    {
        static::addGlobalScope('sekolah', function (Builder $q) {
            if ($id = Tenant::id()) {
                $q->where($q->getModel()->getTable() . '.sekolah_id', $id);
            }
        });

        static::creating(function ($model) {
            if (empty($model->sekolah_id) && ($id = Tenant::id())) {
                $model->sekolah_id = $id;
            }
        });
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }
}
