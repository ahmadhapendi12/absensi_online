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
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal Lembur')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('jam_mulai_aktual')
                    ->label('Waktu Lembur')
                    ->formatStateUsing(function ($record) {
                        $mulai = $record->jam_mulai_aktual ? Carbon::parse($record->jam_mulai_aktual)->format('H:i') : '-';
                        $selesai = $record->jam_selesai_aktual ? Carbon::parse($record->jam_selesai_aktual)->format('H:i') : '-';
                        return "{$mulai} - {$selesai} WIB";
                    }),

                TextColumn::make('durasi_menit')
                    ->label('Durasi')
                    ->formatStateUsing(function ($state) {
                        $jam = floor($state / 60);
                        $menit = $state % 60;
                        if ($jam > 0 && $menit > 0) {
                            return "{$jam} jam {$menit} menit";
                        } elseif ($jam > 0) {
                            return "{$jam} jam ({$state} mnt)";
                        }
                        return "{$state} menit";
                    })
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('tugas_lembur')
                    ->label('Tugas / Pekerjaan')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->tugas_lembur),

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
                ViewAction::make(),
                DeleteAction::make()
                    ->label('Batalkan')
                    ->visible(fn ($record) => $record->status_pengajuan === 'Pending'),
            ]);
    }
}
