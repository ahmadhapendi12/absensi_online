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

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    public function getDescription(): string | Htmlable | null
    {
        return new HtmlString('
            <div class="fi-wi-chart-legend-box fi-legend-highlight" style="width: 100%; margin-top: 0.25rem; padding: 1rem 1.15rem; border-radius: 1rem; background: linear-gradient(135deg, #fffbeb 0%, #fef2f2 60%, #fff7ed 100%); border: 2px solid #f59e0b; font-size: 0.75rem; box-shadow: 0 0 18px rgba(245, 158, 11, 0.28), 0 4px 14px rgba(234, 88, 12, 0.12); position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #10b981 0%, #3b82f6 30%, #f59e0b 60%, #ef4444 100%);"></div>
                <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.6rem; font-weight: 700; color: #92400e;">
                    <span style="position: relative; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.25rem 0.7rem; border-radius: 0.5rem; font-size: 0.7rem; font-weight: 800; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; letter-spacing: 0.05em; box-shadow: 0 3px 10px rgba(16, 185, 129, 0.4); text-transform: uppercase;">
                        <span style="position: relative; display: inline-block; width: 8px; height: 8px;">
                            <span style="position: absolute; inset: 0; border-radius: 50%; background: #a7f3d0; animation: legendPulse 1.5s cubic-bezier(0, 0, 0.2, 1) infinite; opacity: 0.75;"></span>
                            <span style="position: relative; display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #a7f3d0;"></span>
                        </span>
                        LIVE DATA
                    </span>
                    <span style="font-size: 0.8rem;">Petunjuk Warna Status:</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; color: #7c2d12; font-weight: 600;">
                    <div style="display: flex; align-items: center; gap: 6px; padding: 0.2rem 0.4rem; border-radius: 0.4rem; background: rgba(16, 185, 129, 0.08);"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#10B981; box-shadow: 0 0 6px #10B981; flex-shrink:0;"></span>Hijau: Tepat Waktu</div>
                    <div style="display: flex; align-items: center; gap: 6px; padding: 0.2rem 0.4rem; border-radius: 0.4rem; background: rgba(245, 158, 11, 0.08);"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#F59E0B; box-shadow: 0 0 6px #F59E0B; flex-shrink:0;"></span>Kuning: Terlambat</div>
                    <div style="display: flex; align-items: center; gap: 6px; padding: 0.2rem 0.4rem; border-radius: 0.4rem; background: rgba(249, 115, 22, 0.08);"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#F97316; box-shadow: 0 0 6px #F97316; flex-shrink:0;"></span>Orange: Izin</div>
                    <div style="display: flex; align-items: center; gap: 6px; padding: 0.2rem 0.4rem; border-radius: 0.4rem; background: rgba(239, 68, 68, 0.08);"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#EF4444; box-shadow: 0 0 6px #EF4444; flex-shrink:0;"></span>Merah: Sakit</div>
                    <div style="grid-column: span 2; display: flex; align-items: center; gap: 6px; padding: 0.25rem 0.4rem; border-radius: 0.4rem; background: rgba(185, 28, 28, 0.06); border-top: 1px dashed rgba(185, 28, 28, 0.2); margin-top: 0.1rem; padding-top: 0.35rem;"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#B91C1C; box-shadow: 0 0 6px #B91C1C; flex-shrink:0;"></span>Merah Tua: Lembur Disetujui</div>
                </div>
            </div>
        ');
    }

    protected function getFilters(): ?array
    {
        $cabangs = \App\Models\CabangKantor::orderBy('nama_cabang')->pluck('nama_cabang', 'id')->toArray();

        return [
            '' => 'Semua Cabang',
            ...$cabangs,
        ];
    }

    protected function getData(): array
    {
        $absensiQuery = Absensi::query();
        $lemburQuery = Lembur::query();

        if (filled($this->filter)) {
            $absensiQuery->whereHas('user', function ($q) {
                $q->where('cabang_id', $this->filter);
            });

            $lemburQuery->whereHas('user', function ($q) {
                $q->where('cabang_id', $this->filter);
            });
        }

        $tepatWaktu = (clone $absensiQuery)->where('status_masuk', 'Tepat Waktu')->count();
        $terlambat = (clone $absensiQuery)->where('status_masuk', 'Terlambat')->count();
        $izin = (clone $absensiQuery)->where('status_masuk', 'Izin')->count();
        $sakit = (clone $absensiQuery)->where('status_masuk', 'Sakit')->count();
        $lembur = (clone $lemburQuery)->where('status_pengajuan', 'Disetujui')->count();

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
