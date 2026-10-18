<?php

namespace App\Filament\Karyawan\Resources\Lemburs\Tables;

use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LemburKaryawansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                // Mode Ringkas Khusus Tampilan Layar HP (< md)
                TextColumn::make('mobile_summary')
                    ->label('Tanggal & Waktu')
                    ->getStateUsing(fn ($record) => Carbon::parse($record->tanggal)->format('d M Y'))
                    ->description(function ($record) {
                        $hari = Carbon::parse($record->tanggal)->translatedFormat('l');
                        $mulai = $record->jam_mulai_aktual ? Carbon::parse($record->jam_mulai_aktual)->format('H:i') : '-';
                        $selesai = $record->jam_selesai_aktual ? Carbon::parse($record->jam_selesai_aktual)->format('H:i') : '-';
                        $durasi = round($record->durasi_menit / 60, 1) . ' jam';
                        return "{$hari} • {$mulai}-{$selesai} ({$durasi})";
                    })
                    ->hiddenFrom('md'),

                // Mode Terpisah Bersih Khusus Desktop (>= md)
                TextColumn::make('tanggal')
                    ->label('Tanggal Lembur')
                    ->date('d M Y')
                    ->visibleFrom('md')
                    ->sortable(),

                TextColumn::make('jam_mulai_aktual')
                    ->label('Waktu Lembur')
                    ->formatStateUsing(function ($record) {
                        $mulai = $record->jam_mulai_aktual ? Carbon::parse($record->jam_mulai_aktual)->format('H:i') : '-';
                        $selesai = $record->jam_selesai_aktual ? Carbon::parse($record->jam_selesai_aktual)->format('H:i') : '-';
                        return "{$mulai} - {$selesai} WIB";
                    })
                    ->visibleFrom('md'),

                TextColumn::make('durasi_menit')
                    ->label('Durasi')
                    ->formatStateUsing(fn ($state) => round($state / 60, 1) . ' Jam')
                    ->badge()
                    ->color('info')
                    ->visibleFrom('md')
                    ->sortable(),

                TextColumn::make('alasan_lembur')
                    ->label('Tugas / Alasan')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->alasan_lembur)
                    ->visibleFrom('md'),

                TextColumn::make('status_pengajuan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                SelectFilter::make('status_pengajuan')
                    ->options([
                        'Pending' => 'Pending',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
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
                    ->visible(fn ($record) => $record->status_pengajuan === 'Pending'),
            ]);
    }
}
