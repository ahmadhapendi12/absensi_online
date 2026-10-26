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

use App\Models\MasterShift;

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

        // Agenda Toko (Informasi kegiatan seperti Jumsih, Kamsih, agenda kios - tidak memblokir presensi)
        $agendaToko = AgendaLiburToko::where('tanggal', $hariIni)
            ->where('status', '!=', 'dibatalkan')
            ->where(function ($q) use ($user) {
                $q->whereNull('cabang_id')
                  ->orWhere('cabang_id', $user->cabang_id);
            })
            ->first();

        // Shift yang tersedia untuk cabang karyawan (atau shift umum jika belum diikat per cabang)
        $availableShifts = MasterShift::where(function ($q) use ($user) {
                $q->where('cabang_id', $user->cabang_id)
                  ->orWhereNull('cabang_id');
            })
            ->orderBy('jam_masuk')
            ->get();

        // Sinkronisasi status Libur otomatis jika jam operasional shift telah berakhir dan belum ada presensi
        app(\App\Services\AbsensiAutoLiburService::class)->syncLibur($hariIni, $user);

        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->first();

        // Jika belum ada absensi hari ini, periksa sesi aktif dari kemarin yang belum pulang (misal shift malam / lintas hari)
        if (!$absensiHariIni) {
            $absensiHariIni = Absensi::where('user_id', $user->id)
                ->where('tanggal', Carbon::yesterday()->toDateString())
                ->whereNotNull('jam_masuk')
                ->whereNull('jam_pulang')
                ->first();
        }

        $lemburHariIni = \App\Models\Lembur::where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->where('status_pengajuan', 'Disetujui')
            ->first();

        $sedangBekerja = ($absensiHariIni && $absensiHariIni->jam_masuk && !$absensiHariIni->jam_pulang);

        return [
            'user' => $user,
            'jadwal' => $jadwal,
            'shift' => $jadwal?->shift,
            'cabang' => $user->cabang,
            'agendaToko' => $agendaToko,
            'availableShifts' => $availableShifts,
            'absensi' => $absensiHariIni,
            'sedangBekerja' => $sedangBekerja,
            'lemburHariIni' => $lemburHariIni,
        ];
    }

    public function pilihShift(string $shiftId): void
    {
        $user = Auth::user();

        // Validasi: Karyawan tidak boleh pindah shift jika sedang bekerja aktif (hari ini atau sesi kemarin)
        $sedangBekerja = Absensi::where('user_id', $user->id)
            ->whereIn('tanggal', [Carbon::today()->toDateString(), Carbon::yesterday()->toDateString()])
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->exists();

        if ($sedangBekerja) {
            \Filament\Notifications\Notification::make()
                ->title('Pindah Shift Ditolak')
                ->body('Anda sedang bekerja pada shift aktif. Pindah shift tidak diperbolehkan di tengah jam kerja.')
                ->warning()
                ->send();
            return;
        }

        $jadwalLama = JadwalKaryawan::where('user_id', $user->id)->first();
        if ($jadwalLama && $jadwalLama->shift_id === $shiftId) {
            return;
        }

        $shift = MasterShift::where('id', $shiftId)
            ->where(function ($q) use ($user) {
                $q->where('cabang_id', $user->cabang_id)
                  ->orWhereNull('cabang_id');
            })
            ->first();

        if (!$shift) {
            \Filament\Notifications\Notification::make()
                ->title('Shift tidak ditemukan atau tidak tersedia untuk cabang Anda.')
                ->danger()
                ->send();
            return;
        }

        JadwalKaryawan::updateOrCreate(
            ['user_id' => $user->id],
            ['shift_id' => $shift->id]
        );

        \Filament\Notifications\Notification::make()
            ->title("Shift berhasil diubah menjadi: {$shift->nama_shift}")
            ->success()
            ->send();
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
