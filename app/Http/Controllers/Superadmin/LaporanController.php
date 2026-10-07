<?php
namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\CabangKantor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $cabangs = CabangKantor::all();
        
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', Carbon::now()->endOfMonth()->toDateString());
        $cabangId = $request->input('cabang_id');

        $query = Absensi::with(['user.cabang', 'jadwal.shift'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tanggal', 'desc');

        if ($cabangId) {
            $query->whereHas('user', function($q) use ($cabangId) {
                $q->where('cabang_id', $cabangId);
            });
        }

        $dataAbsensi = $query->get();

        return view('superadmin.laporan', compact('dataAbsensi', 'cabangs', 'tanggalMulai', 'tanggalSelesai', 'cabangId'));
    }

    // 2. Fungsi untuk Export data ke PDF (berdasarkan filter tanggal)
    public function exportPdf(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', Carbon::now()->endOfMonth()->toDateString());
        $cabangId = $request->input('cabang_id');

        // Tarik data dari database
        $query = Absensi::with(['user.cabang', 'jadwal.shift'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tanggal', 'asc');
            
        if ($cabangId) {
            $query->whereHas('user', function($q) use ($cabangId) {
                $q->where('cabang_id', $cabangId);
            });
        }
        
        $dataAbsensi = $query->get();

        // Render ke file blade khusus PDF (nanti kita buat view-nya)
        $pdf = Pdf::loadView('superadmin.cetak-pdf', [
            'dataAbsensi' => $dataAbsensi,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai
        ]);

        // Download file PDF
        $namaFile = 'Rekap_Absensi_Lazatto_' . $tanggalMulai . '_sd_' . $tanggalSelesai . '.pdf';
        return $pdf->download($namaFile);
    }
}