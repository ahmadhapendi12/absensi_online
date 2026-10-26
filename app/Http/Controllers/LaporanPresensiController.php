<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\CabangKantor;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanPresensiController extends Controller
{
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        
        // Parameter Filter
        $tipePeriode = $request->input('tipe_periode', 'bulan'); // 'hari_ini', 'bulan', 'tahun', 'rentang'
        $bulan = (int) $request->input('bulan', Carbon::now()->month);
        $tahun = (int) $request->input('tahun', Carbon::now()->year);
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $cabangId = $request->input('cabang_id');
        $statusMasuk = $request->input('status_masuk'); // 'semua' or specific status
        $userId = $request->input('user_id');

        // Jika yang request karyawan biasa, kunci user_id ke dirinya sendiri
        if ($user && $user->hasRole('karyawan')) {
            $userId = $user->id;
        }

        // Tentukan Rentang Tanggal berdasarkan Tipe Periode
        $labelPeriode = '';
        if ($tipePeriode === 'hari_ini') {
            $startDate = Carbon::today()->toDateString();
            $endDate = Carbon::today()->toDateString();
            $labelPeriode = Carbon::today()->translatedFormat('d F Y');
        } elseif ($tipePeriode === 'tahun') {
            $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfYear()->toDateString();
            $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfYear()->toDateString();
            $labelPeriode = "Tahun {$tahun}";
        } elseif ($tipePeriode === 'rentang' && $tanggalMulai && $tanggalSelesai) {
            $startDate = Carbon::parse($tanggalMulai)->toDateString();
            $endDate = Carbon::parse($tanggalSelesai)->toDateString();
            $labelPeriode = Carbon::parse($startDate)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($endDate)->translatedFormat('d M Y');
        } else {
            // Default: 'bulan'
            $date = Carbon::createFromDate($tahun, $bulan, 1);
            $startDate = $date->copy()->startOfMonth()->toDateString();
            $endDate = $date->copy()->endOfMonth()->toDateString();
            $labelPeriode = $date->translatedFormat('F Y');
        }

        // Query Absensi
        $query = Absensi::with(['user.profile', 'user.cabang', 'jadwal.shift'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_masuk', 'asc');

        if ($cabangId && $cabangId !== 'semua') {
            $query->whereHas('user', fn ($q) => $q->where('cabang_id', $cabangId));
            $cabang = CabangKantor::find($cabangId);
            $labelCabang = $cabang?->nama_cabang ?? 'Cabang Khusus';
        } else {
            $labelCabang = 'Seluruh Cabang';
        }

        if ($userId && $userId !== 'semua') {
            $query->where('user_id', $userId);
            $filterUser = User::find($userId);
            $labelKaryawan = $filterUser?->name ?? 'Karyawan';
        } else {
            $labelKaryawan = 'Semua Karyawan';
        }

        if ($statusMasuk && $statusMasuk !== 'semua') {
            $query->where('status_masuk', $statusMasuk);
            $labelStatus = $statusMasuk;
        } else {
            $labelStatus = 'Semua Status';
        }

        $dataAbsensi = $query->get();

        // Hitung Ringkasan Statistik
        $totalRecords = $dataAbsensi->count();
        $totalTepatWaktu = $dataAbsensi->where('status_masuk', 'Tepat Waktu')->count();
        $totalTerlambat = $dataAbsensi->where('status_masuk', 'Terlambat')->count();
        $totalIzin = $dataAbsensi->where('status_masuk', 'Izin')->count();
        $totalSakit = $dataAbsensi->where('status_masuk', 'Sakit')->count();
        $totalCuti = $dataAbsensi->where('status_masuk', 'Cuti')->count();
        $totalLibur = $dataAbsensi->where('status_masuk', 'Libur')->count();
        $totalHadir = $totalTepatWaktu + $totalTerlambat;
        $totalMenitTerlambat = $dataAbsensi->sum('menit_terlambat');
        $persenKehadiran = $totalRecords > 0 ? round(($totalHadir / $totalRecords) * 100, 1) : 0;

        $stats = [
            'total' => $totalRecords,
            'hadir' => $totalHadir,
            'tepat_waktu' => $totalTepatWaktu,
            'terlambat' => $totalTerlambat,
            'izin' => $totalIzin,
            'sakit' => $totalSakit,
            'cuti' => $totalCuti,
            'libur' => $totalLibur,
            'menit_terlambat' => $totalMenitTerlambat,
            'persen_kehadiran' => $persenKehadiran,
        ];

        // Cetak PDF
        $pdf = Pdf::loadView('pdf.laporan-rekap-presensi', compact(
            'dataAbsensi',
            'stats',
            'labelPeriode',
            'labelCabang',
            'labelKaryawan',
            'labelStatus',
            'startDate',
            'endDate'
        ))->setPaper('a4', 'landscape');

        $filename = 'Rekap_Presensi_Lazatto_' . str_replace(' ', '_', $labelPeriode) . '.pdf';

        if ($request->has('preview')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
