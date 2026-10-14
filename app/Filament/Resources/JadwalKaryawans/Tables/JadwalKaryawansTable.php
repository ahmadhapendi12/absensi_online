<?php

namespace App\Filament\Resources\JadwalKaryawans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JadwalKaryawansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_kerja')
                    ->label('Tanggal Kerja')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('user.profile.nik')
                    ->label('NIK')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.cabang.nama_cabang')
                    ->label('Cabang')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('shift.nama_shift')
                    ->label('Shift Kerja')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('shift.jam_masuk')
                    ->label('Jam Masuk')
                    ->time('H:i'),
                TextColumn::make('shift.jam_pulang')
                    ->label('Jam Pulang')
                    ->time('H:i'),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Filter Karyawan')
                    ->relationship('user', 'name', fn (Builder $query) => $query->role('karyawan')),
                SelectFilter::make('shift_id')
                    ->label('Filter Shift')
                    ->relationship('shift', 'nama_shift'),
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
