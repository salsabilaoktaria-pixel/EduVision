<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Membatasi akses halaman berdasarkan role user.
     * Daftarkan di bootstrap/app.php atau app/Http/Kernel.php sebagai 'role'.
     * Contoh pemakaian di route: ->middleware('role:admin,guru')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
