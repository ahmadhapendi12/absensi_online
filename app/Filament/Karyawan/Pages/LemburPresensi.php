<?php

namespace App\Filament\Karyawan\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;
use Illuminate\Support\Facades\Auth;
use App\Models\Lembur;

class LemburPresensi extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;

    protected static UnitEnum|string|null $navigationGroup = 'Menu Utama';

    protected static ?string $navigationLabel = 'Presensi Lembur';

    protected static ?string $title = 'Presensi Kamera Lembur';

    protected static ?string $slug = 'lembur-presensi';

    protected string $view = 'filament.karyawan.pages.lembur-presensi';

    public ?string $lembur_id = null;
    public ?Lembur $lembur = null;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->lembur_id = request()->query('id') ?? request()->query('lembur_id');
        if ($this->lembur_id) {
            $this->lembur = Lembur::with(['user.profile', 'user.cabang'])
                ->where('id', $this->lembur_id)
                ->where('user_id', Auth::id())
                ->first();
        }
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        $isSudahMulai = !empty($this->lembur?->foto_mulai);
        $isSudahSelesai = !empty($this->lembur?->foto_selesai);

        $hariIni = \Carbon\Carbon::today()->toDateString();
        $jamSekarang = \Carbon\Carbon::now()->format('H:i');
        $jamMulaiJadwal = $this->lembur?->jam_mulai_aktual ? substr($this->lembur->jam_mulai_aktual, 0, 5) : null;

        // Validasi waktu buka presensi masuk: wajib tepat atau setelah jam mulai jadwal pengajuan
        $isBelumBukaTanggal = (!$isSudahMulai && $this->lembur && $hariIni < $this->lembur->tanggal);
        $isBelumBukaJam = (!$isSudahMulai && $this->lembur && $hariIni === $this->lembur->tanggal && $jamMulaiJadwal && $jamSekarang < $jamMulaiJadwal);
        $isBelumBolehMasuk = ($isBelumBukaTanggal || $isBelumBukaJam);

        $lemburUrl = \App\Filament\Karyawan\Resources\Lemburs\LemburKaryawanResource::getUrl('index', panel: 'karyawan');

        return [
            'user' => $user,
            'lembur' => $this->lembur,
            'cabang' => $user->cabang,
            'isSudahMulai' => $isSudahMulai,
            'isSudahSelesai' => $isSudahSelesai,
            'jamMulaiJadwal' => $jamMulaiJadwal,
            'isBelumBukaTanggal' => $isBelumBukaTanggal,
            'isBelumBukaJam' => $isBelumBukaJam,
            'isBelumBolehMasuk' => $isBelumBolehMasuk,
            'lemburUrl' => $lemburUrl,
        ];
    }
}
