<?php
namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        $riwayat = Absensi::where('user_id', Auth::id())
                          ->orderBy('tanggal', 'desc')
                          ->get();
                          
        return view('karyawan.riwayat', compact('riwayat'));
    }
}