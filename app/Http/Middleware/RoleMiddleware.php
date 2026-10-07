<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Jika user belum login atau rolenya tidak sesuai dengan rute, tolak aksesnya!
        if (!Auth::check() || Auth::user()->role !== $role) {
            abort(403, 'AKSES DITOLAK: Anda tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}