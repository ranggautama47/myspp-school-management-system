<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPanelAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Cek apakah user ada dan memiliki role admin/super-admin
        if ($user && ! ($user->hasRole('admin') || $user->hasRole('super-admin') || $user->hasRole('bendahara') || $user->hasRole('operator') )) {
            // Jika student/operator mencoba masuk admin panel, tendang ke /dashboard atau 403
            return redirect('/dashboard')->with('error', 'Akses ditolak. Anda bukan Admin.');
        }

        return $next($request);
    }
}