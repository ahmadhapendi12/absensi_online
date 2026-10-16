<?php

namespace App\Filament\Karyawan\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Absensi;
use App\Models\PengajuanIzin;
use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class KaryawanStatistikWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // Total Hadir Tepat Waktu Bulan Ini
        $tepatWaktu = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status_masuk', 'Hadir')
            ->count();

        // Total Terlambat Bulan Ini
        $terlambat = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status_masuk', 'Terlambat')
            ->count();

        // Total Izin/Sakit/Cuti Bulan Ini
        $totalIzin = PengajuanIzin::where('user_id', $user->id)
            ->where('status', 'Disetujui')
            ->where(function ($q) use ($bulanIni, $tahunIni) {
                $q->whereMonth('tanggal_mulai', $bulanIni)
                  ->whereYear('tanggal_mulai', $tahunIni);
            })
            ->count();

        // Total Menit Lembur Bulan Ini
        $totalMenitLembur = Lembur::where('user_id', $user->id)
            ->where('status_pengajuan', 'Disetujui')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->sum('durasi_menit');

        $jamLembur = round($totalMenitLembur / 60, 1);

        return [
            Stat::make('Hadir Tepat Waktu', "{$tepatWaktu} Hari")
                ->description('Bulan ' . Carbon::now()->translatedFormat('F Y'))
                ->color('success'),

            Stat::make('Keterlambatan', "{$terlambat} Kali")
                ->description('Total terlambat bulan ini')
                ->color($terlambat > 0 ? 'warning' : 'gray'),

            Stat::make('Izin / Sakit Disetujui', "{$totalIzin} Kali")
                ->description('Pengajuan resmi bulan ini')
                ->color('info'),

            Stat::make('Total Jam Lembur', "{$jamLembur} Jam")
                ->description("{$totalMenitLembur} menit disetujui")
                ->color('primary'),
        ];
    }
}
