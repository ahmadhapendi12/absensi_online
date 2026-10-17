<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\User;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile.foto_wajah_acuan')
                    ->label('Foto')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(fn (User $record) => $record->avatar_url),
                TextColumn::make('profile.nik')
                    ->label('NIK')
                    ->placeholder('-')
                    ->weight('bold')
                    ->copyable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Karyawan')
                    ->description(fn (User $record): ?string => $record->email)
                    ->searchable(['name', 'email'])
                    ->sortable(),
                TextColumn::make('profile.telphone')
                    ->label('No. HP / WA')
                    ->placeholder('-')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('cabang.nama_cabang')
                    ->label('Cabang')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'superadmin' => 'danger',
                        'karyawan' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                        default => $state,
                    }),
                TextColumn::make('profile.alamat')
                    ->label('Alamat')
                    ->placeholder('-')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('cabang_id')
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
