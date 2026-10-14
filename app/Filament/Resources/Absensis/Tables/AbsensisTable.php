<?php

namespace App\Filament\Resources\Absensis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AbsensisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('user.profile.nik')
                    ->label('NIK')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.cabang.nama_cabang')
                    ->label('Cabang')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('jadwal.shift.nama_shift')
                    ->label('Shift')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),
                TextColumn::make('jam_masuk')
                    ->label('Masuk')
                    ->time('H:i')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('jam_pulang')
                    ->label('Pulang')
                    ->time('H:i')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('status_masuk')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Terlambat' => 'warning',
                        'Izin' => 'info',
                        'Sakit' => 'danger',
                        'Cuti' => 'primary',
                        default => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('menit_terlambat')
                    ->label('Terlambat')
                    ->suffix(' mnt')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('foto_masuk')
                    ->label('Foto Selfie')
                    ->disk('public')
                    ->circular()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status_masuk')
                    ->label('Status Kehadiran')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Terlambat' => 'Terlambat',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                        'Cuti' => 'Cuti',
                        'Alpha' => 'Alpha',
                    ]),
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
