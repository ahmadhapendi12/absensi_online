<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Gabungkan list role yang diizinkan (dukung role superadmin & super_admin)
        $allowedRoles = [];
        foreach ($roles as $r) {
            $allowedRoles = array_merge($allowedRoles, explode('|', $r));
        }

        if (in_array('superadmin', $allowedRoles) || in_array('super_admin', $allowedRoles)) {
            $allowedRoles[] = 'superadmin';
            $allowedRoles[] = 'super_admin';
        }

        // Cek via Spatie Permission HasRoles
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole($allowedRoles)) {
            return $next($request);
        }

        // Fallback cek atribut role pada model
        if (isset($user->role) && in_array($user->role, $allowedRoles)) {
            return $next($request);
        }

        abort(403, 'AKSES DITOLAK: Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}