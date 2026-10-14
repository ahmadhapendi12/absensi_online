<?php

namespace App\Filament\Resources\Absensis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AbsensiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name', fn (Builder $query) => $query->role('karyawan'))
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('tanggal')
                    ->label('Tanggal Presensi')
                    ->default(now())
                    ->required(),
                TimePicker::make('jam_masuk')
                    ->label('Jam Masuk'),
                TimePicker::make('jam_pulang')
                    ->label('Jam Pulang'),
                Select::make('status_masuk')
                    ->label('Status Kehadiran')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Terlambat' => 'Terlambat',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                        'Cuti' => 'Cuti',
                        'Alpha' => 'Alpha',
                    ])
                    ->required(),
                TextInput::make('menit_terlambat')
                    ->label('Keterlambatan (Menit)')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('koordinat_masuk')
                    ->label('Koordinat Masuk'),
                TextInput::make('koordinat_pulang')
                    ->label('Koordinat Pulang'),
            ]);
    }
}
