<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SemesterSekolah extends Model
{
    use \App\Models\Concerns\BelongsToSekolah;

    protected $table = 'semester_sekolah';
    protected $fillable = ['semester', 'tahun_ajaran'];
}
