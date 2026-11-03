<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Sama seperti pengumpulan_tugas_file, tapi untuk riwayat (log) pengumpulan:
// satu baris riwayat_pengumpulan_tugas sekarang mewakili SATU KALI aksi kumpul
// (boleh berisi banyak file), dan file-filenya disimpan di tabel anak ini.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_pengumpulan_tugas_file', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('riwayat_pengumpulan_tugas_id');
            $table->string('file_path');
            $table->string('file_nama_asli');
            $table->timestamps();

            $table->foreign('riwayat_pengumpulan_tugas_id', 'fk_riwayat_tugas_file')
                ->references('id')->on('riwayat_pengumpulan_tugas')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_pengumpulan_tugas_file');
    }
};
