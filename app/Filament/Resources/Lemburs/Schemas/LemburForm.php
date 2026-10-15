<?php

namespace App\Filament\Resources\Lemburs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class LemburForm
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
                    ->label('Tanggal Lembur')
                    ->default(now())
                    ->required(),
                TimePicker::make('jam_mulai_aktual')
                    ->label('Jam Mulai Lembur')
                    ->prefixIcon('heroicon-o-clock')
                    ->seconds(false)
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
                    ->label('Jam Selesai Lembur')
                    ->prefixIcon('heroicon-o-clock')
                    ->seconds(false)
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
                TextInput::make('durasi_menit')
                    ->label('Durasi Lembur (Menit)')
                    ->suffix(' menit')
                    ->numeric()
                    ->default(60)
                    ->helperText('Otomatis dihitung dari jam mulai & selesai.')
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
