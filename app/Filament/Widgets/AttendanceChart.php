<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class AttendanceChart extends ChartWidget
{
    protected ?string $heading = 'Pola Jam Kedatangan (Bulan Ini)';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;

    public function getDescription(): string | Htmlable | null
    {
        return new HtmlString('
            <div class="fi-wi-chart-legend-box fi-legend-highlight" style="width: 100%; margin-top: 0.25rem; padding: 1rem 1.15rem; border-radius: 1rem; background: linear-gradient(135deg, #fffbeb 0%, #fef2f2 60%, #fff7ed 100%); border: 2px solid #f59e0b; font-size: 0.75rem; box-shadow: 0 0 18px rgba(245, 158, 11, 0.28), 0 4px 14px rgba(234, 88, 12, 0.12); position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #f59e0b 0%, #ea580c 50%, #dc2626 100%);"></div>
                <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.6rem; font-weight: 700; color: #92400e;">
                    <span style="position: relative; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.25rem 0.7rem; border-radius: 0.5rem; font-size: 0.7rem; font-weight: 800; background: linear-gradient(135deg, #f59e0b 0%, #ea580c 50%, #dc2626 100%); color: #ffffff; letter-spacing: 0.05em; box-shadow: 0 3px 10px rgba(245, 158, 11, 0.4); text-transform: uppercase;">
                        <span style="position: relative; display: inline-block; width: 8px; height: 8px;">
                            <span style="position: absolute; inset: 0; border-radius: 50%; background: #fef08a; animation: legendPulse 1.5s cubic-bezier(0, 0, 0.2, 1) infinite; opacity: 0.75;"></span>
                            <span style="position: relative; display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #fef08a;"></span>
                        </span>
                        SHIFT TIMING
                    </span>
                    <span style="font-size: 0.8rem;">Petunjuk Pola Kedatangan:</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; color: #7c2d12; font-weight: 600;">
                    <div style="display: flex; align-items: center; gap: 6px; padding: 0.2rem 0.4rem; border-radius: 0.4rem; background: rgba(16, 185, 129, 0.08);"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#10B981; box-shadow: 0 0 6px #10B981; flex-shrink:0;"></span>Awal (&gt;30 mnt)</div>
                    <div style="display: flex; align-items: center; gap: 6px; padding: 0.2rem 0.4rem; border-radius: 0.4rem; background: rgba(59, 130, 246, 0.08);"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#3B82F6; box-shadow: 0 0 6px #3B82F6; flex-shrink:0;"></span>Normal (10-30 mnt)</div>
                    <div style="display: flex; align-items: center; gap: 6px; padding: 0.2rem 0.4rem; border-radius: 0.4rem; background: rgba(234, 179, 8, 0.08);"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#EAB308; box-shadow: 0 0 6px #EAB308; flex-shrink:0;"></span>Mepet (&lt;10 mnt)</div>
                    <div style="display: flex; align-items: center; gap: 6px; padding: 0.2rem 0.4rem; border-radius: 0.4rem; background: rgba(239, 68, 68, 0.08);"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#EF4444; box-shadow: 0 0 6px #EF4444; flex-shrink:0;"></span>Terlambat</div>
                    <div style="grid-column: span 2; display: flex; align-items: center; gap: 6px; padding: 0.25rem 0.4rem; border-radius: 0.4rem; background: rgba(107, 114, 128, 0.06); border-top: 1px dashed rgba(107, 114, 128, 0.3); margin-top: 0.1rem; padding-top: 0.35rem;"><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#6B7280; box-shadow: 0 0 6px #6B7280; flex-shrink:0;"></span>Shift 1 (06:00) | Shift 2 (14:00) | Shift 3 (22:00)</div>
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
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $query = Absensi::with(['jadwal.shift', 'user.jadwal.shift'])
            ->whereBetween('tanggal', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->whereNotNull('jam_masuk');

        if (filled($this->filter)) {
            $query->whereHas('user', function ($q) {
                $q->where('cabang_id', $this->filter);
            });
        }

        $attendances = $query->get();

        $awal = 0;
        $normal = 0;
        $mepet = 0;
        $terlambat = 0;

        foreach ($attendances as $absen) {
            // Ambil info shift dari relasi jadwal atau fallback
            $shift = $absen->jadwal?->shift ?? $absen->user?->jadwal?->shift;
            $namaShift = strtolower($shift?->nama_shift ?? '');

            // Logika dinamis penentuan jam target shift:
            // Shift 1 = 06:00:00, Shift 2 = 14:00:00, Shift 3 = 22:00:00
            if (str_contains($namaShift, '1') || str_contains($namaShift, 'pagi')) {
                $targetTimeString = '06:00:00';
            } elseif (str_contains($namaShift, '2') || str_contains($namaShift, 'sore') || str_contains($namaShift, 'siang')) {
                $targetTimeString = '14:00:00';
            } elseif (str_contains($namaShift, '3') || str_contains($namaShift, 'malam')) {
                $targetTimeString = '22:00:00';
            } else {
                $targetTimeString = $shift?->jam_masuk ?? '08:00:00';
            }

            // Jika status tercatat Terlambat atau menit_terlambat > 0
            if ($absen->status_masuk === 'Terlambat' || ($absen->menit_terlambat && $absen->menit_terlambat > 0)) {
                $terlambat++;
                continue;
            }

            $targetTime = Carbon::createFromTimeString($targetTimeString);
            $jamMasuk = Carbon::createFromTimeString($absen->jam_masuk);

            // Datang melewati jam target shift
            if ($jamMasuk->greaterThan($targetTime)) {
                $terlambat++;
            } else {
                // Selisih menit kedatangan sebelum jam target shift
                $diffInMinutes = $jamMasuk->diffInMinutes($targetTime, false);

                if ($diffInMinutes > 30) {
                    $awal++; // >30 menit sebelum jam target
                } elseif ($diffInMinutes >= 10) {
                    $normal++; // 10-30 menit sebelum jam target
                } else {
                    $mepet++; // <10 menit sebelum jam target hingga tepat jam target
                }
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kehadiran',
                    'data' => [$awal, $normal, $mepet, $terlambat],
                    'backgroundColor' => [
                        '#10b981', // Hijau - Awal (>30 menit sebelum shift)
                        '#3b82f6', // Biru - Normal (10-30 menit sebelum shift)
                        '#eab308', // Kuning - Mepet (<10 menit sebelum shift)
                        '#ef4444', // Merah - Terlambat
                    ],
                ],
            ],
            'labels' => [
                'Awal (>30 mnt)',
                'Normal (10-30 mnt)',
                'Mepet (<10 mnt)',
                'Terlambat',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
