<?php

namespace App\Filament\Karyawan\Resources\Lemburs\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\FileUpload;
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
                                    ->label('Jam Mulai Lembur')
                                    ->prefixIcon('heroicon-o-clock')
                                    ->native(false)
                                    ->seconds(false)
                                    ->format('H:i')
                                    ->displayFormat('H:i')
                                    ->default('17:00')
                                    ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 5) : '17:00')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $selesai = $get('jam_selesai_aktual');
                                        if ($state && $selesai) {
                                            $start = Carbon::parse($state);
                                            $end = Carbon::parse($selesai);
                                            if ($end->lessThan($start)) { $end->addDay(); }
                                            $set('durasi_menit', (int) $start->diffInMinutes($end));
                                        }
                                    })
                                    ->required(),

                                TimePicker::make('jam_selesai_aktual')
                                    ->label('Jam Selesai Lembur')
                                    ->prefixIcon('heroicon-o-clock')
                                    ->native(false)
                                    ->seconds(false)
                                    ->format('H:i')
                                    ->displayFormat('H:i')
                                    ->default('19:00')
                                    ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 5) : '19:00')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $mulai = $get('jam_mulai_aktual');
                                        if ($mulai && $state) {
                                            $start = Carbon::parse($mulai);
                                            $end = Carbon::parse($state);
                                            if ($end->lessThan($start)) { $end->addDay(); }
                                            $set('durasi_menit', (int) $start->diffInMinutes($end));
                                        }
                                    })
                                    ->required(),
                            ]),

                        Textarea::make('alasan_lembur')
                            ->label('Uraian Tugas / Alasan Lembur')
                            ->placeholder('Jelaskan rincian pekerjaan atau instruksi kerja yang diselesaikan selama lembur...')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Dokumentasi Presensi Lembur (Masuk & Keluar)')
                    ->description('Foto bukti selfie kehadiran & waktu pelaksanaan lembur aktual di outlet.')
                    ->visible(fn ($record) => filled($record))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('preview_foto_masuk')
                                    ->label('Foto Masuk Lembur')
                                    ->content(function ($record) {
                                        if ($record && $record->foto_mulai) {
                                            $url = asset('storage/' . $record->foto_mulai);
                                            $jam = $record->jam_mulai_aktual ? Carbon::parse($record->jam_mulai_aktual)->format('H:i') . ' WIB' : '-';
                                            return new \Illuminate\Support\HtmlString("
                                                <div style='border: 1.5px solid rgba(16, 185, 129, 0.3); border-radius: 0.75rem; padding: 0.75rem; background: rgba(16, 185, 129, 0.05); text-align: center;'>
                                                    <a href='{$url}' target='_blank' style='display: block;'>
                                                        <img src='{$url}' alt='Foto Masuk Lembur' style='max-height: 220px; width: 100%; object-fit: cover; border-radius: 0.5rem; border: 1px solid rgba(0,0,0,0.1); margin: 0 auto;' />
                                                    </a>
                                                    <div style='margin-top: 0.5rem; font-size: 0.8125rem; font-weight: 700; color: #059669;'>
                                                        ✓ Jam Masuk Aktual: {$jam}
                                                    </div>
                                                </div>
                                            ");
                                        }
                                        return new \Illuminate\Support\HtmlString("
                                            <div style='border: 1.5px dashed rgba(156, 163, 175, 0.4); border-radius: 0.75rem; padding: 2rem 1rem; text-align: center; background: rgba(156, 163, 175, 0.05);'>
                                                <div style='width: 48px; height: 48px; border-radius: 9999px; background: rgba(156, 163, 175, 0.15); margin: 0 auto 0.5rem auto; display: flex; align-items: center; justify-content: center; color: #9ca3af;'>
                                                    <svg style='width: 24px; height: 24px;' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z'></path></svg>
                                                </div>
                                                <strong style='display: block; font-size: 0.875rem; color: #6b7280;'>Belum Ada Foto Masuk</strong>
                                                <span style='font-size: 0.75rem; color: #9ca3af; display: block; margin-top: 0.25rem;'>Karyawan belum melakukan presensi selfie masuk lembur.</span>
                                            </div>
                                        ");
                                    }),

                                \Filament\Forms\Components\Placeholder::make('preview_foto_keluar')
                                    ->label('Foto Keluar Lembur')
                                    ->content(function ($record) {
                                        if ($record && $record->foto_selesai) {
                                            $url = asset('storage/' . $record->foto_selesai);
                                            $jam = $record->jam_selesai_aktual ? Carbon::parse($record->jam_selesai_aktual)->format('H:i') . ' WIB' : '-';
                                            return new \Illuminate\Support\HtmlString("
                                                <div style='border: 1.5px solid rgba(16, 185, 129, 0.3); border-radius: 0.75rem; padding: 0.75rem; background: rgba(16, 185, 129, 0.05); text-align: center;'>
                                                    <a href='{$url}' target='_blank' style='display: block;'>
                                                        <img src='{$url}' alt='Foto Pulang Lembur' style='max-height: 220px; width: 100%; object-fit: cover; border-radius: 0.5rem; border: 1px solid rgba(0,0,0,0.1); margin: 0 auto;' />
                                                    </a>
                                                    <div style='margin-top: 0.5rem; font-size: 0.8125rem; font-weight: 700; color: #059669;'>
                                                        ✓ Jam Pulang Aktual: {$jam}
                                                    </div>
                                                </div>
                                            ");
                                        }
                                        return new \Illuminate\Support\HtmlString("
                                            <div style='border: 1.5px dashed rgba(156, 163, 175, 0.4); border-radius: 0.75rem; padding: 2rem 1rem; text-align: center; background: rgba(156, 163, 175, 0.05);'>
                                                <div style='width: 48px; height: 48px; border-radius: 9999px; background: rgba(156, 163, 175, 0.15); margin: 0 auto 0.5rem auto; display: flex; align-items: center; justify-content: center; color: #9ca3af;'>
                                                    <svg style='width: 24px; height: 24px;' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z'></path></svg>
                                                </div>
                                                <strong style='display: block; font-size: 0.875rem; color: #6b7280;'>Belum Ada Foto Pulang</strong>
                                                <span style='font-size: 0.75rem; color: #9ca3af; display: block; margin-top: 0.25rem;'>Karyawan belum melakukan presensi selfie pulang lembur.</span>
                                            </div>
                                        ");
                                    }),
                            ]),
                    ]),
            ]);
    }
}
