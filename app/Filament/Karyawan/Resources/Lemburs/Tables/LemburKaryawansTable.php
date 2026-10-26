<?php

namespace App\Filament\Karyawan\Resources\Lemburs\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class LemburKaryawansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                // Mode Ringkas & Responsif Khusus Tampilan Layar HP (< md) Tanpa Perlu Scroll Samping
                TextColumn::make('mobile_summary')
                    ->label('Rincian Lembur')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $tgl = Carbon::parse($record->tanggal)->format('d M Y');
                        $hari = Carbon::parse($record->tanggal)->translatedFormat('l');
                        $mulai = $record->jam_mulai_aktual ? Carbon::parse($record->jam_mulai_aktual)->format('H:i') : '-';
                        $selesai = $record->jam_selesai_aktual ? Carbon::parse($record->jam_selesai_aktual)->format('H:i') : '-';
                        $durasi = round($record->durasi_menit / 60, 1) . ' Jam';

                        $badgeAccColor = match ($record->status_pengajuan) {
                            'Disetujui' => '#059669',
                            'Ditolak' => '#dc2626',
                            default => '#d97706',
                        };
                        $badgeAccBg = match ($record->status_pengajuan) {
                            'Disetujui' => 'rgba(16, 185, 129, 0.15)',
                            'Ditolak' => 'rgba(239, 68, 68, 0.15)',
                            default => 'rgba(245, 158, 11, 0.15)',
                        };

                        $progresText = 'Siap Mulai';
                        $progresColor = '#2563eb';
                        $progresBg = 'rgba(37, 99, 235, 0.12)';

                        if ($record->status_pengajuan !== 'Disetujui') {
                            $progresText = $record->status_pengajuan;
                            $progresColor = $badgeAccColor;
                            $progresBg = $badgeAccBg;
                        } elseif ($record->foto_mulai && $record->foto_selesai) {
                            $progresText = 'Lembur Selesai';
                            $progresColor = '#059669';
                            $progresBg = 'rgba(16, 185, 129, 0.15)';
                        } elseif ($record->foto_mulai && !$record->foto_selesai) {
                            $progresText = 'Sedang Berlangsung';
                            $progresColor = '#d97706';
                            $progresBg = 'rgba(245, 158, 11, 0.15)';
                        }

                        $alasan = e(\Illuminate\Support\Str::limit($record->alasan_lembur, 45));

                        $fotoBuktiBadge = '';
                        if ($record->foto_mulai && $record->foto_selesai) {
                            $fotoBuktiBadge = '<span style="font-size: 0.6875rem; color: #059669; font-weight: 600;">📷 Foto Masuk & Pulang ✓</span>';
                        } elseif ($record->foto_mulai) {
                            $fotoBuktiBadge = '<span style="font-size: 0.6875rem; color: #d97706; font-weight: 600;">📷 Foto Masuk ✓</span>';
                        }

                        return new HtmlString("
                            <div style='display: flex; flex-direction: column; gap: 0.35rem; padding: 0.25rem 0;'>
                                <div style='display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;'>
                                    <strong style='font-size: 0.9375rem;'>{$hari}, {$tgl}</strong>
                                    <span style='padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: {$badgeAccBg}; color: {$badgeAccColor};'>
                                        {$record->status_pengajuan}
                                    </span>
                                </div>
                                <div style='display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; opacity: 0.9;'>
                                    <span>⏰ {$mulai} - {$selesai} WIB</span>
                                    <span style='opacity: 0.5;'>•</span>
                                    <span style='font-weight: 700; color: #f59e0b;'>{$durasi}</span>
                                </div>
                                <div style='font-size: 0.75rem; opacity: 0.75; font-style: italic;'>
                                    \"{$alasan}\"
                                </div>
                                <div style='display: flex; align-items: center; justify-content: space-between; margin-top: 0.125rem;'>
                                    <span style='padding: 0.125rem 0.45rem; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: {$progresBg}; color: {$progresColor};'>
                                        {$progresText}
                                    </span>
                                    {$fotoBuktiBadge}
                                </div>
                            </div>
                        ");
                    })
                    ->hiddenFrom('md'),

                // Mode Terpisah Bersih Khusus Desktop (>= md)
                TextColumn::make('tanggal')
                    ->label('Tanggal Lembur')
                    ->date('d M Y')
                    ->visibleFrom('md')
                    ->sortable(),

                TextColumn::make('jam_mulai_aktual')
                    ->label('Waktu Lembur')
                    ->formatStateUsing(function ($record) {
                        $mulai = $record->jam_mulai_aktual ? Carbon::parse($record->jam_mulai_aktual)->format('H:i') : '-';
                        $selesai = $record->jam_selesai_aktual ? Carbon::parse($record->jam_selesai_aktual)->format('H:i') : '-';
                        return "{$mulai} - {$selesai} WIB";
                    })
                    ->visibleFrom('md'),

                TextColumn::make('durasi_menit')
                    ->label('Durasi')
                    ->formatStateUsing(fn ($state) => round($state / 60, 1) . ' Jam')
                    ->badge()
                    ->color('info')
                    ->visibleFrom('md')
                    ->sortable(),

                TextColumn::make('alasan_lembur')
                    ->label('Tugas / Alasan')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->alasan_lembur)
                    ->visibleFrom('md'),

                TextColumn::make('status_pengajuan')
                    ->label('Status ACC')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'warning',
                    })
                    ->description(function ($record): ?string {
                        if ($record->status_pengajuan === 'Ditolak' && $record->alasan_penolakan) {
                            return 'Alasan: ' . $record->alasan_penolakan;
                        }
                        return null;
                    })
                    ->visibleFrom('md'),

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
                    })
                    ->visibleFrom('md'),

                ImageColumn::make('foto_mulai')
                    ->label('Foto Masuk')
                    ->disk('public')
                    ->circular()
                    ->placeholder('-')
                    ->visibleFrom('md'),

                ImageColumn::make('foto_selesai')
                    ->label('Foto Pulang')
                    ->disk('public')
                    ->circular()
                    ->placeholder('-')
                    ->visibleFrom('md'),
            ])
            ->filters([
                SelectFilter::make('status_pengajuan')
                    ->options([
                        'Pending' => 'Pending',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                // AKSI 1: PRESENSI MASUK LEMBUR (Kamera & GPS Biometrik)
                Action::make('mulaiLembur')
                    ->label('Presensi Masuk')
                    ->icon('heroicon-o-camera')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status_pengajuan === 'Disetujui' && empty($record->foto_mulai))
                    ->url(fn ($record) => url('/karyawan/lembur-presensi?id=' . $record->id)),

                // AKSI 2: PRESENSI PULANG LEMBUR (Kamera & GPS Biometrik)
                Action::make('selesaiLembur')
                    ->label('Presensi Pulang')
                    ->icon('heroicon-o-camera')
                    ->color('success')
                    ->visible(fn ($record) => $record->status_pengajuan === 'Disetujui' && ! empty($record->foto_mulai) && empty($record->foto_selesai))
                    ->url(fn ($record) => url('/karyawan/lembur-presensi?id=' . $record->id)),

                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail Lengkap'),
                DeleteAction::make()
                    ->label('Batalkan')
                    ->iconButton()
                    ->tooltip('Batalkan Pengajuan')
                    ->visible(fn ($record) => $record->status_pengajuan === 'Pending'),
            ]);
    }
}
