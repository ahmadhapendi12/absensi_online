<?php

namespace App\Filament\Resources\PengajuanIzins\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Absensi;
use App\Models\JadwalKaryawan;
use Carbon\CarbonPeriod;

class PengajuanIzinsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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
                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sakit' => 'danger',
                        'Izin' => 'warning',
                        'Cuti' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('tanggal_selesai')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('alasan')
                    ->label('Alasan')
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->alasan),
                TextColumn::make('status')
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
                SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                    ]),
                SelectFilter::make('jenis')
                    ->options([
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Cuti' => 'Cuti',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'Pending')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'Disetujui',
                            'disetujui_oleh' => auth()->id(),
                        ]);

                        $period = CarbonPeriod::create($record->tanggal_mulai, $record->tanggal_selesai);
                        foreach ($period as $date) {
                            $tgl = $date->toDateString();
                            $jadwal = JadwalKaryawan::where('user_id', $record->user_id)
                                ->where('tanggal_kerja', $tgl)
                                ->first();

                            Absensi::updateOrCreate(
                                ['user_id' => $record->user_id, 'tanggal' => $tgl],
                                [
                                    'jadwal_id' => $jadwal?->id,
                                    'pengajuan_izin_id' => $record->id,
                                    'status_masuk' => $record->jenis,
                                    'menit_terlambat' => 0,
                                ]
                            );
                        }

                        Notification::make()
                            ->title('Pengajuan izin disetujui & otomatis dicatat ke rekap absensi.')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'Pending')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'Ditolak',
                            'disetujui_oleh' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Pengajuan izin ditolak.')
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
