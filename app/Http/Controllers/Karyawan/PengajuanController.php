<?php
namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\PengajuanIzin;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    public function index()
    {
        return view('karyawan.pengajuan'); 
    }

    public function storeIzin(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:Sakit,Izin,Cuti',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'alasan' => 'required'
        ]);

        PengajuanIzin::create([
            'user_id' => Auth::id(),
            'jenis' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'status' => 'Pending'
        ]);

        return back()->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function lembur()
    {
        return view('karyawan.lembur');
    }

    public function storeLembur(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'alasan_lembur' => 'required|string',
            'jam_mulai_aktual' => 'required',
            'jam_selesai_aktual' => 'required',
        ]);

        Lembur::create([
            'user_id' => Auth::id(),
            'tanggal' => $request->tanggal,
            'alasan_lembur' => $request->alasan_lembur,
            'jam_mulai_aktual' => $request->jam_mulai_aktual,
            'jam_selesai_aktual' => $request->jam_selesai_aktual,
            'status_pengajuan' => 'Pending'
        ]);

        return back()->with('success', 'Pengajuan lembur berhasil dikirim.');
    }
}