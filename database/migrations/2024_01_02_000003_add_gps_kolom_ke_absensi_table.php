<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->decimal('jarak_ke_sekolah_meter', 10, 2)->nullable()->after('longitude');
            $table->boolean('di_area_sekolah')->default(false)->after('jarak_ke_sekolah_meter');
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn(['jarak_ke_sekolah_meter', 'di_area_sekolah']);
        });
    }
};
