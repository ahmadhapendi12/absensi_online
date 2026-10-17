<?php

namespace App\Filament\Karyawan\Resources\PengajuanIzins\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PengajuanIzinKaryawanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Formulir Pengajuan Izin / Cuti')
                    ->description('Silakan isi formulir di bawah ini dengan lengkap untuk mengajukan izin atau cuti.')
                    ->schema([
                        Select::make('jenis')
                            ->label('Jenis Izin')
                            ->options([
                                'Sakit' => 'Sakit (Surat Dokter)',
                                'Izin' => 'Izin Kepentingan Pribadi/Keluarga',
                                'Cuti' => 'Cuti Tahunan / Khusus',
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
                            ->placeholder('Jelaskan alasan pengajuan izin secara rinci...')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),

                        FileUpload::make('file_bukti')
                            ->label('Dokumen Bukti Pendukung / Surat Dokter (Opsional)')
                            ->directory('pengajuan_izin')
                            ->disk('public')
                            ->image()
                            ->columnSpanFull()
                            ->helperText('Unggah foto bukti keterangan surat dokter atau berkas pendukung.'),
                    ]),
            ]);
    }
}
