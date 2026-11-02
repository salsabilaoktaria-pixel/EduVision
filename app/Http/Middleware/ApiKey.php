<?php

namespace App\Http\Middleware;

use App\Models\ApiKlien;
use Closure;
use Illuminate\Http\Request;

/** Memeriksa kunci API dari header "X-API-Key" atau "Authorization: Bearer <kunci>". */
class ApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $kunci = $request->header('X-API-Key') ?: $request->bearerToken();
        $klien = $kunci ? ApiKlien::withoutGlobalScopes()->where('kunci_hash', ApiKlien::hash($kunci))->where('aktif', true)->first() : null;

        if (!$klien) {
            return response()->json(['sukses' => false, 'pesan' => 'Kunci API tidak valid atau dinonaktifkan.'], 401);
        }

        if (!$klien->terakhir_dipakai || $klien->terakhir_dipakai->lt(now()->subMinute())) {
            $klien->forceFill(['terakhir_dipakai' => now()])->save();
        }
        $request->attributes->set('api_klien', $klien);
        // Seluruh data yang dibaca/ditulis lewat API otomatis dibatasi ke sekolah pemilik kunci.
        \App\Support\Tenant::paksa($klien->sekolah_id);

        return $next($request);
    }
}
