<?php

namespace App\Filament\Karyawan\Resources\Lemburs\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
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
                                TimePicker::make('jam_mulai_aktual')
                                    ->label('Jam Mulai Lembur (Format 24 Jam)')
                                    ->prefixIcon('heroicon-o-clock')
                                    ->format('H:i')
                                    ->displayFormat('H:i')
                                    ->seconds(false)
                                    ->default('17:00')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $selesai = $get('jam_selesai_aktual');
                                        if ($state && $selesai) {
                                            $start = Carbon::parse($state);
                                            $end = Carbon::parse($selesai);
                                            if ($end->lessThan($start)) {
                                                $end->addDay();
                                            }
                                            $set('durasi_menit', $start->diffInMinutes($end));
                                        }
                                    })
                                    ->required(),

                                TimePicker::make('jam_selesai_aktual')
                                    ->label('Jam Selesai Lembur (Format 24 Jam)')
                                    ->prefixIcon('heroicon-o-clock')
                                    ->format('H:i')
                                    ->displayFormat('H:i')
                                    ->seconds(false)
                                    ->default('19:00')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $mulai = $get('jam_mulai_aktual');
                                        if ($mulai && $state) {
                                            $start = Carbon::parse($mulai);
                                            $end = Carbon::parse($state);
                                            if ($end->lessThan($start)) {
                                                $end->addDay();
                                            }
                                            $set('durasi_menit', $start->diffInMinutes($end));
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
