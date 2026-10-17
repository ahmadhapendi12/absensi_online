<?php

namespace App\Filament\Karyawan\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilKaryawan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static UnitEnum|string|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Profil Saya';

    protected static ?string $title = 'Profil Karyawan';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.karyawan.pages.profil-karyawan';

    public ?string $telphone = null;
    public ?string $alamat = null;
    public ?string $password_baru = null;
    public ?string $konfirmasi_password = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->telphone = $user->profile?->telphone;
        $this->alamat = $user->profile?->alamat;
    }

    public function simpanProfil(): void
    {
        $user = Auth::user();

        $rules = [
            'telphone' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
        ];

        if (filled($this->password_baru)) {
            $rules['password_baru'] = 'min:6';
            $rules['konfirmasi_password'] = 'same:password_baru';
        }

        $this->validate($rules, [
            'password_baru.min' => 'Password baru minimal 6 karakter.',
            'konfirmasi_password.same' => 'Konfirmasi password tidak cocok dengan password baru.',
        ]);

        if ($user->profile) {
            $user->profile->update([
                'telphone' => $this->telphone,
                'alamat' => $this->alamat,
            ]);
        }

        if (filled($this->password_baru)) {
            $user->update([
                'password' => Hash::make($this->password_baru),
            ]);
            $this->password_baru = null;
            $this->konfirmasi_password = null;
        }

        Notification::make()
            ->title('Profil Berhasil Diperbarui!')
            ->success()
            ->send();
    }

    public function simpanFotoWajahMaster(string $fotoBase64): void
    {
        $user = Auth::user();

        if (empty($fotoBase64) || !str_contains($fotoBase64, 'base64,')) {
            Notification::make()
                ->title('Gagal Mengambil Foto')
                ->body('Format foto tidak valid. Silakan ulangi pengambilan foto.')
                ->danger()
                ->send();
            return;
        }

        $imageParts = explode(";base64,", $fotoBase64);
        $imageBase64 = base64_decode($imageParts[1]);

        $fileName = 'master_' . ($user->profile?->nik ?? $user->id) . '_' . time() . '.jpg';
        $path = 'wajah_acuan/' . $fileName;

        if ($user->profile?->foto_wajah_acuan) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile->foto_wajah_acuan);
        }

        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $imageBase64);

        if ($user->profile) {
            $user->profile->update([
                'foto_wajah_acuan' => $path,
            ]);
        }

        Notification::make()
            ->title('Foto Wajah Acuan Master Berhasil Terdaftar!')
            ->body('Foto wajah Anda berhasil disimpan sebagai identitas biometrik resmi untuk presensi.')
            ->success()
            ->send();
    }

    public function getViewData(): array
    {
        $user = Auth::user()->load(['cabang', 'profile', 'jadwal.shift']);

        return [
            'user' => $user,
            'cabang' => $user->cabang,
            'profile' => $user->profile,
            'shift' => $user->jadwal?->shift,
        ];
    }
}
