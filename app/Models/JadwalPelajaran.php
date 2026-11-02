<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use \App\Models\Concerns\BelongsToSekolah;

    protected $table = 'jadwal_pelajaran';
    protected $fillable = ['kelas_id', 'mata_pelajaran_id', 'guru_id', 'hari', 'jam_ke', 'jam_mulai', 'jam_selesai', 'semester', 'tahun_ajaran'];
    protected $casts = ['jam_ke' => 'array'];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->semester)) {
                $s = \App\Support\Semester::terpilih();
                $model->semester = $s['semester'];
                $model->tahun_ajaran = $s['tahun_ajaran'];
            }
        });
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    // Contoh tampilan: "1, 2, 3" atau "1-3" kalau berurutan
    public function labelJamKe(): string
    {
        $jam = $this->jam_ke;
        sort($jam);
        if (count($jam) > 1 && $jam === range($jam[0], end($jam))) {
            return $jam[0] . '-' . end($jam);
        }
        return implode(', ', $jam);
    }

    // Contoh tampilan: "07.00 - 08.30"
    public function labelJamClock(): string
    {
        if (!$this->jam_mulai || !$this->jam_selesai) {
            return '-';
        }
        $mulai = substr($this->jam_mulai, 0, 5);
        $selesai = substr($this->jam_selesai, 0, 5);
        return str_replace(':', '.', $mulai) . ' - ' . str_replace(':', '.', $selesai);
    }
}
