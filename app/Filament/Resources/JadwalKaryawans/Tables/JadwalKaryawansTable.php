<?php

namespace App\Filament\Resources\JadwalKaryawans\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
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
                ImageColumn::make('user.avatar_url')
                    ->label('')
                    ->circular()
                    ->width(40)
                    ->height(40),
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->description(fn ($record): string => 'NIK: ' . ($record->user?->profile?->nik ?? '-'))
                    ->searchable(['name'])
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
                    ->label('Jam Kerja')
                    ->formatStateUsing(fn ($record) => $record->shift ? (Carbon::parse($record->shift->jam_masuk)->format('H:i') . ' - ' . Carbon::parse($record->shift->jam_pulang)->format('H:i') . ' WIB') : '-'),
                TextColumn::make('hari_libur_rutin')
                    ->label('Libur Rutin')
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : 'Minggu')
                    ->sortable(),
                TextColumn::make('user.status')
                    ->label('Status Akun')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                        default => $state ? ucfirst($state) : '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('cabang_id')
                    ->label('Filter Cabang')
                    ->relationship('user.cabang', 'nama_cabang'),
                SelectFilter::make('shift_id')
                    ->label('Filter Shift')
                    ->relationship('shift', 'nama_shift'),
                SelectFilter::make('hari_libur_rutin')
                    ->label('Filter Hari Libur')
                    ->options([
                        'senin' => 'Senin',
                        'selasa' => 'Selasa',
                        'rabu' => 'Rabu',
                        'kamis' => 'Kamis',
                        'jumat' => 'Jumat',
                        'sabtu' => 'Sabtu',
                        'minggu' => 'Minggu',
                    ]),
                SelectFilter::make('status')
                    ->label('Status Karyawan')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                    ])
                    ->query(fn (Builder $query, array $data) => !empty($data['value']) ? $query->whereHas('user', fn ($q) => $q->where('status', $data['value'])) : $query),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah Shift'),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
