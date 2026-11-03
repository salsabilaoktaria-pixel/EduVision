<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sebelumnya kolom jawaban_benar WAJIB diisi (enum A/B/C/D, tidak boleh kosong).
 * Ini membuat proses IMPORT soal dari file gagal total kalau file yang diupload
 * tidak menuliskan kunci jawaban secara eksplisit di dalam teksnya (banyak bank
 * soal asli seperti ini — kunci jawaban dipisah di halaman/file lain).
 *
 * Sekarang kolom dibuat NULLABLE: soal tetap bisa diimpor walau kunci jawabannya
 * belum diketahui, lalu admin/guru bisa melengkapi jawaban benar itu belakangan
 * lewat halaman Edit. Soal yang jawabannya masih kosong akan ditandai di
 * halaman Bank Soal supaya mudah ditemukan dan dilengkapi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_soal', function (Blueprint $table) {
            $table->enum('jawaban_benar', ['A', 'B', 'C', 'D'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bank_soal', function (Blueprint $table) {
            $table->enum('jawaban_benar', ['A', 'B', 'C', 'D'])->nullable(false)->change();
        });
    }
};
