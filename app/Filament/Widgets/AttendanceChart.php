<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class AttendanceChart extends ChartWidget
{
    protected ?string $heading = 'Trend Kehadiran (7 Hari Terakhir)';

    protected static ?int $sort = 2;

    public function getDescription(): string | Htmlable | null
    {
        return new HtmlString('
            <div style="margin-top: 0.65rem; padding: 0.75rem 1rem; border-radius: 0.85rem; background: linear-gradient(135deg, #fff7ed 0%, #fef2f2 100%); border: 1px solid #fed7aa; display: flex; flex-wrap: wrap; gap: 1.25rem; font-size: 0.75rem; font-weight: 600; color: #7c2d12; box-shadow: 0 2px 8px rgba(234, 88, 12, 0.08);">
                <span style="display: inline-flex; align-items: center; gap: 0.4rem;">
                    <span style="width: 14px; height: 4px; border-radius: 9999px; background: #10B981; box-shadow: 0 0 4px #10B981;"></span>
                    <strong style="color: #065f46;">HIJAU:</strong> Tepat Waktu
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.4rem;">
                    <span style="width: 14px; height: 4px; border-radius: 9999px; background: #F59E0B; box-shadow: 0 0 4px #F59E0B;"></span>
                    <strong style="color: #92400e;">KUNING:</strong> Terlambat
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.4rem;">
                    <span style="width: 14px; height: 4px; border-radius: 9999px; background: #EF4444; box-shadow: 0 0 4px #EF4444;"></span>
                    <strong style="color: #991b1b;">MERAH:</strong> Izin / Sakit / Cuti
                </span>
            </div>
        ');
    }

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
                    'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Terlambat',
                    'data' => $terlambat,
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.12)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Izin / Sakit / Cuti',
                    'data' => $izin,
                    'borderColor' => '#EF4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.12)',
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
