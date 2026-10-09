<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanIzin;
use App\Models\Lembur;
use App\Models\Absensi;
use App\Models\JadwalKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PengajuanController extends Controller
{
    public function index()
    {
        $izins = PengajuanIzin::with('user')->where('status', 'Pending')->orderBy('created_at', 'desc')->get();
        $lemburs = Lembur::with('user')->where('status_pengajuan', 'Pending')->orderBy('created_at', 'desc')->get();
        return view('superadmin.pengajuan', compact('izins', 'lemburs'));
    }

    public function approveIzin($id)
    {
        $izin = PengajuanIzin::findOrFail($id);
        $izin->update([
            'status' => 'Disetujui',
            'disetujui_oleh' => Auth::id()
        ]);

        // Auto-generate / Sync ke tabel Absensi untuk tanggal rentang izin
        $period = CarbonPeriod::create($izin->tanggal_mulai, $izin->tanggal_selesai);
        foreach ($period as $date) {
            $tgl = $date->toDateString();
            
            $jadwal = JadwalKaryawan::where('user_id', $izin->user_id)
                ->where('tanggal_kerja', $tgl)
                ->first();

            Absensi::updateOrCreate(
                [
                    'user_id' => $izin->user_id,
                    'tanggal' => $tgl,
                ],
                [
                    'jadwal_id' => $jadwal ? $jadwal->id : null,
                    'pengajuan_izin_id' => $izin->id,
                    'status_masuk' => $izin->jenis, // 'Sakit', 'Izin', atau 'Cuti'
                    'menit_terlambat' => 0,
                ]
            );
        }

        return back()->with('success', 'Pengajuan izin disetujui & otomatis dicatat di rekap absensi.');
    }

    public function rejectIzin($id)
    {
        $izin = PengajuanIzin::findOrFail($id);
        $izin->update([
            'status' => 'Ditolak',
            'disetujui_oleh' => Auth::id()
        ]);

        return back()->with('success', 'Pengajuan izin ditolak.');
    }

    public function approveLembur($id)
    {
        $lembur = Lembur::findOrFail($id);
        $lembur->update([
            'status_pengajuan' => 'Disetujui',
            'disetujui_oleh' => Auth::id()
        ]);

        return back()->with('success', 'Pengajuan lembur disetujui.');
    }

    public function rejectLembur($id)
    {
        $lembur = Lembur::findOrFail($id);
        $lembur->update([
            'status_pengajuan' => 'Ditolak',
            'disetujui_oleh' => Auth::id()
        ]);

        return back()->with('success', 'Pengajuan lembur ditolak.');
    }
}