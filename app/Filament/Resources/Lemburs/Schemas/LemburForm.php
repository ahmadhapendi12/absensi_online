<?php

namespace App\Filament\Resources\Lemburs\Schemas;

use App\Models\CabangKantor;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class LemburForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('cabang_id')
                            ->label('Cabang Toko / Kios')
                            ->options(CabangKantor::pluck('nama_cabang', 'id'))
                            ->live()
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($set, $record) {
                                if ($record && $record->user) {
                                    $set('cabang_id', $record->user->cabang_id);
                                }
                            })
                            ->afterStateUpdated(fn ($set) => $set('user_id', null))
                            ->placeholder('Pilih Cabang (Opsional)'),

                        Select::make('user_id')
                            ->label('Karyawan')
                            ->options(function (callable $get) {
                                $cabangId = $get('cabang_id');
                                $query = User::role('karyawan');
                                if ($cabangId) {
                                    $query->where('cabang_id', $cabangId);
                                }
                                return $query->with('cabang', 'profile')->get()->mapWithKeys(function ($u) {
                                    $cabang = $u->cabang ? " [{$u->cabang->nama_cabang}]" : '';
                                    $nik = $u->profile?->nik ? " - NIK: {$u->profile->nik}" : '';
                                    return [$u->id => "{$u->name}{$cabang}{$nik}"];
                                });
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $user = User::find($state);
                                    if ($user && $user->cabang_id) {
                                        $set('cabang_id', $user->cabang_id);
                                    }
                                }
                            }),
                    ]),

                DatePicker::make('tanggal')
                    ->label('Tanggal Lembur')
                    ->default(now())
                    ->required(),

                Grid::make(2)
                    ->schema([
                        TimePicker::make('jam_mulai_aktual')
                            ->label('Jam Mulai Lembur (Format 24 Jam)')
                            ->prefixIcon('heroicon-o-clock')
                            ->native(false)
                            ->format('H:i')
                            ->displayFormat('H:i')
                            ->seconds(false)
                            ->default('17:00')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $selesai = $get('jam_selesai_aktual');
                                if ($state && $selesai) {
                                    $start = \Carbon\Carbon::parse($state);
                                    $end = \Carbon\Carbon::parse($selesai);
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
                            ->native(false)
                            ->format('H:i')
                            ->displayFormat('H:i')
                            ->seconds(false)
                            ->default('19:00')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $mulai = $get('jam_mulai_aktual');
                                if ($mulai && $state) {
                                    $start = \Carbon\Carbon::parse($mulai);
                                    $end = \Carbon\Carbon::parse($state);
                                    if ($end->lessThan($start)) {
                                        $end->addDay();
                                    }
                                    $set('durasi_menit', $start->diffInMinutes($end));
                                }
                            })
                            ->required(),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextInput::make('durasi_menit')
                            ->label('Durasi Lembur (Menit)')
                            ->suffix(' menit')
                            ->numeric()
                            ->default(120)
                            ->helperText('Otomatis dihitung dari jam mulai & selesai lembur.')
                            ->required(),

                        Select::make('status_pengajuan')
                            ->label('Status Pengajuan')
                            ->options([
                                'Pending' => 'Pending',
                                'Disetujui' => 'Disetujui',
                                'Ditolak' => 'Ditolak',
                            ])
                            ->default('Pending')
                            ->required(),
                    ]),

                Select::make('disetujui_oleh')
                    ->label('Diproses / Disetujui Oleh')
                    ->relationship('penyetuju', 'name')
                    ->disabled()
                    ->placeholder('-'),

                Textarea::make('alasan_lembur')
                    ->label('Alasan / Pekerjaan Lembur')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
