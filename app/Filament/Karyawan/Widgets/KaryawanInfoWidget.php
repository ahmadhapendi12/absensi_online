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

        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->first();

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
