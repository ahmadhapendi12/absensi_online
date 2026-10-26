<?php

namespace App\Filament\Resources\CabangKantors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CabangKantorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_cabang')
                    ->label('Nama Cabang Kantor')
                    ->placeholder('Contoh: Kantor Cabang Bandung')
                    ->required(),
                TextInput::make('latitude')
                    ->label('Latitude GPS')
                    ->placeholder('Contoh: -6.9175')
                    ->required(),
                TextInput::make('longitude')
                    ->label('Longitude GPS')
                    ->placeholder('Contoh: 107.6191')
                    ->required(),
                TextInput::make('radius_meter')
                    ->label('Radius Toleransi Absensi (Meter)')
                    ->helperText('Batas jarak maksimal karyawan boleh melakukan presensi.')
                    ->required()
                    ->numeric()
                    ->default(100),
                \Filament\Forms\Components\Toggle::make('is_24_jam')
                    ->label('Operasional Toko 24 Jam')
                    ->helperText('Aktifkan jika toko/cabang ini beroperasi non-stop 24 jam dengan rotasi shift penuh.')
                    ->default(false),
            ]);
    }
}
