<?php
namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanIzin;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    public function index()
    {
        $izins = PengajuanIzin::with('user')->where('status', 'Pending')->get();
        $lemburs = Lembur::with('user')->where('status_pengajuan', 'Pending')->get();
        return view('superadmin.pengajuan', compact('izins', 'lemburs'));
    }

    public function approveIzin($id)
    {
        $izin = PengajuanIzin::findOrFail($id);
        $izin->update(['status' => 'Disetujui', 'disetujui_oleh' => Auth::id()]);
        return back()->with('success', 'Izin disetujui.');
    }

    public function approveLembur($id)
    {
        $lembur = Lembur::findOrFail($id);
        $lembur->update(['status_pengajuan' => 'Disetujui', 'disetujui_oleh' => Auth::id()]);
        return back()->with('success', 'Lembur disetujui.');
    }
}