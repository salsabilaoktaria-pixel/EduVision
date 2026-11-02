<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Header keamanan standar untuk semua respons web. */
class HeaderKeamanan
{
    public function handle(Request $request, Closure $next)
    {
        $res = $next($request);
        $res->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $res->headers->set('X-Content-Type-Options', 'nosniff');
        $res->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $res->headers->set('Permissions-Policy', 'camera=(self), geolocation=(self), microphone=()');
        $res->headers->set('X-XSS-Protection', '0');
        if ($request->isSecure()) {
            $res->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        // Halaman berisi data pribadi tidak boleh disimpan cache browser / tombol Back setelah logout.
        if ($request->user()) {
            $res->headers->set('Cache-Control', 'no-store, private');
        }
        return $res;
    }
}
