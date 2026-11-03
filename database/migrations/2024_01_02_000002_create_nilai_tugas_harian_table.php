<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Menyimpan banyak nilai tugas harian per satu baris "nilai" (per siswa+mapel+semester),
    // supaya rata-ratanya bisa dihitung otomatis jadi nilai_tugas.
    public function up(): void
    {
        Schema::create('nilai_tugas_harian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nilai_id');
            $table->string('nama_tugas');
            $table->decimal('skor', 5, 2);
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_tugas_harian');
    }
};
