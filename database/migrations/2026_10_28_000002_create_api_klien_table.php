<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris = satu aplikasi luar yang boleh memakai API (mis. aplikasi Komite).
        // Kunci asli TIDAK disimpan, hanya hash SHA-256 (ditampilkan sekali saat dibuat).
        Schema::create('api_klien', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('awalan_kunci', 12);
            $table->string('kunci_hash', 64)->unique();
            $table->boolean('aktif')->default(true);
            $table->timestamp('terakhir_dipakai')->nullable();
            $table->timestamps();
        });

        // Cermin status pembayaran komite yang dikirim aplikasi Komite (sumber kebenaran tetap di sana).
        Schema::create('komite_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->string('periode', 30);                       // mis. "2026/2027" atau "Semester Ganjil 2026"
            $table->decimal('nominal', 12, 2)->default(0);
            $table->string('status', 20)->default('Belum Lunas'); // Lunas | Belum Lunas | Cicilan
            $table->string('referensi', 60)->nullable();          // nomor transaksi di aplikasi Komite
            $table->timestamp('dibayar_pada')->nullable();
            $table->timestamps();
            $table->unique(['siswa_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komite_pembayaran');
        Schema::dropIfExists('api_klien');
    }
};
