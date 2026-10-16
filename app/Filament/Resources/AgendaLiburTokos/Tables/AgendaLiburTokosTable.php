<?php

namespace App\Filament\Resources\AgendaLiburTokos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AgendaLiburTokosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('nama_agenda')
                    ->label('Nama Agenda / Libur')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->keterangan ? \Illuminate\Support\Str::limit($record->keterangan, 40) : null),

                TextColumn::make('jenis')
                    ->label('Jenis Agenda')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'jumsih' => 'Jumsih',
                        'kamsih' => 'Kamsih',
                        'libur_kios' => 'Libur Kios',
                        'libur_nasional' => 'Libur Nasional',
                        default => $state ? ucfirst($state) : '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'jumsih' => 'info',
                        'kamsih' => 'warning',
                        'libur_kios' => 'danger',
                        'libur_nasional' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('cabang.nama_cabang')
                    ->label('Berlaku Untuk')
                    ->placeholder('Semua Cabang')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Terjadwal',
                        'berlangsung' => 'Sedang Berlangsung',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        default => $state ? ucfirst($state) : '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'info',
                        'berlangsung' => 'warning',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'pending' => 'Terjadwal',
                        'berlangsung' => 'Sedang Berlangsung',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
                SelectFilter::make('jenis')
                    ->label('Filter Jenis')
                    ->options([
                        'jumsih' => 'Jumsih',
                        'kamsih' => 'Kamsih',
                        'libur_kios' => 'Libur Kios',
                        'libur_nasional' => 'Libur Nasional',
                        'lainnya' => 'Lainnya',
                    ]),
                SelectFilter::make('cabang_id')
                    ->label('Filter Cabang')
                    ->relationship('cabang', 'nama_cabang'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal', 'desc');
    }
}
