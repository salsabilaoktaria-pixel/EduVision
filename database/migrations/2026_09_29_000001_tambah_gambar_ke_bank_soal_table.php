<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Beberapa soal (terutama IPA/Matematika/Geografi) butuh gambar pendukung
// (grafik, peta, diagram, dsb) — kolom ini menyimpan path gambar tsb, opsional.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_soal', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('pertanyaan');
        });
    }

    public function down(): void
    {
        Schema::table('bank_soal', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
    }
};
