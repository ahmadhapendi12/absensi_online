<?php

namespace App\Filament\Karyawan\Resources\PengajuanIzins\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class PengajuanIzinKaryawanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jenis')
                    ->label('Jenis Izin')
                    ->options([
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Cuti' => 'Cuti',
                    ])
                    ->required(),

                Grid::make(2)
                    ->schema([
                        DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->default(now())
                            ->required(),
                        DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->default(now())
                            ->required(),
                    ]),

                Textarea::make('alasan')
                    ->label('Alasan / Keterangan')
                    ->placeholder('Jelaskan alasan izin secara rinci...')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('file_bukti')
                    ->label('File Bukti / Surat Dokter (Opsional)')
                    ->directory('pengajuan_izin')
                    ->disk('public')
                    ->image()
                    ->columnSpanFull()
                    ->helperText('Unggah foto surat dokter atau dokumen pendukung jika sakit/keperluan khusus.'),
            ]);
    }
}
