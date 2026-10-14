<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\CabangKantor;
use App\Models\Absensi;
use App\Models\PengajuanIzin;
use App\Models\Lembur;
use Carbon\Carbon;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $hariIni = Carbon::now()->toDateString();
        $totalKaryawan = User::role('karyawan')->count();
        $totalCabang = CabangKantor::count();
        $pendingIzin = PengajuanIzin::where('status', 'Pending')->count();
        $pendingLembur = Lembur::where('status_pengajuan', 'Pending')->count();
        $totalPending = $pendingIzin + $pendingLembur;
        $hadirHariIni = Absensi::where('tanggal', $hariIni)->whereIn('status_masuk', ['Hadir', 'Terlambat'])->count();

        return [
            Stat::make('Total Karyawan', $totalKaryawan)
                ->description('Karyawan terdaftar aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Kantor Cabang', $totalCabang)
                ->description('Lokasi geofence GPS')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            Stat::make('Menunggu Persetujuan', $totalPending)
                ->description("{$pendingIzin} Izin, {$pendingLembur} Lembur")
                ->descriptionIcon('heroicon-m-clock')
                ->color($totalPending > 0 ? 'warning' : 'success'),

            Stat::make('Hadir Hari Ini', $hadirHariIni)
                ->description('Presensi tercatat hari ini')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
