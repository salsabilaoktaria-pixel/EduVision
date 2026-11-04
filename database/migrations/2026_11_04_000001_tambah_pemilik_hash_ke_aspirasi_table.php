<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Aspirasi anonim tidak lagi menyimpan user_id. Kepemilikan (agar pengirim bisa melihat riwayatnya sendiri)
// disimpan sebagai sidik jari HMAC berkunci APP_KEY: isi database saja tidak cukup untuk mengetahui siapa pengirimnya.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aspirasi', function (Blueprint $table) {
            $table->string('pemilik_hash', 64)->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('aspirasi', function (Blueprint $table) {
            $table->dropColumn('pemilik_hash');
        });
    }
};
