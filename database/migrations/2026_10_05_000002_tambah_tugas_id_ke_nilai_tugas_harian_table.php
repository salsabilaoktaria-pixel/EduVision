<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tambahan tugas_id (nullable) ke nilai_tugas_harian: baris tugas harian yang
// berasal dari fitur "Pengumpulan Tugas" ditandai dengan tugas_id ini, supaya
// kalau guru menilai ulang/ubah nilai tugas yang sama, baris yang di-UPDATE
// adalah baris yang sama (tidak numpuk duplikat), dan tidak tertukar hanya
// karena dua tugas kebetulan punya judul yang sama.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilai_tugas_harian', function (Blueprint $table) {
            $table->unsignedBigInteger('tugas_id')->nullable()->after('nilai_id');
        });
    }

    public function down(): void
    {
        Schema::table('nilai_tugas_harian', function (Blueprint $table) {
            $table->dropColumn('tugas_id');
        });
    }
};
