<?php
namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', Carbon::now()->month);
        $tahun = (int) $request->input('tahun', Carbon::now()->year);
        $status = $request->input('status', 'semua');

        $query = Absensi::where('user_id', Auth::id())
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal', 'desc');

        if ($status && $status !== 'semua') {
            $query->where('status_masuk', $status);
        }

        $riwayat = $query->get();

        return view('karyawan.riwayat', compact('riwayat', 'bulan', 'tahun', 'status'));
    }
}