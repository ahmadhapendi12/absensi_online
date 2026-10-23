<?php

namespace App\Filament\Karyawan\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\JadwalKaryawan;
use App\Models\AgendaLiburToko;
use App\Models\Absensi;

class PresensiMandiri extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;

    protected static UnitEnum|string|null $navigationGroup = 'Menu Utama';

    protected static ?string $navigationLabel = 'Presensi Masuk / Pulang';

    protected static ?string $title = 'Presensi Kamera & GPS';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.karyawan.pages.presensi-mandiri';

    public function getViewData(): array
    {
        $user = Auth::user();
        $hariIni = Carbon::today()->toDateString();
        $jadwal = JadwalKaryawan::with('shift')->where('user_id', $user->id)->first();

        $dayMap = [
            'sunday' => 'minggu',
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => 'sabtu',
        ];
        $hariIniId = $dayMap[strtolower(Carbon::now()->format('l'))] ?? 'minggu';
        $isHariLibur = false;
        $pesanLibur = null;
        $tipeLibur = null;

        if ($jadwal && strtolower($jadwal->hari_libur_rutin) === $hariIniId) {
            $isHariLibur = true;
            $tipeLibur = 'rutin';
            $pesanLibur = 'Hari ini adalah jadwal libur rutin mingguan Anda (' . ucfirst($jadwal->hari_libur_rutin) . '). Selamat beristirahat!';
        }

        $agendaToko = AgendaLiburToko::where('tanggal', $hariIni)
            ->where('status', '!=', 'dibatalkan')
            ->where(function ($q) use ($user) {
                $q->whereNull('cabang_id')
                  ->orWhere('cabang_id', $user->cabang_id);
            })
            ->first();

        if ($agendaToko) {
            $isHariLibur = true;
            $tipeLibur = $agendaToko->jenis;
            $pesanLibur = trim("Hari ini agenda {$agendaToko->nama_agenda}. " . ($agendaToko->keterangan ?? ''));
        }

        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->first();

        $lemburHariIni = \App\Models\Lembur::where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->where('status_pengajuan', 'Disetujui')
            ->first();

        return [
            'user' => $user,
            'jadwal' => $jadwal,
            'shift' => $jadwal?->shift,
            'cabang' => $user->cabang,
            'isHariLibur' => $isHariLibur,
            'pesanLibur' => $pesanLibur,
            'tipeLibur' => $tipeLibur,
            'agendaToko' => $agendaToko,
            'absensi' => $absensiHariIni,
            'lemburHariIni' => $lemburHariIni,
        ];
    }

    public function submitPresensi(string $fotoBase64, string $koordinat, ?string $liveDescriptor = null): array
    {
        $service = app(\App\Services\BiometricFaceVerificationService::class);
        $result = $service->prosesPresensi(Auth::user(), $fotoBase64, $koordinat, $liveDescriptor);

        \Filament\Notifications\Notification::make()
            ->title($result['message'])
            ->success()
            ->send();

        return $result;
    }
}
