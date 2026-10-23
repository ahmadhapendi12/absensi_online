<?php

namespace App\Filament\Resources\MasterShifts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MasterShiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama Shift')
                    ->description('Tentukan nama shift dan jam kerja reguler.')
                    ->schema([
                        TextInput::make('nama_shift')
                            ->label('Nama Shift')
                            ->placeholder('Contoh: Shift Pagi Kios')
                            ->columnSpanFull()
                            ->required(),

                        Grid::make(2)
                            ->schema([
                                TimePicker::make('jam_masuk')
                                    ->label('Jam Masuk Kerja')
                                    ->prefixIcon('heroicon-o-clock')
                                    ->native(false)
                                    ->seconds(false)
                                    ->format('H:i')
                                    ->displayFormat('H:i')
                                    ->default('08:00')
                                    ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 5) : '08:00')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $pulang = $get('jam_pulang');
                                        if ($state && $pulang) {
                                            $masukClean = substr($state, 0, 5);
                                            $pulangClean = substr($pulang, 0, 5);
                                            $set('lintas_hari', $pulangClean < $masukClean);
                                        }
                                    })
                                    ->required(),

                                TimePicker::make('jam_pulang')
                                    ->label('Jam Pulang Kerja')
                                    ->prefixIcon('heroicon-o-clock')
                                    ->native(false)
                                    ->seconds(false)
                                    ->format('H:i')
                                    ->displayFormat('H:i')
                                    ->default('16:00')
                                    ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 5) : '16:00')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $masuk = $get('jam_masuk');
                                        if ($masuk && $state) {
                                            $masukClean = substr($masuk, 0, 5);
                                            $pulangClean = substr($state, 0, 5);
                                            $set('lintas_hari', $pulangClean < $masukClean);
                                        }
                                    })
                                    ->required(),
                            ]),
                    ]),

                Section::make('Aturan Presensi & Batas Waktu')
                    ->description('Atur jendela batas waktu presensi masuk dan keluar untuk mencegah kecurangan.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('buka_absen_menit')
                                    ->label('Waktu Buka Presensi Masuk')
                                    ->options([
                                        15 => '15 Menit Sebelum Shift',
                                        30 => '30 Menit Sebelum Shift (Standar)',
                                        45 => '45 Menit Sebelum Shift',
                                        60 => '60 Menit (1 Jam) Sebelum Shift',
                                    ])
                                    ->default(30)
                                    ->required()
                                    ->helperText('Tombol presensi masuk baru bisa diklik sesuai waktu ini.'),

                                Select::make('toleransi_terlambat_menit')
                                    ->label('Batas Toleransi Keterlambatan')
                                    ->options([
                                        0 => 'Tepat Waktu (0 Menit)',
                                        5 => '5 Menit Toleransi',
                                        10 => '10 Menit Toleransi',
                                        15 => '15 Menit Toleransi (Standar)',
                                        30 => '30 Menit Toleransi',
                                    ])
                                    ->default(15)
                                    ->required()
                                    ->helperText('Lewat dari batas ini status kehadiran menjadi Terlambat.'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('toleransi_pulang_menit')
                                    ->label('Toleransi Pulang Lebih Awal')
                                    ->options([
                                        0 => 'Wajib Tepat Jam Pulang (0 Menit)',
                                        5 => 'Boleh Pulang 5 Menit Lebih Awal',
                                        10 => 'Boleh Pulang 10 Menit Lebih Awal',
                                        15 => 'Boleh Pulang 15 Menit Lebih Awal',
                                    ])
                                    ->default(0)
                                    ->required()
                                    ->helperText('Mencegah karyawan kabur atau absen sebelum shift berakhir.'),

                                Select::make('batas_akhir_pulang_menit')
                                    ->label('Batas Akhir Absen Pulang (Closing)')
                                    ->options([
                                        60 => 'Maksimal 1 Jam Setelah Shift',
                                        120 => 'Maksimal 2 Jam Setelah Shift (Standar Closing)',
                                        180 => 'Maksimal 3 Jam Setelah Shift',
                                        240 => 'Maksimal 4 Jam Setelah Shift',
                                    ])
                                    ->default(120)
                                    ->required()
                                    ->helperText('Lewat dari batas ini karyawan dinyatakan "Tidak Absen Pulang".'),
                            ]),

                        Toggle::make('lintas_hari')
                            ->label('Shift Lintas Hari (Melewati Tengah Malam)')
                            ->helperText('Otomatis aktif jika jam pulang lebih kecil dari jam masuk (melewati 00:00).')
                            ->live()
                            ->default(false),
                    ]),
            ]);
    }
}
