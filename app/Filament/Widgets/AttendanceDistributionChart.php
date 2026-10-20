<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Absensi;
use App\Models\Lembur;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class AttendanceDistributionChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Rekap Kehadiran';

    protected static ?int $sort = 3;

    public function getDescription(): string | Htmlable | null
    {
        return new HtmlString('
            <div style="margin-top: 0.65rem; padding: 0.85rem 1rem; border-radius: 0.85rem; background: linear-gradient(135deg, #fff7ed 0%, #fef2f2 100%); border: 1px solid #fed7aa; font-size: 0.75rem; box-shadow: 0 2px 8px rgba(234, 88, 12, 0.08);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; font-weight: 700; color: #9a3412;">
                    <span style="padding: 0.15rem 0.55rem; border-radius: 0.35rem; font-size: 0.65rem; font-weight: 800; background: linear-gradient(135deg, #ea580c, #dc2626); color: #ffffff; letter-spacing: 0.04em; box-shadow: 0 2px 6px rgba(220, 38, 38, 0.3);">LIVE DATA</span>
                    <span>Petunjuk Warna Status:</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.4rem; color: #7c2d12; font-weight: 600;">
                    <div><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#10B981; margin-right:6px; box-shadow: 0 0 4px #10B981;"></span>Hijau: Tepat Waktu</div>
                    <div><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#F59E0B; margin-right:6px; box-shadow: 0 0 4px #F59E0B;"></span>Kuning: Terlambat</div>
                    <div><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#F97316; margin-right:6px; box-shadow: 0 0 4px #F97316;"></span>Orange: Izin</div>
                    <div><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#EF4444; margin-right:6px; box-shadow: 0 0 4px #EF4444;"></span>Merah: Sakit</div>
                    <div style="grid-column: span 2;"><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#B91C1C; margin-right:6px; box-shadow: 0 0 4px #B91C1C;"></span>Merah Tua: Lembur Disetujui</div>
                </div>
            </div>
        ');
    }

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
                        '#F97316', // Orange - Izin
                        '#EF4444', // Merah Lazatto - Sakit
                        '#B91C1C', // Merah Gelap - Lembur Disetujui
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
