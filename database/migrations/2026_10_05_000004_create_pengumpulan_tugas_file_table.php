<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Siswa sekarang bisa mengumpulkan LEBIH DARI SATU file untuk satu tugas
// (misal: foto + video sekaligus). Tabel pengumpulan_tugas tetap jadi baris
// "status terkini" per siswa+tugas (dipakai untuk menilai), sedangkan file-file
// yang menyertainya dipindah ke tabel anak ini supaya jumlahnya tidak dibatasi 1.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumpulan_tugas_file', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengumpulan_tugas_id');
            $table->string('file_path');
            $table->string('file_nama_asli');
            $table->timestamps();

            $table->foreign('pengumpulan_tugas_id')->references('id')->on('pengumpulan_tugas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas_file');
    }
};
