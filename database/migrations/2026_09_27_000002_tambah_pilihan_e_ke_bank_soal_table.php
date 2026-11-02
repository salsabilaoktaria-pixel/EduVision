<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TEMUAN PENTING dari file soal ujian ASLI (ATS Sejarah SMA Negeri 11
 * Palembang): standar soal pilihan ganda di Indonesia itu 5 PILIHAN (A-E),
 * bukan 4 (A-D) seperti asumsi awal aplikasi ini. Migration ini menambah
 * kolom pilihan_e (opsional/nullable — soal lama yang cuma 4 pilihan tetap
 * valid) dan memperluas enum jawaban_benar supaya bisa juga "E".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_soal', function (Blueprint $table) {
            $table->string('pilihan_e')->nullable()->after('pilihan_d');
        });

        // Ubah enum jawaban_benar dari ('A','B','C','D') jadi ('A','B','C','D','E').
        // SQLite tidak punya ALTER COLUMN enum, tapi karena Laravel merepresentasikan
        // enum SQLite sebagai CHECK constraint yang otomatis ditangani ulang oleh
        // schema builder saat ->change(), ini aman dipakai di kedua database.
        Schema::table('bank_soal', function (Blueprint $table) {
            $table->enum('jawaban_benar', ['A', 'B', 'C', 'D', 'E'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bank_soal', function (Blueprint $table) {
            $table->enum('jawaban_benar', ['A', 'B', 'C', 'D'])->nullable()->change();
            $table->dropColumn('pilihan_e');
        });
    }
};
