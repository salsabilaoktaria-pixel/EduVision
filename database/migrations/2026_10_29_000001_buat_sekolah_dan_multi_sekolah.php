<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * MULTI-SEKOLAH: satu instalasi EduVision dipakai banyak sekolah.
 * - Tabel `sekolah` = identitas + logo + titik lokasi Face ID tiap sekolah.
 * - Semua tabel data diberi kolom `sekolah_id`; model memakai trait BelongsToSekolah
 *   sehingga setiap akun HANYA melihat & mengubah data sekolahnya sendiri.
 * - Peran baru `super_admin` (sekolah_id kosong) yang mendaftarkan sekolah baru.
 * Data lama (kalau ada) otomatis dipindahkan ke satu sekolah bawaan.
 */
return new class extends Migration
{
    private array $tabelData = [
        'users', 'kelas', 'guru', 'siswa', 'mata_pelajaran', 'nilai', 'absensi', 'spp', 'aspirasi',
        'prestasi', 'bank_soal', 'jadwal_pelajaran', 'sesi_absensi_mapel', 'absensi_mapel', 'materi',
        'tugas', 'pengumpulan_tugas', 'api_klien', 'komite_pembayaran',
    ];

    public function up(): void
    {
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('npsn', 20)->nullable();
            $table->string('jenjang', 20)->default('SMA');
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('kepala_sekolah')->nullable();
            $table->string('nip_kepala_sekolah', 30)->nullable();
            $table->string('logo')->nullable();
            $table->decimal('latitude', 10, 7)->default(-6.2000000);
            $table->decimal('longitude', 10, 7)->default(106.8166667);
            $table->unsignedInteger('radius_meter')->default(200);
            $table->timestamps();
        });

        foreach ($this->tabelData as $tabel) {
            if (Schema::hasTable($tabel) && !Schema::hasColumn($tabel, 'sekolah_id')) {
                Schema::table($tabel, function (Blueprint $t) {
                    $t->unsignedBigInteger('sekolah_id')->nullable()->index();
                });
            }
        }

        // Pindahkan data lama (kalau ada) ke sekolah bawaan.
        $lama = Schema::hasTable('pengaturan_sekolah') ? DB::table('pengaturan_sekolah')->first() : null;
        if ($lama || DB::table('users')->exists()) {
            $id = DB::table('sekolah')->insertGetId([
                'nama' => $lama->nama_sekolah ?? 'Sekolah Pertama',
                'latitude' => $lama->latitude ?? -6.2,
                'longitude' => $lama->longitude ?? 106.8166667,
                'radius_meter' => $lama->radius_meter ?? 200,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            foreach ($this->tabelData as $tabel) {
                if (Schema::hasTable($tabel)) {
                    DB::table($tabel)->whereNull('sekolah_id')->update(['sekolah_id' => $id]);
                }
            }
        }

        // Kode mapel boleh sama di sekolah berbeda (mis. "MTK-01") -> unik PER sekolah.
        try {
            Schema::table('mata_pelajaran', fn (Blueprint $t) => $t->dropUnique('mata_pelajaran_kode_mapel_unique'));
        } catch (\Throwable $e) {
            // indeks sudah tidak ada: abaikan
        }
        Schema::table('mata_pelajaran', fn (Blueprint $t) => $t->unique(['sekolah_id', 'kode_mapel']));

        // Peran super_admin (MySQL/MariaDB: ubah ENUM; database lain memakai migrasi awal yang sudah diperbarui).
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','guru','siswa','wali_kelas') NOT NULL DEFAULT 'siswa'");
        }
    }

    public function down(): void
    {
        foreach ($this->tabelData as $tabel) {
            if (Schema::hasTable($tabel) && Schema::hasColumn($tabel, 'sekolah_id')) {
                Schema::table($tabel, fn (Blueprint $t) => $t->dropColumn('sekolah_id'));
            }
        }
        Schema::dropIfExists('sekolah');
    }
};
