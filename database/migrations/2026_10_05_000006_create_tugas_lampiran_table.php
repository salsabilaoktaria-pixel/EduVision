<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Lampiran yang disertakan GURU saat membuat/memberi tugas: boleh berupa
// file (.docx, .pdf, gambar, .txt, video) ATAU link, dan boleh lebih dari satu.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas_lampiran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tugas_id');
            $table->enum('tipe', ['file', 'link']);
            $table->string('file_path')->nullable();
            $table->string('file_nama_asli')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();

            $table->foreign('tugas_id')->references('id')->on('tugas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_lampiran');
    }
};
