<?php

namespace App\Filament\Resources\MasterShifts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MasterShiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_shift')
                    ->label('Nama Shift')
                    ->placeholder('Contoh: Shift Pagi')
                    ->required(),
                TimePicker::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->required(),
                TimePicker::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->required(),
                TextInput::make('toleransi_terlambat_menit')
                    ->label('Toleransi Keterlambatan (Menit)')
                    ->helperText('Berapa menit toleransi sebelum dinyatakan terlambat.')
                    ->required()
                    ->numeric()
                    ->default(15),
                Toggle::make('lintas_hari')
                    ->label('Shift Lintas Hari (Melewati Tengah Malam)')
                    ->helperText('Aktifkan jika jam pulang shift berada di keesokan harinya (misal: 22:00 - 06:00).')
                    ->default(false),
            ]);
    }
}
