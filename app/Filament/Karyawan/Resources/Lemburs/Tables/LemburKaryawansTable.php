<?php

namespace App\Filament\Karyawan\Resources\Lemburs\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LemburKaryawansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                // Mode Ringkas Khusus Tampilan Layar HP (< md)
                TextColumn::make('mobile_summary')
                    ->label('Tanggal & Waktu')
                    ->getStateUsing(fn ($record) => Carbon::parse($record->tanggal)->format('d M Y'))
                    ->description(function ($record) {
                        $hari = Carbon::parse($record->tanggal)->translatedFormat('l');
                        $mulai = $record->jam_mulai_aktual ? Carbon::parse($record->jam_mulai_aktual)->format('H:i') : '-';
                        $selesai = $record->jam_selesai_aktual ? Carbon::parse($record->jam_selesai_aktual)->format('H:i') : '-';
                        $durasi = round($record->durasi_menit / 60, 1) . ' jam';
                        return "{$hari} • {$mulai}-{$selesai} ({$durasi})";
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
                    ->placeholder('-')
                    ->visibleFrom('lg'),

                ImageColumn::make('foto_selesai')
                    ->label('Foto Pulang')
                    ->disk('public')
                    ->circular()
                    ->placeholder('-')
                    ->visibleFrom('lg'),
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
                // AKSI 1: FOTO MASUK LEMBUR (Setelah di-ACC Admin)
                Action::make('mulaiLembur')
                    ->label('Foto Masuk Lembur')
                    ->icon('heroicon-o-camera')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status_pengajuan === 'Disetujui' && empty($record->foto_mulai))
                    ->modalHeading('Presensi Masuk Lembur')
                    ->modalDescription('Unggah foto selfie / bukti kehadiran di lokasi saat memulai jam lembur.')
                    ->modalSubmitActionLabel('Simpan Masuk Lembur')
                    ->form([
                        TimePicker::make('jam_mulai_aktual')
                            ->label('Jam Mulai Lembur (Format 24 Jam)')
                            ->prefixIcon('heroicon-o-clock')
                            ->native(false)
                            ->format('H:i')
                            ->displayFormat('H:i')
                            ->seconds(false)
                            ->default(now()->format('H:i'))
                            ->required(),
                        FileUpload::make('foto_mulai')
                            ->label('Foto Bukti Masuk Lembur')
                            ->image()
                            ->directory('lembur')
                            ->disk('public')
                            ->required()
                            ->helperText('Ambil foto selfie di area kerja atau foto pekerjaan awal.'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'jam_mulai_aktual' => $data['jam_mulai_aktual'],
                            'foto_mulai' => $data['foto_mulai'],
                        ]);

                        Notification::make()
                            ->title('Presensi Masuk Lembur Tercatat')
                            ->body('Selamat menjalankan lembur. Jangan lupa foto pulang setelah selesai.')
                            ->success()
                            ->send();
                    }),

                // AKSI 2: FOTO PULANG LEMBUR (Setelah Foto Masuk Selesai)
                Action::make('selesaiLembur')
                    ->label('Foto Pulang Lembur')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status_pengajuan === 'Disetujui' && ! empty($record->foto_mulai) && empty($record->foto_selesai))
                    ->modalHeading('Presensi Selesai Lembur')
                    ->modalDescription('Unggah foto selfie / bukti hasil kerja selesai lembur untuk mengakhiri sesi.')
                    ->modalSubmitActionLabel('Selesaikan Lembur')
                    ->form([
                        TimePicker::make('jam_selesai_aktual')
                            ->label('Jam Selesai Lembur (Format 24 Jam)')
                            ->prefixIcon('heroicon-o-clock')
                            ->native(false)
                            ->format('H:i')
                            ->displayFormat('H:i')
                            ->seconds(false)
                            ->default(now()->format('H:i'))
                            ->required(),
                        FileUpload::make('foto_selesai')
                            ->label('Foto Bukti Selesai Lembur')
                            ->image()
                            ->directory('lembur')
                            ->disk('public')
                            ->required()
                            ->helperText('Ambil foto selfie closing atau hasil pekerjaan lembur.'),
                    ])
                    ->action(function ($record, array $data) {
                        $jamMulai = $record->jam_mulai_aktual ? Carbon::parse($record->jam_mulai_aktual) : Carbon::parse($record->tanggal . ' 17:00');
                        $jamSelesai = Carbon::parse($data['jam_selesai_aktual']);
                        if ($jamSelesai->lessThan($jamMulai)) {
                            $jamSelesai->addDay();
                        }
                        $durasiMenit = (int) $jamMulai->diffInMinutes($jamSelesai);

                        $record->update([
                            'jam_selesai_aktual' => $data['jam_selesai_aktual'],
                            'foto_selesai' => $data['foto_selesai'],
                            'durasi_menit' => $durasiMenit,
                        ]);

                        $durasiJam = round($durasiMenit / 60, 1);
                        Notification::make()
                            ->title('Lembur Berhasil Diselesaikan')
                            ->body("Total durasi lembur: {$durasiJam} Jam ({$durasiMenit} Menit). Terima kasih!")
                            ->success()
                            ->send();
                    }),

                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail'),
                DeleteAction::make()
                    ->label('Batalkan')
                    ->iconButton()
                    ->tooltip('Batalkan Pengajuan')
                    ->visible(fn ($record) => $record->status_pengajuan === 'Pending'),
            ]);
    }
}
