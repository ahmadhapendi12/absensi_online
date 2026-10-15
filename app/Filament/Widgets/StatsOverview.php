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
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $hariIni = Carbon::now()->toDateString();
        $totalKaryawan = User::role('karyawan')->count();
        $totalCabang = CabangKantor::count();
        $pendingIzin = PengajuanIzin::where('status', 'Pending')->count();
        $pendingLembur = Lembur::where('status_pengajuan', 'Pending')->count();
        $totalPending = $pendingIzin + $pendingLembur;
        
        $hadirHariIni = Absensi::where('tanggal', $hariIni)
            ->whereIn('status_masuk', ['Hadir', 'Tepat Waktu', 'Terlambat'])
            ->count();

        $terlambatHariIni = Absensi::where('tanggal', $hariIni)
            ->where('status_masuk', 'Terlambat')
            ->count();

        // 7-day sparkline attendance
        $attendanceTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i)->toDateString();
            $attendanceTrend[] = Absensi::where('tanggal', $tgl)
                ->whereIn('status_masuk', ['Hadir', 'Tepat Waktu', 'Terlambat'])
                ->count();
        }

        return [
            Stat::make('Total Karyawan Aktif', $totalKaryawan)
                ->description('Terdaftar di seluruh cabang')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([$totalKaryawan, $totalKaryawan, $totalKaryawan, $totalKaryawan, $totalKaryawan])
                ->color('primary'),

            Stat::make('Hadir Hari Ini', $hadirHariIni)
                ->description("Dari total {$totalKaryawan} karyawan")
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart($attendanceTrend)
                ->color('success'),

            Stat::make('Terlambat Hari Ini', $terlambatHariIni)
                ->description($terlambatHariIni > 0 ? 'Perlu evaluasi shift' : 'Semua hadir tepat waktu')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($terlambatHariIni > 0 ? 'warning' : 'gray'),

            Stat::make('Menunggu Persetujuan', $totalPending)
                ->description("{$pendingIzin} Izin/Sakit, {$pendingLembur} Lembur")
                ->descriptionIcon('heroicon-m-clock')
                ->color($totalPending > 0 ? 'danger' : 'success'),
        ];
    }
}
