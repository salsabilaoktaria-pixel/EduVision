<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Menambah opsi "Lainnya" (input manual bebas) di pilihan keterangan absen
// Face ID pagi, selain Hadir/Izin/Sakit yang sudah ada. Teksnya disimpan di
// sini, sedangkan kolom `status` tetap salah satu dari Hadir/Sakit/Izin/Alpa
// (supaya semua kode lain yang sudah bergantung ke enum status itu tidak perlu
// diubah) — kalau siswa pilih "Lainnya", status disimpan sebagai "Izin" dan
// alasan lengkapnya ada di keterangan_detail ini.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->string('keterangan_detail', 255)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn('keterangan_detail');
        });
    }
};
