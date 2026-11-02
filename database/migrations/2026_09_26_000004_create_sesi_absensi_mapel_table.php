<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// SESI ABSEN QR: satu baris = satu kali guru "membuka" absen QR untuk 1 mapel
// di 1 kelas pada 1 tanggal (boleh mencakup beberapa jam ke sekaligus).
// QR yang ditampilkan ke proyektor itu SELALU berisi qr_token milik sesi ini,
// dan qr_token-nya berganti tiap beberapa detik (lihat AbsensiMapelController)
// supaya tidak bisa di-screenshot lalu disebar.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_absensi_mapel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_pelajaran_id')->nullable();
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('mata_pelajaran_id');
            $table->unsignedBigInteger('guru_id');
            $table->date('tanggal');
            $table->json('jam_ke'); // snapshot jam ke yang dipakai sesi ini
            $table->string('qr_token', 64);
            $table->timestamp('qr_diperbarui_pada')->useCurrent();
            $table->timestamp('dibuka_pada')->useCurrent();
            $table->timestamp('ditutup_pada')->nullable();
            $table->enum('status', ['dibuka', 'ditutup'])->default('dibuka');
            $table->timestamps();

            $table->foreign('jadwal_pelajaran_id')->references('id')->on('jadwal_pelajaran')->onDelete('set null');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajaran')->onDelete('cascade');
            $table->foreign('guru_id')->references('id')->on('guru')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_absensi_mapel');
    }
};
