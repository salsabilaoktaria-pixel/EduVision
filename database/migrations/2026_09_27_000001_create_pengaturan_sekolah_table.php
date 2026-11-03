<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Sebelumnya koordinat sekolah HARDCODE di config/sekolah.php (nilai contoh/
// placeholder) — admin tidak bisa mengubahnya lewat aplikasi, harus edit file
// server langsung. Sekarang disimpan di database lewat halaman Pengaturan
// Sekolah, supaya admin bisa mengisi koordinat sekolah yang SESUNGGUHNYA
// (termasuk lewat tombol "pakai lokasi saya sekarang" saat admin sedang
// berada di sekolah), tanpa perlu akses server.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah')->default('Sekolah Kita');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('radius_meter')->default(200);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_sekolah');
    }
};
