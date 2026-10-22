<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Carbon\Carbon;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun & Akses')
                    ->description('Data login dan penempatan cabang karyawan.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Masukkan nama karyawan')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(100),
                        Select::make('cabang_id')
                            ->label('Cabang Kantor')
                            ->relationship('cabang', 'nama_cabang')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('roles')
                            ->label('Peran / Hak Akses (Role)')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->label('Status Akun')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Nonaktif',
                            ])
                            ->default('active')
                            ->required(),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText('Biarkan kosong jika tidak ingin mengubah password.'),
                    ])
                    ->columns(2),

                Section::make('Biodata & Profil Karyawan')
                    ->description('Data identitas pribadi, NIK, kontak, dan alamat (opsional saat pendaftaran awal; karyawan dapat melengkapi mandiri).')
                    ->relationship('profile')
                    ->schema([
                        TextInput::make('nik')
                            ->label('Nomor Induk Karyawan (NIK)')
                            ->placeholder('Contoh: 12345678 (Bisa diisi mandiri oleh karyawan)')
                            ->nullable()
                            ->maxLength(30),
                        DatePicker::make('tanggal_bergabung')
                            ->label('Tanggal Mulai Bergabung / Masuk Kerja')
                            ->displayFormat('d/m/Y')
                            ->helperText('Tanggal resmi karyawan mulai bekerja di kios (menentukan awal mulai aktifnya jadwal shift di kalender).'),
                        TextInput::make('telphone')
                            ->label('Nomor HP / WhatsApp')
                            ->placeholder('Contoh: 081234567890')
                            ->tel()
                            ->maxLength(30),
                        Textarea::make('alamat')
                            ->label('Alamat Domisili')
                            ->placeholder('Alamat lengkap karyawan')
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('foto_wajah_acuan')
                            ->label('Foto Profil & Wajah Acuan (Face ID)')
                            ->image()
                            ->avatar()
                            ->directory('wajah_acuan')
                            ->disk('public'),

                        // =========================================================================================================
                        // [FITUR PRIVASI & ENKRIPSI KTP - BLIND VERIFICATION SUPERADMIN]
                        // CATATAN DEVELOPER: Fitur ini memastikan Superadmin TIDAK DAPAT melihat/menyalahgunakan foto KTP karyawan.
                        // Superadmin HANYA melihat tanda centang verifikasi apakah KTP sudah diupload atau belum.
                        // Berkas fisik KTP dienkripsi dengan standar enkripsi aman (UU PDP).
                        //
                        // JIKA KLIEN INGIN FITUR INI DIUBAH (SUPERADMIN BISA LIHAT & UPLOAD KTP LANGSUNG):
                        // 1. Comment komponen Placeholder di bawah ini.
                        // 2. Un-comment komponen FileUpload di bawahnya.
                        // =========================================================================================================
                        \Filament\Forms\Components\Placeholder::make('status_ktp_privasi')
                            ->label('Status Berkas KTP (Enkripsi & Privasi Aman)')
                            ->content(function ($record) {
                                $hasKtp = filled($record?->profile?->foto_ktp);
                                if ($hasKtp) {
                                    return new \Illuminate\Support\HtmlString('
                                        <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.75rem; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.8125rem; font-weight: 700;">
                                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>✓ KTP Sudah Diunggah & Terenkripsi Aman</span>
                                        </div>
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">
                                            Demi perlindungan privasi data pribadi, berkas fisik KTP dienkripsi dan tidak ditampilkan kepada admin.
                                        </div>
                                    ');
                                }
                                return new \Illuminate\Support\HtmlString('
                                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.75rem; border-radius: 0.5rem; background: rgba(156, 163, 175, 0.1); color: #6b7280; border: 1px solid rgba(156, 163, 175, 0.25); font-size: 0.8125rem; font-weight: 600;">
                                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <span>Belum Diunggah (Dapat dilengkapi mandiri oleh karyawan)</span>
                                    </div>
                                ');
                            }),

                        /*
                        // [OPSI REVERT JIKA KLIEN INGIN SUPERADMIN BISA UPLOAD/LIHAT KTP SECARA LANGSUNG]:
                        FileUpload::make('foto_ktp')
                            ->label('Foto KTP / Identitas')
                            ->image()
                            ->directory('ktp_karyawan')
                            ->disk('public'),
                        */
                        // =========================================================================================================
                        // [AKHIR KODE FITUR PRIVASI & ENKRIPSI KTP]
                        // =========================================================================================================
                    ])
                    ->columns(2),

                Section::make('Penetapan Shift & Jadwal Kerja')
                    ->description('Tentukan shift kerja aktif dan hari libur mingguan karyawan.')
                    ->relationship('jadwal')
                    ->schema([
                        Select::make('shift_id')
                            ->label('Shift Kerja')
                            ->relationship('shift', 'nama_shift')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama_shift} (" . Carbon::parse($record->jam_masuk)->format('H:i') . ' - ' . Carbon::parse($record->jam_pulang)->format('H:i') . ' WIB)')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Pilih shift jam kerja untuk karyawan ini.'),
                        Select::make('hari_libur_rutin')
                            ->label('Hari Libur Rutin Mingguan (Off)')
                            ->options([
                                'senin' => 'Senin',
                                'selasa' => 'Selasa',
                                'rabu' => 'Rabu',
                                'kamis' => 'Kamis',
                                'jumat' => 'Jumat',
                                'sabtu' => 'Sabtu',
                                'minggu' => 'Minggu',
                            ])
                            ->default('senin')
                            ->nullable()
                            ->helperText('Jatah libur 1 hari per minggu.'),
                    ])
                    ->columns(2),
            ]);
    }
}
