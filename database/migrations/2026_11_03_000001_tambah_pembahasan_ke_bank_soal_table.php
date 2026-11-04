<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Penjelasan/pembahasan soal (opsional) yang tampil ke siswa setelah menjawab latihan soal.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_soal', function (Blueprint $table) {
            $table->text('pembahasan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bank_soal', function (Blueprint $table) {
            $table->dropColumn('pembahasan');
        });
    }
};
