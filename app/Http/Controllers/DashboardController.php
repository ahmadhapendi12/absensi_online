<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CabangKantor;
use App\Models\Absensi;
use App\Models\Lembur;
use App\Models\PengajuanIzin;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Jika yang login adalah Superadmin
        if ($user->hasRole('superadmin')) {
            return redirect('/admin');
        } 
        
        // 2. Jika yang login adalah Karyawan
        elseif ($user->hasRole('karyawan')) {
            return redirect()->route('karyawan.presensi.create');
        }

        // 3. Jika role tidak valid, paksa logout
        Auth::logout();
        return redirect('/login')->withErrors(['login' => 'Akun Anda tidak memiliki akses yang sah.']);
    }
}