<?php

namespace App\Filament\Resources\MasterShifts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MasterShiftsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_shift')
                    ->label('Nama Shift')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jam_masuk')
                    ->label('Jam Kerja Shift')
                    ->formatStateUsing(fn ($record) => \Carbon\Carbon::parse($record->jam_masuk)->format('H:i') . ' - ' . \Carbon\Carbon::parse($record->jam_pulang)->format('H:i') . ' WIB')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('buka_absen_menit')
                    ->label('Buka Presensi Masuk')
                    ->formatStateUsing(function ($record) {
                        $masuk = \Carbon\Carbon::createFromTimeString($record->jam_masuk);
                        $buka = $masuk->subMinutes($record->buka_absen_menit ?? 30);
                        return $buka->format('H:i') . ' WIB';
                    })
                    ->description(fn ($record): string => ($record->buka_absen_menit ?? 30) . ' menit sebelum shift')
                    ->badge()
                    ->color('success'),

                TextColumn::make('toleransi_terlambat_menit')
                    ->label('Batas Tepat Waktu')
                    ->formatStateUsing(function ($record) {
                        $masuk = \Carbon\Carbon::createFromTimeString($record->jam_masuk);
                        $toleransi = $masuk->addMinutes($record->toleransi_terlambat_menit ?? 15);
                        return 's/d ' . $toleransi->format('H:i') . ' WIB';
                    })
                    ->description(fn ($record): string => 'Toleransi ' . ($record->toleransi_terlambat_menit ?? 15) . ' menit')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('toleransi_pulang_menit')
                    ->label('Boleh Absen Pulang')
                    ->formatStateUsing(function ($record) {
                        $pulang = \Carbon\Carbon::createFromTimeString($record->jam_pulang);
                        if (($record->toleransi_pulang_menit ?? 0) > 0) {
                            $boleh = $pulang->subMinutes($record->toleransi_pulang_menit);
                            return 'Mulai ' . $boleh->format('H:i') . ' WIB';
                        }
                        return 'Pukul ' . $pulang->format('H:i') . ' WIB';
                    })
                    ->description(fn ($record): string => ($record->toleransi_pulang_menit ?? 0) > 0 ? 'Toleransi ' . $record->toleransi_pulang_menit . ' mnt awal' : 'Wajib tepat jam pulang')
                    ->badge()
                    ->color('info'),

                TextColumn::make('batas_akhir_pulang_menit')
                    ->label('Batas Akhir Pulang (Closing)')
                    ->formatStateUsing(function ($record) {
                        $pulang = \Carbon\Carbon::createFromTimeString($record->jam_pulang);
                        $akhir = $pulang->addMinutes($record->batas_akhir_pulang_menit ?? 120);
                        return 's/d ' . $akhir->format('H:i') . ' WIB';
                    })
                    ->description(fn ($record): string => 'Maks ' . (($record->batas_akhir_pulang_menit ?? 120) / 60) . ' jam closing')
                    ->badge()
                    ->color('danger'),

                TextColumn::make('cabang.nama_cabang')
                    ->label('Cabang / Toko')
                    ->placeholder('Semua Cabang (Global)')
                    ->badge()
                    ->color(fn ($record) => $record->cabang?->is_24_jam ? 'warning' : 'info')
                    ->formatStateUsing(fn ($record) => ($record->cabang?->nama_cabang ?? 'Semua Cabang') . ($record->cabang?->is_24_jam ? ' [24 Jam]' : ''))
                    ->sortable(),

                IconColumn::make('lintas_hari')
                    ->label('Lintas Hari')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('cabang_id')
                    ->label('Filter Cabang')
                    ->relationship('cabang', 'nama_cabang'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
