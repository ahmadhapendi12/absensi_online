<?php

namespace App\Filament\Resources\Absensis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AbsensisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
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
                TextColumn::make('jadwal.shift.nama_shift')
                    ->label('Shift')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),
                TextColumn::make('jam_masuk')
                    ->label('Jam Presensi')
                    ->formatStateUsing(function ($record) {
                        return $record->format_jam_kerja;
                    })
                    ->description(function ($record): ?string {
                        if ($record->jam_masuk && !$record->jam_pulang) {
                            if ($record->status_display === 'Tidak Absen Pulang') {
                                return 'Tidak checkout hingga batas shift';
                            }
                            return 'Masuk: ' . \Carbon\Carbon::parse($record->jam_masuk)->format('H:i:s') . ' WIB';
                        }
                        return null;
                    })
                    ->sortable(),
                TextColumn::make('status_masuk')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        return $record->status_display;
                    })
                    ->color(function ($record): string {
                        return match ($record->status_display) {
                            'Sedang Berlangsung' => 'info',
                            'Tidak Absen Pulang' => 'danger',
                            'Hadir', 'Tepat Waktu' => 'success',
                            'Terlambat' => 'warning',
                            'Izin' => 'info',
                            'Sakit' => 'danger',
                            'Cuti' => 'primary',
                            default => 'danger',
                        };
                    })
                    ->description(function ($record): ?string {
                        if ($record->jam_masuk && !$record->jam_pulang) {
                            $ket = $record->status_masuk === 'Terlambat' 
                                ? 'Terlambat (' . $record->menit_terlambat . ' mnt)' 
                                : 'Tepat Waktu';
                            return 'Saat masuk: ' . $ket;
                        }
                        return null;
                    })
                    ->sortable(),
                TextColumn::make('menit_terlambat')
                    ->label('Terlambat')
                    ->suffix(' mnt')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('foto_masuk')
                    ->label('Foto Selfie')
                    ->disk('public')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_masuk')
                    ->label('Status Kehadiran')
                    ->options([
                        'sedang_berlangsung' => 'Sedang Berlangsung (Sedang Bekerja)',
                        'tidak_absen_pulang' => 'Tidak Absen Pulang (Lupa Checkout)',
                        'Hadir' => 'Hadir / Tepat Waktu',
                        'Terlambat' => 'Terlambat',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                        'Cuti' => 'Cuti',
                        'Alpha' => 'Alpha',
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if (($data['value'] ?? null) === 'sedang_berlangsung') {
                            return $query->whereDate('tanggal', now()->toDateString())
                                ->whereNotNull('jam_masuk')
                                ->whereNull('jam_pulang');
                        }
                        if (($data['value'] ?? null) === 'tidak_absen_pulang') {
                            return $query->whereNotNull('jam_masuk')
                                ->whereNull('jam_pulang')
                                ->where(function ($q) {
                                    $q->whereDate('tanggal', '<', now()->toDateString())
                                      ->orWhereRaw("jam_masuk IS NOT NULL AND jam_pulang IS NULL AND tanggal = CURRENT_DATE");
                                });
                        }
                        if (!empty($data['value'])) {
                            return $query->where('status_masuk', $data['value']);
                        }
                        return $query;
                    }),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('export_pdf')
                    ->label('Unduh Laporan PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('danger')
                    ->modalHeading('Export Rekapitulasi Presensi (PDF)')
                    ->modalDescription('Pilih periode, cabang, dan kriteria data presensi yang ingin diunduh ke format PDF.')
                    ->modalSubmitActionLabel('Unduh Laporan PDF')
                    ->schema([
                        \Filament\Forms\Components\Select::make('tipe_periode')
                            ->label('Pilihan Periode')
                            ->options([
                                'bulan' => 'Bulan Tertentu',
                                'tahun' => '1 Tahun Penuh',
                                'hari_ini' => 'Hari Ini',
                                'rentang' => 'Rentang Tanggal Kustom',
                            ])
                            ->default('bulan')
                            ->live(),
                        \Filament\Forms\Components\Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ])
                            ->default(now()->month)
                            ->visible(fn ($get) => in_array($get('tipe_periode'), ['bulan', null])),
                        \Filament\Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options([
                                now()->year - 1 => (string)(now()->year - 1),
                                now()->year => (string)now()->year,
                                now()->year + 1 => (string)(now()->year + 1),
                            ])
                            ->default(now()->year)
                            ->visible(fn ($get) => in_array($get('tipe_periode'), ['bulan', 'tahun', null])),
                        \Filament\Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->visible(fn ($get) => $get('tipe_periode') === 'rentang'),
                        \Filament\Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->visible(fn ($get) => $get('tipe_periode') === 'rentang'),
                        \Filament\Forms\Components\Select::make('cabang_id')
                            ->label('Cabang Toko')
                            ->options(['semua' => 'Seluruh Cabang (Semua Outlet)'] + \App\Models\CabangKantor::pluck('nama_cabang', 'id')->toArray())
                            ->default('semua'),
                        \Filament\Forms\Components\Select::make('status_masuk')
                            ->label('Filter Status Presensi')
                            ->options([
                                'semua' => 'Semua Status Kehadiran',
                                'Tepat Waktu' => 'Hadir Tepat Waktu',
                                'Terlambat' => 'Terlambat Saja',
                                'Izin' => 'Izin',
                                'Sakit' => 'Sakit',
                                'Cuti' => 'Cuti',
                            ])
                            ->default('semua'),
                    ])
                    ->action(function (array $data) {
                        $params = http_build_query([
                            'tipe_periode' => $data['tipe_periode'] ?? 'bulan',
                            'bulan' => $data['bulan'] ?? now()->month,
                            'tahun' => $data['tahun'] ?? now()->year,
                            'tanggal_mulai' => $data['tanggal_mulai'] ?? null,
                            'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
                            'cabang_id' => $data['cabang_id'] ?? 'semua',
                            'status_masuk' => $data['status_masuk'] ?? 'semua',
                        ]);
                        return redirect()->to(route('laporan.export-pdf') . '?' . $params);
                    }),
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
