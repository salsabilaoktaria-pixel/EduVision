<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabel pengumpulan tugas oleh siswa: bisa berupa file (.docx/.pdf/.jpg/.png) atau link.
// Satu siswa hanya boleh punya SATU baris pengumpulan per tugas (unique), supaya kalau
// submit ulang itu meng-update baris yang sama (tidak numpuk banyak baris).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tugas_id');
            $table->unsignedBigInteger('siswa_id');
            $table->enum('tipe', ['file', 'link']);
            $table->string('file_path')->nullable();
            $table->string('file_nama_asli')->nullable();
            $table->string('link')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('dikumpulkan_pada');
            $table->unsignedTinyInteger('nilai')->nullable();
            $table->text('catatan_guru')->nullable();
            $table->timestamps();

            $table->foreign('tugas_id')->references('id')->on('tugas')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->unique(['tugas_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
