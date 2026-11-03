<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Kumpulan materi pembelajaran: bisa berupa link YouTube, atau file yang
// diupload (PDF/DOCX/apapun). kelas_id nullable = materi berlaku untuk SEMUA
// kelas yang mengambil mapel tsb; kalau diisi, cuma untuk kelas itu saja.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mata_pelajaran_id');
            $table->unsignedBigInteger('guru_id')->nullable();
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['link_youtube', 'file']);
            $table->string('link')->nullable();       // dipakai kalau tipe = link_youtube
            $table->string('file_path')->nullable();  // dipakai kalau tipe = file
            $table->string('file_nama_asli')->nullable();
            $table->timestamps();

            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajaran')->onDelete('cascade');
            $table->foreign('guru_id')->references('id')->on('guru')->onDelete('set null');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};
