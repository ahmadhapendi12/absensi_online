<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Absensi;
use Carbon\Carbon;

class AttendanceChart extends ChartWidget
{
    protected ?string $heading = 'Trend Kehadiran (7 Hari Terakhir)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(Carbon::today()->subDays($i)->toDateString());
        }

        $tepatWaktu = [];
        $terlambat = [];
        $izin = [];

        foreach ($dates as $date) {
            $tepatWaktu[] = Absensi::where('tanggal', $date)->where('status_masuk', 'Tepat Waktu')->count();
            $terlambat[] = Absensi::where('tanggal', $date)->where('status_masuk', 'Terlambat')->count();
            $izin[] = Absensi::where('tanggal', $date)->whereIn('status_masuk', ['Izin', 'Sakit', 'Cuti'])->count();
        }

        $labels = $dates->map(fn ($d) => Carbon::parse($d)->translatedFormat('d M'))->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Tepat Waktu',
                    'data' => $tepatWaktu,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Terlambat',
                    'data' => $terlambat,
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Izin / Sakit / Cuti',
                    'data' => $izin,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
