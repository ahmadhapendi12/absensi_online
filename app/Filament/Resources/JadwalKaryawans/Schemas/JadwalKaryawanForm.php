<?php

namespace App\Filament\Resources\JadwalKaryawans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

use Illuminate\Database\Eloquent\Builder;

class JadwalKaryawanForm
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
                Select::make('shift_id')
                    ->label('Shift Kerja')
                    ->relationship('shift', 'nama_shift')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('tanggal_kerja')
                    ->label('Tanggal Kerja')
                    ->default(now())
                    ->required(),
            ]);
    }
}
