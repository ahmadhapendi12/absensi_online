<?php

namespace App\Filament\Karyawan\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilKaryawan extends Page
{
    use WithFileUploads;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static UnitEnum|string|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Profil Saya';

    protected static ?string $title = 'Profil Karyawan';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.karyawan.pages.profil-karyawan';

    public ?string $nik = null;
    public ?string $telphone = null;
    public ?string $alamat = null;
    public ?string $password_baru = null;
    public ?string $konfirmasi_password = null;
    public $berkas_ktp = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->nik = $user->profile?->nik;
        $this->telphone = $user->profile?->telphone;
        $this->alamat = $user->profile?->alamat;
    }

    public function simpanProfil(): void
    {
        $user = Auth::user();

        $rules = [
            'nik' => 'nullable|string|max:30|unique:user_profiles,nik,' . ($user->profile?->id ?? 'NULL'),
            'telphone' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'berkas_ktp' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ];

        if (filled($this->password_baru)) {
            $rules['password_baru'] = 'min:6';
            $rules['konfirmasi_password'] = 'same:password_baru';
        }

        $this->validate($rules, [
            'nik.unique' => 'NIK ini sudah terdaftar oleh karyawan lain.',
            'berkas_ktp.max' => 'Ukuran berkas KTP maksimal 5MB.',
            'berkas_ktp.mimes' => 'Format KTP harus berupa gambar (JPG, PNG, WEBP) atau PDF.',
            'password_baru.min' => 'Password baru minimal 6 karakter.',
            'konfirmasi_password.same' => 'Konfirmasi password tidak cocok dengan password baru.',
        ]);

        $updateData = [
            'telphone' => $this->telphone,
            'alamat' => $this->alamat,
        ];

        if (filled($this->nik)) {
            $updateData['nik'] = $this->nik;
        }

        // =========================================================================================================
        // [FITUR PRIVASI & ENKRIPSI KTP - PROSES ENKRIPSI PENYIMPANAN]
        // Berkas KTP karyawan dienkripsi menggunakan AES-256 (Crypt::encrypt) sebelum ditulis ke disk penyimpanan.
        // Berkas disimpan di private storage lokal, BUKAN di public storage, sehingga tidak dapat diakses publik.
        // Superadmin pun hanya dapat melihat tanda centang verifikasi status tanpa dapat melihat foto KTP asli.
        // =========================================================================================================
        if ($this->berkas_ktp) {
            $rawContent = file_get_contents($this->berkas_ktp->getRealPath());
            $encryptedContent = Crypt::encrypt($rawContent);
            $extension = $this->berkas_ktp->getClientOriginalExtension() ?: 'bin';
            $secureFileName = 'ktp_terenkripsi/' . $user->id . '_' . time() . '.' . $extension . '.enc';

            Storage::disk('local')->put($secureFileName, $encryptedContent);

            // Hapus berkas KTP lama jika ada
            if ($user->profile?->foto_ktp && Storage::disk('local')->exists($user->profile->foto_ktp)) {
                Storage::disk('local')->delete($user->profile->foto_ktp);
            }

            $updateData['foto_ktp'] = $secureFileName;
            $this->berkas_ktp = null;
        }
        // =========================================================================================================
        // [AKHIR FITUR PRIVASI & ENKRIPSI KTP]
        // =========================================================================================================

        if ($user->profile) {
            $user->profile->update($updateData);
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

    public function simpanFotoWajahMaster(string $fotoBase64, ?string $descriptorJson = null): void
    {
        $user = Auth::user();

        if ($user->profile?->is_biometric_locked) {
            Notification::make()
                ->title('Data Wajah Sudah Terdaftar')
                ->body('Data biometrik Anda telah tersimpan. Hubungi administrator apabila memerlukan perubahan data.')
                ->warning()
                ->send();
            return;
        }

        if (empty($fotoBase64) || !str_contains($fotoBase64, 'base64,')) {
            Notification::make()
                ->title('Gagal Memproses Foto')
                ->body('Format foto tidak valid. Silakan lakukan pengambilan foto kembali.')
                ->danger()
                ->send();
            return;
        }

        $vector = json_decode($descriptorJson ?? '', true);
        if (!is_array($vector) || count($vector) !== 128) {
            Notification::make()
                ->title('Wajah Tidak Terdeteksi')
                ->body('Kamera tidak mendeteksi wajah dengan jelas. Pastikan seluruh wajah Anda terlihat tegak di depan kamera dan ulangi pengambilan foto.')
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
                'wajah_terverifikasi_at' => now(),
                'is_biometric_locked' => true,
                'face_descriptor' => $descriptorJson,
                'catatan_biometrik' => 'Perekaman mandiri awal via portal karyawan',
            ]);
        }

        Notification::make()
            ->title('Perekaman Wajah Berhasil')
            ->body('Data biometrik wajah Anda berhasil didaftarkan.')
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
