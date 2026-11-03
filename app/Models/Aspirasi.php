<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspirasi extends Model
{
    protected $table = 'aspirasi';
    protected $fillable = ['user_id', 'judul', 'isi', 'anonim', 'status', 'tanggapan'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
