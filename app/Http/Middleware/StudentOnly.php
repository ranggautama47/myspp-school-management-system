<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentOnly
{
    /**
     * Pastikan hanya role 'student' yang akses portal ini.
     * Admin yang coba akses /dashboard → redirect ke /admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ($user->hasRole('admin') || $user->hasRole('super-admin'))) {
            return redirect('/admin');
        }

        if ($user && ! $user->hasRole('student')) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk siswa.');
        }

        return $next($request);
    }
}
