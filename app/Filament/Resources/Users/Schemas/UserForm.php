<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                    ->description('Data identitas pribadi, NIK, kontak, dan alamat.')
                    ->relationship('profile')
                    ->schema([
                        TextInput::make('nik')
                            ->label('Nomor Induk Karyawan (NIK)')
                            ->placeholder('Contoh: 12345678')
                            ->required()
                            ->maxLength(30),
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
                        FileUpload::make('foto_ktp')
                            ->label('Foto KTP / Identitas')
                            ->image()
                            ->directory('ktp_karyawan')
                            ->disk('public'),
                    ])
                    ->columns(2),
            ]);
    }
}
