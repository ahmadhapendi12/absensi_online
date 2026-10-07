<?php
namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    // 1. Fungsi untuk menampilkan data di halaman Web Superadmin
    public function index(Request $request)
    {
        // Default menampilkan data bulan ini
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $dataAbsensi = Absensi::with(['user', 'jadwal.shift'])
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('superadmin.laporan', compact('dataAbsensi'));
    }

    // 2. Fungsi untuk Export data ke PDF (berdasarkan filter tanggal)
    public function exportPdf(Request $request)
    {
        // Tangkap input filter dari user, jika kosong gunakan default awal & akhir bulan ini
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', Carbon::now()->endOfMonth()->toDateString());

        // Tarik data dari database
        $dataAbsensi = Absensi::with(['user', 'jadwal.shift'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tanggal', 'asc')
            ->get();

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