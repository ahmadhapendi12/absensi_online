<?php

namespace App\Filament\Resources\Lemburs\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LembursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('jam_mulai_aktual')
                    ->label('Waktu Lembur')
                    ->formatStateUsing(fn ($record) => ($record->jam_mulai_aktual ? \Carbon\Carbon::parse($record->jam_mulai_aktual)->format('H:i') : '-') . ' - ' . ($record->jam_selesai_aktual ? \Carbon\Carbon::parse($record->jam_selesai_aktual)->format('H:i') : '-'))
                    ->description(fn ($record): string => ($record->durasi_menit ?? 0) . ' Menit'),
                TextColumn::make('status_pengajuan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('penyetuju.name')
                    ->label('Disetujui Oleh')
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status_pengajuan')
                    ->label('Filter Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status_pengajuan === 'Pending')
                    ->action(function ($record) {
                        $record->update([
                            'status_pengajuan' => 'Disetujui',
                            'disetujui_oleh' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Pengajuan lembur disetujui.')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status_pengajuan === 'Pending')
                    ->action(function ($record) {
                        $record->update([
                            'status_pengajuan' => 'Ditolak',
                            'disetujui_oleh' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Pengajuan lembur ditolak.')
                            ->danger()
                            ->send();
                    }),
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
