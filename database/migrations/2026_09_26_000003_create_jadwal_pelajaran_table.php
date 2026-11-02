<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Jadwal pelajaran per kelas: menentukan mapel apa, diajar guru siapa, hari apa,
// dan JAM KE berapa saja (bisa lebih dari satu jam sekaligus, contoh Matematika
// kelas X-1 di jam ke 1-3, disimpan sebagai JSON [1,2,3] supaya guru tidak perlu
// buka sesi absen berkali-kali untuk mapel yang sama di jam berurutan).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('mata_pelajaran_id');
            $table->unsignedBigInteger('guru_id');
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jumat", 'Sabtu']);
            $table->json('jam_ke'); // contoh: [1,2,3]
            $table->timestamps();

            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajaran')->onDelete('cascade');
            $table->foreign('guru_id')->references('id')->on('guru')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajaran');
    }
};
