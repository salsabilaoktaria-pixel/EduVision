<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * KURIKULUM + SEMESTER:
 * - Tabel `kurikulum` (per sekolah): Kurikulum Merdeka, K13, dst.
 * - Mapel punya kurikulum_id & semester (Ganjil / Genap / Semua).
 * - Materi & jadwal punya semester + tahun ajaran supaya datanya terpisah per semester.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurikulum', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sekolah_id')->nullable()->index();
            $table->string('nama');
            $table->string('kode', 30)->nullable();
            $table->string('tahun_berlaku', 20)->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(false);
            $table->timestamps();
        });

        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->unsignedBigInteger('kurikulum_id')->nullable()->after('guru_id')->index();
            $table->string('semester', 10)->default('Semua')->after('kurikulum_id'); // Ganjil | Genap | Semua
        });

        foreach (['materi', 'jadwal_pelajaran'] as $tabel) {
            Schema::table($tabel, function (Blueprint $table) {
                $table->string('semester', 10)->nullable();
                $table->string('tahun_ajaran', 9)->nullable();
            });
            $bulan = (int) date('n');
            $tahun = (int) date('Y');
            $sem = $bulan >= 7 ? 'Ganjil' : 'Genap';
            $ta = $bulan >= 7 ? $tahun . '/' . ($tahun + 1) : ($tahun - 1) . '/' . $tahun;
            DB::table($tabel)->update(['semester' => $sem, 'tahun_ajaran' => $ta]);
        }
    }

    public function down(): void
    {
        foreach (['materi', 'jadwal_pelajaran'] as $tabel) {
            Schema::table($tabel, fn (Blueprint $t) => $t->dropColumn(['semester', 'tahun_ajaran']));
        }
        Schema::table('mata_pelajaran', fn (Blueprint $t) => $t->dropColumn(['kurikulum_id', 'semester']));
        Schema::dropIfExists('kurikulum');
    }
};
