<?php

namespace App\Filament\Karyawan\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;
use Illuminate\Support\Facades\Auth;
use App\Models\MasterShift;
use App\Models\JadwalKaryawan;
use App\Models\RiwayatJadwalKaryawan;
use Filament\Notifications\Notification;

use App\Models\Absensi;
use Carbon\Carbon;

class PilihShift extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static UnitEnum|string|null $navigationGroup = 'Menu Utama';

    protected static ?string $navigationLabel = 'Pilihan Shift Kerja';

    protected static ?string $title = 'Pilihan Shift Kerja Toko';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.karyawan.pages.pilih-shift';

    public function getViewData(): array
    {
        $user = Auth::user();
        $cabang = $user->cabang;

        $jadwal = JadwalKaryawan::with('shift')->where('user_id', $user->id)->first();
        $activeShiftId = $jadwal?->shift_id;

        // Cek apakah karyawan sedang bekerja (sudah absen masuk, belum absen pulang hari ini atau kemarin)
        $sedangBekerja = Absensi::where('user_id', $user->id)
            ->whereIn('tanggal', [Carbon::today()->toDateString(), Carbon::yesterday()->toDateString()])
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->exists();

        // Shift yang tersedia khusus untuk cabang karyawan atau shift global
        $shifts = MasterShift::with('cabang')
            ->where(function ($q) use ($user) {
                $q->where('cabang_id', $user->cabang_id)
                  ->orWhereNull('cabang_id');
            })
            ->orderBy('jam_masuk')
            ->get();

        return [
            'user' => $user,
            'cabang' => $cabang,
            'jadwal' => $jadwal,
            'activeShift' => $jadwal?->shift,
            'activeShiftId' => $activeShiftId,
            'sedangBekerja' => $sedangBekerja,
            'shifts' => $shifts,
        ];
    }

    public function pilihShift(string $shiftId): void
    {
        $user = Auth::user();

        // 1. Validasi: Karyawan tidak boleh pindah shift jika sedang bekerja aktif
        $sedangBekerja = Absensi::where('user_id', $user->id)
            ->whereIn('tanggal', [Carbon::today()->toDateString(), Carbon::yesterday()->toDateString()])
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->exists();

        if ($sedangBekerja) {
            Notification::make()
                ->title('Pindah Shift Ditolak')
                ->body('Anda sedang bekerja pada shift aktif. Pindah shift tidak diperbolehkan di tengah jam kerja. Silakan lakukan presensi pulang terlebih dahulu.')
                ->warning()
                ->send();
            return;
        }

        // 2. Mencegah notifikasi berulang jika shift yang diklik adalah shift yang sudah aktif
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
            Notification::make()
                ->title('Shift Tidak Ditemukan')
                ->body('Shift tersebut tidak tersedia untuk cabang penempatan Anda.')
                ->danger()
                ->send();
            return;
        }

        JadwalKaryawan::updateOrCreate(
            ['user_id' => $user->id],
            [
                'shift_id' => $shift->id,
            ]
        );

        Notification::make()
            ->title('Shift Berhasil Dipilih!')
            ->body("Shift kerja Anda saat ini adalah {$shift->nama_shift} (" . substr($shift->jam_masuk, 0, 5) . " - " . substr($shift->jam_pulang, 0, 5) . " WIB).")
            ->success()
            ->send();
    }
}
