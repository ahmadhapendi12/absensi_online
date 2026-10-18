<?php

namespace App\Filament\Karyawan\Resources\RekapPresensis\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RekapPresensiKaryawansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->striped()
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->description(fn ($record) => Carbon::parse($record->tanggal)->translatedFormat('l'))
                    ->sortable(),

                TextColumn::make('jadwal.shift.nama_shift')
                    ->label('Shift Kerja')
                    ->getStateUsing(function ($record) {
                        return $record->jadwal?->shift?->nama_shift 
                            ?? $record->user?->jadwal?->shift?->nama_shift 
                            ?? 'Shift Standar';
                    })
                    ->badge()
                    ->color('gray')
                    ->visibleFrom('sm'),

                TextColumn::make('format_jam_kerja')
                    ->label('Jam Kerja')
                    ->placeholder('-')
                    ->badge()
                    ->color('info'),

                TextColumn::make('status_display')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Sedang Berlangsung' => 'info',
                        'Terlambat' => 'warning',
                        'Tidak Absen Pulang' => 'danger',
                        'Izin', 'Cuti' => 'info',
                        'Sakit' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('menit_terlambat')
                    ->label('Keterlambatan')
                    ->formatStateUsing(function ($state, $record) {
                        if (in_array($record->status_masuk, ['Izin', 'Sakit', 'Cuti', 'Alpha']) || !$record->jam_masuk) {
                            return '-';
                        }
                        if (!$state || $state <= 0) {
                            return 'Tepat Waktu';
                        }
                        return "{$state} Menit";
                    })
                    ->badge()
                    ->color(function ($state, $record) {
                        if (in_array($record->status_masuk, ['Izin', 'Sakit', 'Cuti', 'Alpha']) || !$record->jam_masuk) {
                            return 'gray';
                        }
                        return $state > 0 ? 'warning' : 'success';
                    })
                    ->visibleFrom('md')
                    ->toggleable(),

                ImageColumn::make('foto_masuk')
                    ->label('Foto Masuk')
                    ->circular()
                    ->disk('public')
                    ->placeholder('-')
                    ->visibleFrom('md')
                    ->toggleable(),

                ImageColumn::make('foto_pulang')
                    ->label('Foto Pulang')
                    ->circular()
                    ->disk('public')
                    ->placeholder('-')
                    ->visibleFrom('md')
                    ->toggleable(),
            ])
            ->actions([
                Action::make('detailFoto')
                    ->iconButton()
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat Detail & Foto Presensi')
                    ->color('gray')
                    ->modalHeading(fn ($record) => 'Detail Presensi: ' . Carbon::parse($record->tanggal)->translatedFormat('l, d F Y'))
                    ->modalContent(fn ($record) => view('filament.karyawan.modals.detail-presensi', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->filters([
                SelectFilter::make('status_masuk')
                    ->label('Status Kehadiran')
                    ->options([
                        'Hadir' => 'Hadir Tepat Waktu',
                        'Terlambat' => 'Terlambat',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                        'Cuti' => 'Cuti',
                        'Alpha' => 'Alpha',
                    ]),
            ]);
    }
}
