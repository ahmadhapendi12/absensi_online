<?php

namespace App\Filament\Resources\Absensis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AbsensiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
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
                    ]),

                Grid::make(2)
                    ->schema([
                        TimePicker::make('jam_masuk')
                            ->label('Jam Masuk (Format 24 Jam)')
                            ->prefixIcon('heroicon-o-clock')
                            ->format('H:i')
                            ->displayFormat('H:i')
                            ->seconds(false),
                        TimePicker::make('jam_pulang')
                            ->label('Jam Pulang (Format 24 Jam)')
                            ->prefixIcon('heroicon-o-clock')
                            ->format('H:i')
                            ->displayFormat('H:i')
                            ->seconds(false),
                    ]),

                Grid::make(2)
                    ->schema([
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
                            ->suffix(' menit')
                            ->required(),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextInput::make('koordinat_masuk')
                            ->label('Koordinat Masuk'),
                        TextInput::make('koordinat_pulang')
                            ->label('Koordinat Pulang'),
                    ]),
            ]);
    }
}
