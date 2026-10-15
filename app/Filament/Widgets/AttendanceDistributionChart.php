<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Absensi;
use App\Models\Lembur;

class AttendanceDistributionChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Rekap Kehadiran';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $tepatWaktu = Absensi::where('status_masuk', 'Tepat Waktu')->count();
        $terlambat = Absensi::where('status_masuk', 'Terlambat')->count();
        $izin = Absensi::where('status_masuk', 'Izin')->count();
        $sakit = Absensi::where('status_masuk', 'Sakit')->count();
        $lembur = Lembur::where('status_pengajuan', 'Disetujui')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => [$tepatWaktu, $terlambat, $izin, $sakit, $lembur],
                    'backgroundColor' => [
                        '#10B981', // Hijau - Tepat Waktu
                        '#F59E0B', // Kuning - Terlambat
                        '#3B82F6', // Biru - Izin
                        '#8B5CF6', // Ungu - Sakit
                        '#EC4899', // Pink - Lembur Disetujui
                    ],
                ],
            ],
            'labels' => ['Tepat Waktu', 'Terlambat', 'Izin', 'Sakit', 'Lembur Disetujui'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
