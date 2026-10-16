<?php

namespace App\Filament\Resources\AgendaLiburTokos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AgendaLiburTokoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_agenda')
                    ->label('Nama Agenda / Libur')
                    ->placeholder('Contoh: Jumsih (Jumat Bersih), Kamsih, atau Libur Idul Fitri')
                    ->required()
                    ->maxLength(255),

                DatePicker::make('tanggal')
                    ->label('Tanggal Pelaksanaan')
                    ->default(now())
                    ->required(),

                Select::make('jenis')
                    ->label('Jenis Agenda / Libur')
                    ->options([
                        'jumsih' => 'Jumsih (Jumat Bersih Toko)',
                        'kamsih' => 'Kamsih (Kamis Bersih Toko)',
                        'libur_kios' => 'Libur Kios / Cabang Tutup',
                        'libur_nasional' => 'Libur Nasional / Hari Raya',
                        'lainnya' => 'Agenda Lainnya',
                    ])
                    ->default('jumsih')
                    ->required(),

                Select::make('cabang_id')
                    ->label('Berlaku Untuk Cabang')
                    ->relationship('cabang', 'nama_cabang')
                    ->placeholder('Semua Cabang (Berlaku Seluruh Outlet)')
                    ->searchable()
                    ->preload()
                    ->helperText('Kosongkan jika libur/agenda ini berlaku untuk semua cabang.'),

                Select::make('status')
                    ->label('Status Keterlaksanaan')
                    ->options([
                        'pending' => 'Belum Terlaksana / Terjadwal',
                        'berlangsung' => 'Sedang Berlangsung (Hari Ini)',
                        'selesai' => 'Sudah Terlaksana / Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->default('pending')
                    ->required(),

                Textarea::make('keterangan')
                    ->label('Keterangan Tambahan')
                    ->placeholder('Catatan teknis, pembagian tugas kerja bakti, dsb.')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
