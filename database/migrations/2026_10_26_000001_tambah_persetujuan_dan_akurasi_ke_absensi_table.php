<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Alur persetujuan absen Face ID:
// - 'disetujui' : Hadir + di dalam radius sekolah (otomatis), ATAU sudah disetujui admin/guru.
// - 'menunggu'  : siswa memilih Izin/Sakit/Lainnya, atau berada di luar radius sekolah.
//                 Absen tetap tersimpan, tapi baru dihitung setelah disetujui admin/guru.
// - 'ditolak'   : admin/guru menolak; siswa boleh mengirim ulang absennya.
// Kolom akurasi_meter menyimpan ketelitian GPS HP siswa saat absen, dipakai untuk
// mengurangi selisih jarak yang muncul hanya karena GPS kurang presisi.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->decimal('akurasi_meter', 10, 2)->nullable()->after('jarak_ke_sekolah_meter');
            $table->enum('status_persetujuan', ['disetujui', 'menunggu', 'ditolak'])->default('disetujui')->after('di_area_sekolah');
            $table->string('alasan_menunggu', 255)->nullable()->after('status_persetujuan');
            $table->string('catatan_persetujuan', 255)->nullable()->after('alasan_menunggu');
            $table->unsignedBigInteger('diputuskan_oleh')->nullable()->after('catatan_persetujuan');
            $table->timestamp('diputuskan_pada')->nullable()->after('diputuskan_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn(['akurasi_meter', 'status_persetujuan', 'alasan_menunggu', 'catatan_persetujuan', 'diputuskan_oleh', 'diputuskan_pada']);
        });
    }
};
