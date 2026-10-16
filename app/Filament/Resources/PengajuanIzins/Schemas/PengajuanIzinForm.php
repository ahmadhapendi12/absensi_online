<?php

namespace App\Filament\Resources\PengajuanIzins\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class PengajuanIzinForm
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
                        Select::make('jenis')
                            ->label('Jenis Izin')
                            ->options([
                                'Sakit' => 'Sakit',
                                'Izin' => 'Izin',
                                'Cuti' => 'Cuti',
                            ])
                            ->required(),
                    ]),

                Grid::make(2)
                    ->schema([
                        DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required(),
                        DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->required(),
                    ]),

                Grid::make(2)
                    ->schema([
                        Select::make('status')
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
                    ]),

                Textarea::make('alasan')
                    ->label('Alasan / Keterangan')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('file_bukti')
                    ->label('File Bukti / Surat Dokter (Opsional)')
                    ->directory('pengajuan_izin')
                    ->disk('public')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
