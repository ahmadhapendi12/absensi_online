<?php

namespace App\Filament\Karyawan\Resources\Lemburs\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LemburKaryawanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Formulir Pengajuan Lembur')
                    ->description('Catat tanggal dan jam pelaksanaan lembur sesuai penugasan kerja.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('tanggal')
                                    ->label('Tanggal Lembur')
                                    ->default(now())
                                    ->required(),

                                TextInput::make('durasi_menit')
                                    ->label('Durasi Lembur (Menit)')
                                    ->suffix(' menit')
                                    ->numeric()
                                    ->default(120)
                                    ->helperText('Otomatis dihitung dari selisih jam mulai & jam selesai.')
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                        TextInput::make('jam_mulai_aktual')
                            ->label('Jam Mulai Lembur')
                            ->placeholder('17:00')
                            ->mask('99:99')
                            ->prefixIcon('heroicon-o-clock')
                            ->default('17:00')
                            ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 5) : null)
                            ->dehydrateStateUsing(fn ($state) => $state ? substr($state, 0, 5) : null)
                            ->regex('/^([01][0-9]|2[0-3]):[0-5][0-9]$/')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $selesai = $get('jam_selesai_aktual');
                                if ($state && $selesai && strlen($state) === 5 && strlen($selesai) === 5) {
                                    $start = Carbon::parse($state);
                                    $end = Carbon::parse($selesai);
                                    if ($end->lessThan($start)) {
                                        $end->addDay();
                                    }
                                    $set('durasi_menit', (int) $start->diffInMinutes($end));
                                }
                            })
                            ->required(),

                        TextInput::make('jam_selesai_aktual')
                            ->label('Jam Selesai Lembur')
                            ->placeholder('19:00')
                            ->mask('99:99')
                            ->prefixIcon('heroicon-o-clock')
                            ->default('19:00')
                            ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 5) : null)
                            ->dehydrateStateUsing(fn ($state) => $state ? substr($state, 0, 5) : null)
                            ->regex('/^([01][0-9]|2[0-3]):[0-5][0-9]$/')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $mulai = $get('jam_mulai_aktual');
                                if ($mulai && $state && strlen($mulai) === 5 && strlen($state) === 5) {
                                    $start = Carbon::parse($mulai);
                                    $end = Carbon::parse($state);
                                    if ($end->lessThan($start)) {
                                        $end->addDay();
                                    }
                                    $set('durasi_menit', (int) $start->diffInMinutes($end));
                                }
                            })
                            ->required(),
                            ]),

                        Textarea::make('tugas_lembur')
                            ->label('Uraian Tugas / Alasan Lembur')
                            ->placeholder('Jelaskan rincian pekerjaan atau instruksi kerja yang diselesaikan selama lembur...')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
