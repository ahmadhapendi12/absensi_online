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
        if ($user->hasAnyRole(['superadmin', 'super_admin'])) {
            $hariIni = Carbon::now()->toDateString();

            // Hitung data statistik
            $totalKaryawan = User::role('karyawan')->count();
            $totalCabang = CabangKantor::count();
            $pengajuanPending = PengajuanIzin::where('status', 'Pending')->count() 
                              + Lembur::where('status_pengajuan', 'Pending')->count();
            $hadirHariIni = Absensi::where('tanggal', $hariIni)->count();

            $listCabang = CabangKantor::all();

            return view('superadmin.dashboard', compact(
                'totalKaryawan', 
                'totalCabang', 
                'pengajuanPending', 
                'hadirHariIni', 
                'listCabang'
            )); 
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