<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CabangKantor;   // Pastikan model Cabang sudah ada
use App\Models\Absensi;  // Pastikan model Absensi sudah ada
use App\Models\Lembur;   // Pastikan model Lembur sudah ada
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Jika yang login adalah Superadmin
        if ($user->role === 'superadmin') {
            $hariIni = Carbon::now()->toDateString();

            // Hitung data untuk 4 Kartu Statistik
            $totalKaryawan = User::where('role', 'karyawan')->count();
            $totalCabang = CabangKantor::count();
            $pengajuanPending = 0; 
            $hadirHariIni = Absensi::where('tanggal', $hariIni)->count();

            // Ambil data untuk daftar Lokasi Cabang Aktif
            $listCabang = CabangKantor::all();

            // Lempar data (compact) ke view dashboard superadmin
            return view('superadmin.dashboard', compact(
                'totalKaryawan', 
                'totalCabang', 
                'pengajuanPending', 
                'hadirHariIni', 
                'listCabang'
            )); 
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