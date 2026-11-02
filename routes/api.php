<?php

use App\Http\Controllers\Api\DataSiswaApiController;
use Illuminate\Support\Facades\Route;

// Semua rute di sini otomatis berawalan /api. Wajib kunci API (header X-API-Key), maks 60 permintaan/menit.
Route::prefix('v1')->middleware(['api.key', 'throttle:60,1'])->group(function () {
    Route::get('/kelas', [DataSiswaApiController::class, 'kelas']);
    Route::get('/siswa', [DataSiswaApiController::class, 'siswaIndex']);
    Route::get('/siswa/{nisn}', [DataSiswaApiController::class, 'siswaShow']);
    Route::get('/siswa/{nisn}/spp', [DataSiswaApiController::class, 'spp']);
    Route::get('/siswa/{nisn}/komite', [DataSiswaApiController::class, 'komiteIndex']);
    Route::post('/siswa/{nisn}/komite', [DataSiswaApiController::class, 'komiteSimpan']);
});
