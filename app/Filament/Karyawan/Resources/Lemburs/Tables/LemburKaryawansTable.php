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
                TextColumn::make('tanggal')
                    ->label('Tanggal & Waktu')
                    ->date('d M Y')
                    ->description(function ($record) {
                        $hari = Carbon::parse($record->tanggal)->translatedFormat('l');
                        $mulai = $record->jam_mulai_aktual ? Carbon::parse($record->jam_mulai_aktual)->format('H:i') : '-';
                        $selesai = $record->jam_selesai_aktual ? Carbon::parse($record->jam_selesai_aktual)->format('H:i') : '-';
                        $durasi = round($record->durasi_menit / 60, 1) . ' jam';
                        return "{$hari} • {$mulai}-{$selesai} ({$durasi})";
                    })
                    ->sortable(),

                TextColumn::make('jam_mulai_aktual')
                    ->label('Waktu Lembur')
                    ->formatStateUsing(function ($record) {
                        $mulai = $record->jam_mulai_aktual ? Carbon::parse($record->jam_mulai_aktual)->format('H:i') : '-';
                        $selesai = $record->jam_selesai_aktual ? Carbon::parse($record->jam_selesai_aktual)->format('H:i') : '-';
                        return "{$mulai} - {$selesai} WIB";
                    })
                    ->description(function ($record) {
                        $state = $record->durasi_menit;
                        $jam = floor($state / 60);
                        $menit = $state % 60;
                        if ($jam > 0 && $menit > 0) {
                            return "{$jam} jam {$menit} mnt";
                        } elseif ($jam > 0) {
                            return "{$jam} jam";
                        }
                        return "{$state} mnt";
                    })
                    ->visibleFrom('md'),

                TextColumn::make('status_pengajuan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'warning',
                    }),

                TextColumn::make('durasi_menit')
                    ->label('Durasi Jam')
                    ->formatStateUsing(fn ($state) => round($state / 60, 1) . ' Jam')
                    ->badge()
                    ->color('info')
                    ->visibleFrom('md')
                    ->toggleable(),

                TextColumn::make('tugas_lembur')
                    ->label('Uraian Tugas')
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->tugas_lembur)
                    ->visibleFrom('md')
                    ->toggleable(),
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
