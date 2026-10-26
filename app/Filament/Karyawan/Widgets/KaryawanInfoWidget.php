<?php

namespace App\Filament\Karyawan\Widgets;

use Filament\Widgets\Widget;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class KaryawanInfoWidget extends Widget
{
    protected string $view = 'filament.karyawan.widgets.karyawan-info-widget';

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public function getViewData(): array
    {
        $user = Auth::user();
        $hariIni = Carbon::today()->toDateString();

        // Sinkronisasi status Libur otomatis jika jam operasional shift telah berakhir
        app(\App\Services\AbsensiAutoLiburService::class)->syncLibur($hariIni, $user);

        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->first();

        if (!$absensiHariIni) {
            $absensiHariIni = Absensi::where('user_id', $user->id)
                ->where('tanggal', Carbon::yesterday()->toDateString())
                ->whereNotNull('jam_masuk')
                ->whereNull('jam_pulang')
                ->first();
        }

        $jadwal = $user->jadwal;
        $shift = $jadwal?->shift;

        return [
            'user' => $user,
            'absensi' => $absensiHariIni,
            'shift' => $shift,
            'hariIniFormatted' => Carbon::now()->translatedFormat('l, d F Y'),
        ];
    }
}
