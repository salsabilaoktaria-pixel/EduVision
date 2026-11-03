<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Satu baris = status kehadiran SATU siswa di SATU sesi absen mapel.
// - 'Hadir'            : berhasil scan QR (waktu_scan terisi otomatis)
// - 'Izin' / 'Sakit'   : diisi manual oleh guru (siswa yang sudah izin/sakit
//                         dari pagi tidak perlu ikut scan QR)
// - 'Tanpa Keterangan' : otomatis oleh sistem kalau lewat 5 menit dari sesi
//                         dibuka siswa belum scan (lihat terapkanTimeout()) —
//                         inilah status yang dipakai untuk deteksi "membolos".
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_mapel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sesi_absensi_mapel_id');
            $table->unsignedBigInteger('siswa_id');
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Tanpa Keterangan'])->default('Hadir');
            $table->timestamp('waktu_scan')->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->foreign('sesi_absensi_mapel_id')->references('id')->on('sesi_absensi_mapel')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->unique(['sesi_absensi_mapel_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_mapel');
    }
};
