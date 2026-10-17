<?php

namespace App\Filament\Karyawan\Resources\PengajuanIzins\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PengajuanIzinKaryawansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('jenis')
                    ->label('Jenis & Periode')
                    ->badge()
                    ->weight('bold')
                    ->color(fn (string $state): string => match ($state) {
                        'Sakit' => 'danger',
                        'Izin' => 'warning',
                        'Cuti' => 'info',
                        default => 'gray',
                    })
                    ->description(function ($record): string {
                        $mulai = \Carbon\Carbon::parse($record->tanggal_mulai)->format('d M Y');
                        $hari = \Carbon\Carbon::parse($record->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($record->tanggal_selesai)) + 1;
                        if ($record->tanggal_mulai === $record->tanggal_selesai) {
                            return "{$mulai} • {$hari} hari";
                        }
                        $selesai = \Carbon\Carbon::parse($record->tanggal_selesai)->format('d M Y');
                        return "{$mulai} - {$selesai} ({$hari} hari)";
                    })
                    ->sortable(),

                TextColumn::make('tanggal_mulai')
                    ->label('Periode Izin')
                    ->formatStateUsing(function ($record) {
                        $mulai = \Carbon\Carbon::parse($record->tanggal_mulai)->format('d M Y');
                        if ($record->tanggal_mulai === $record->tanggal_selesai) {
                            return $mulai;
                        }
                        $selesai = \Carbon\Carbon::parse($record->tanggal_selesai)->format('d M Y');
                        return "{$mulai} - {$selesai}";
                    })
                    ->visibleFrom('md')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'warning',
                    })
                    ->description(fn ($record) => $record->penyetuju ? 'Oleh: ' . $record->penyetuju->name : null),

                TextColumn::make('alasan')
                    ->label('Alasan')
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->alasan)
                    ->visibleFrom('md')
                    ->toggleable(),

                TextColumn::make('penyetuju.name')
                    ->label('Diproses Oleh')
                    ->placeholder('Menunggu')
                    ->visibleFrom('lg')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                    ]),
                SelectFilter::make('jenis')
                    ->options([
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Cuti' => 'Cuti',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail'),
                DeleteAction::make()
                    ->label('Batalkan')
                    ->iconButton()
                    ->tooltip('Batalkan Pengajuan')
                    ->visible(fn ($record) => $record->status === 'Pending'),
            ]);
    }
}
