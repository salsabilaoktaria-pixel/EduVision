<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tambahan semester & tahun ajaran ke tugas, supaya saat guru memberi nilai
// pengumpulan tugas, nilainya bisa otomatis disalurkan ke data Nilai (nilai
// tugas harian) pada semester & tahun ajaran yang BENAR, tanpa perlu ditanya lagi.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->string('semester')->default('Ganjil')->after('kelas_id');
            $table->string('tahun_ajaran')->nullable()->after('semester');
        });
    }

    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->dropColumn(['semester', 'tahun_ajaran']);
        });
    }
};
