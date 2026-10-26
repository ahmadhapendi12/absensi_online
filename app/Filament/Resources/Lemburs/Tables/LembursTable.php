<?php

namespace App\Filament\Resources\Lemburs\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
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
                    ->formatStateUsing(fn ($record) => ($record->jam_mulai_aktual ? \Carbon\Carbon::parse($record->jam_mulai_aktual)->format('H:i') : '-') . ' - ' . ($record->jam_selesai_aktual ? \Carbon\Carbon::parse($record->jam_selesai_aktual)->format('H:i') : '-')),
                TextColumn::make('durasi_menit')
                    ->label('Durasi')
                    ->formatStateUsing(fn ($state) => round($state / 60, 1) . ' Jam')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('status_pengajuan')
                    ->label('Status ACC')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('progres_lembur')
                    ->label('Pelaksanaan')
                    ->badge()
                    ->state(function ($record): string {
                        if ($record->status_pengajuan !== 'Disetujui') {
                            return $record->status_pengajuan;
                        }
                        if ($record->foto_mulai && $record->foto_selesai) {
                            return 'Lembur Selesai';
                        }
                        if ($record->foto_mulai && ! $record->foto_selesai) {
                            return 'Sedang Lembur';
                        }
                        return 'Siap Mulai';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Lembur Selesai' => 'success',
                        'Sedang Lembur' => 'warning',
                        'Siap Mulai' => 'info',
                        'Ditolak' => 'danger',
                        default => 'gray',
                    }),
                ImageColumn::make('foto_mulai')
                    ->label('Foto Masuk')
                    ->disk('public')
                    ->circular()
                    ->placeholder('-'),
                ImageColumn::make('foto_selesai')
                    ->label('Foto Pulang')
                    ->disk('public')
                    ->circular()
                    ->placeholder('-'),
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
                    ->icon('heroicon-o-x-circle')
                    ->modalHeading('Tolak Pengajuan Lembur')
                    ->modalDescription('Masukkan alasan penolakan agar karyawan mengetahui kendala lembur ini.')
                    ->modalSubmitActionLabel('Konfirmasi Penolakan')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->placeholder('Contoh: Jam kerja operasional sudah mencukupi / Alokasi anggaran lembur tidak tersedia.')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(fn ($record) => $record->status_pengajuan === 'Pending')
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status_pengajuan' => 'Ditolak',
                            'disetujui_oleh' => auth()->id(),
                            'alasan_penolakan' => $data['alasan_penolakan'],
                        ]);

                        Notification::make()
                            ->title('Pengajuan Lembur Berhasil Ditolak')
                            ->body('Alasan penolakan telah disimpan.')
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
