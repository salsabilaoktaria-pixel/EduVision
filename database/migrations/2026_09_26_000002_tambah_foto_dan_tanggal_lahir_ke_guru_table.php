<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Menyamakan data Guru dengan data Siswa: sekarang guru juga bisa punya foto
// (bisa diupload & diklik untuk dilihat pop-up, sama seperti siswa) dan
// tanggal lahir, sama seperti yang sudah ada di form Data Siswa.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('alamat');
            $table->date('tanggal_lahir')->nullable()->after('jenis_kelamin');
        });
    }

    public function down(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->dropColumn(['foto', 'tanggal_lahir']);
        });
    }
};
