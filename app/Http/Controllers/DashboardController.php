<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Jika yang login adalah Superadmin
        if ($user->role === 'superadmin') {
            // Arahkan ke tampilan dasbor superadmin (sesuaikan jika nama rutenya berbeda)
            return view('superadmin.dashboard'); 
        } 
        
        // 2. Jika yang login adalah Karyawan
        elseif ($user->role === 'karyawan') {
            // Langsung arahkan ke halaman kamera absensi
            return redirect()->route('karyawan.presensi.create');
        }

        // 3. Keamanan tambahan: Jika role tidak valid, paksa logout
        Auth::logout();
        return redirect('/login')->withErrors(['login' => 'Akun Anda tidak memiliki akses yang sah.']);
    }
}