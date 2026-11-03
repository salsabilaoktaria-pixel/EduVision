<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Log riwayat SETIAP kali siswa mengumpulkan/mengumpulkan-ulang tugas.
// Tabel pengumpulan_tugas (satu baris per siswa+tugas) tetap jadi acuan "pengumpulan
// TERKINI" untuk dinilai guru, sedangkan tabel ini menyimpan jejak semua versi file/link
// yang pernah dikirim, supaya siswa bisa melihat riwayat pengumpulannya sendiri.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tugas_id');
            $table->unsignedBigInteger('siswa_id');
            $table->enum('tipe', ['file', 'link']);
            $table->string('file_path')->nullable();
            $table->string('file_nama_asli')->nullable();
            $table->string('link')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('dikumpulkan_pada');
            $table->timestamps();

            $table->foreign('tugas_id')->references('id')->on('tugas')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_pengumpulan_tugas');
    }
};
