<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - semester_sekolah: semester tambahan (mis. Ganjil 2027/2028) yang didaftarkan admin.
 * - users.password_awal: password awal TERENKRIPSI (APP_KEY) supaya admin bisa membagikannya;
 *   otomatis dikosongkan begitu pemilik akun mengganti password sendiri.
 * - users.gagal_login & dikunci_sampai: penguncian akun sementara setelah terlalu banyak percobaan gagal.
 * - log_keamanan: catatan aktivitas sensitif (lihat password awal, reset password, login gagal/dikunci).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semester_sekolah', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sekolah_id')->nullable()->index();
            $table->string('semester', 10);
            $table->string('tahun_ajaran', 9);
            $table->timestamps();
            $table->unique(['sekolah_id', 'semester', 'tahun_ajaran']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->text('password_awal')->nullable();
            $table->unsignedSmallInteger('gagal_login')->default(0);
            $table->timestamp('dikunci_sampai')->nullable();
        });

        Schema::create('log_keamanan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('sekolah_id')->nullable()->index();
            $table->string('aksi', 60);
            $table->string('keterangan')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_keamanan');
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['password_awal', 'gagal_login', 'dikunci_sampai']));
        Schema::dropIfExists('semester_sekolah');
    }
};
