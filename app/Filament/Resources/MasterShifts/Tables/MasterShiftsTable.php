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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jam_masuk')
                    ->label('Jam Kerja')
                    ->formatStateUsing(fn ($record) => \Carbon\Carbon::parse($record->jam_masuk)->format('H:i') . ' - ' . \Carbon\Carbon::parse($record->jam_pulang)->format('H:i'))
                    ->sortable(),
                TextColumn::make('toleransi_terlambat_menit')
                    ->label('Toleransi')
                    ->suffix(' menit')
                    ->numeric()
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
                //
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
