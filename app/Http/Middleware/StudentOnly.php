<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentOnly
{
    /**
     * Pastikan hanya role 'student' yang akses portal ini.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // ✅ FIX TEST 3: Cegah Guest (Belum login sama sekali)
        if (! $user) {
            return redirect('/login');
        }

        // Cegah Admin masuk ke portal siswa
        if ($user && ($user->hasRole('admin') || $user->hasRole('super-admin') || $user->hasRole('bendahara') || $user->hasRole('operator'))) {
            return redirect('/admin');
        }

        // Cegah user selain student (misal jika ada role lain)
        if ($user && ! $user->hasRole('student')) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk siswa.');
        }

        return $next($request);
    }
}