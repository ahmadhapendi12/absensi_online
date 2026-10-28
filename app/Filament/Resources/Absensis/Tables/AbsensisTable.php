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
            ->filtersFormColumns(2)
            ->filtersFormWidth(\Filament\Support\Enums\Width::TwoExtraLarge)
            ->filtersFormMaxHeight('480px')
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
                            'Libur' => 'secondary',
                            default => 'secondary',
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
                    ->label('Foto Masuk')
                    ->disk('public')
                    ->circular()
                    ->size(40)
                    ->placeholder('Tidak ada')
                    ->toggleable(),
                ImageColumn::make('foto_pulang')
                    ->label('Foto Pulang')
                    ->disk('public')
                    ->circular()
                    ->size(40)
                    ->placeholder('Tidak ada')
                    ->toggleable(),
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
                        'Libur' => 'Libur',
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
                        if (($data['value'] ?? null) === 'Hadir') {
                            return $query->whereIn('status_masuk', ['Hadir', 'Tepat Waktu']);
                        }
                        if (!empty($data['value'])) {
                            return $query->where('status_masuk', $data['value']);
                        }
                        return $query;
                    }),

                SelectFilter::make('cabang_id')
                    ->label('Cabang / Outlet')
                    ->options(fn () => \App\Models\CabangKantor::pluck('nama_cabang', 'id')->toArray())
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (!$value) return $query;
                        return $query->whereHas('user', fn ($q) => $q->where('cabang_id', $value));
                    }),

                SelectFilter::make('nama_shift')
                    ->label('Shift Kerja')
                    ->options(function () {
                        return \App\Models\MasterShift::select('nama_shift')
                            ->distinct()
                            ->orderBy('nama_shift')
                            ->pluck('nama_shift', 'nama_shift')
                            ->toArray();
                    })
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (!$value) return $query;
                        return $query->whereHas('jadwal.shift', fn ($q) => $q->where('nama_shift', $value));
                    }),

                SelectFilter::make('user_id')
                    ->label('Karyawan (Per Orang)')
                    ->searchable()
                    ->options(fn () => \App\Models\User::role('karyawan')->orderBy('name')->pluck('name', 'id')->toArray())
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (!$value) return $query;
                        return $query->where('user_id', $value);
                    }),

                \Filament\Tables\Filters\Filter::make('periode')
                    ->form([
                        \Filament\Forms\Components\Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ])
                            ->placeholder('Semua Bulan'),
                        \Filament\Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(function () {
                                $now = now()->year;
                                return [
                                    $now => (string) $now,
                                    $now - 1 => (string) ($now - 1),
                                ];
                            })
                            ->default(now()->year),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        $bulan = $data['bulan'] ?? null;
                        $tahun = $data['tahun'] ?? null;

                        if ($bulan && $tahun) {
                            $start = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
                            $end = $start->copy()->endOfMonth();
                            return $query->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
                        }

                        if ($tahun) {
                            return $query->whereYear('tanggal', $tahun);
                        }

                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        $bulan = $data['bulan'] ?? null;
                        $tahun = $data['tahun'] ?? null;
                        if ($bulan && $tahun) {
                            return 'Periode: ' . \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');
                        }
                        if ($tahun) {
                            return "Tahun: {$tahun}";
                        }
                        return null;
                    }),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('export_pdf')
                    ->label('Unduh Laporan PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('danger')
                    ->modalHeading('Export Rekapitulasi Presensi (PDF)')
                    ->modalDescription('Pilih periode, cabang, shift, dan kriteria data presensi yang ingin diunduh ke format PDF.')
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
                            ->default('semua')
                            ->live(),
                        \Filament\Forms\Components\Select::make('status_masuk')
                            ->label('Filter Status Presensi')
                            ->options([
                                'semua' => 'Semua Status Kehadiran',
                                'Hadir' => 'Hadir Tepat Waktu',
                                'Terlambat' => 'Terlambat Saja',
                                'Izin' => 'Izin',
                                'Sakit' => 'Sakit',
                                'Cuti' => 'Cuti',
                                'Libur' => 'Libur',
                                'sedang_berlangsung' => 'Sedang Berlangsung',
                                'tidak_absen_pulang' => 'Tidak Absen Pulang',
                            ])
                            ->default('semua'),
                        \Filament\Forms\Components\Select::make('shift_nama')
                            ->label('Filter Shift Kerja')
                            ->options(function (callable $get) {
                                $cabangId = $get('cabang_id');

                                // Jika user memilih cabang spesifik: tampilkan shift cabang tersebut beserta jam kerjanya
                                if ($cabangId && $cabangId !== 'semua') {
                                    $shifts = \App\Models\MasterShift::where('cabang_id', $cabangId)
                                        ->orderBy('nama_shift')
                                        ->get();

                                    $options = ['semua' => 'Semua Shift di Cabang Ini'];
                                    foreach ($shifts as $s) {
                                        $masuk = substr($s->jam_masuk, 0, 5);
                                        $pulang = substr($s->jam_pulang, 0, 5);
                                        $options[$s->nama_shift] = "{$s->nama_shift} ({$masuk} - {$pulang} WIB)";
                                    }
                                    return $options;
                                }

                                // Jika semua cabang: tampilkan pilihan shift unik tanpa duplikasi (Shift 1, Shift 2, Shift 3 dst.)
                                $distinctShifts = \App\Models\MasterShift::select('nama_shift')
                                    ->distinct()
                                    ->orderBy('nama_shift')
                                    ->pluck('nama_shift', 'nama_shift')
                                    ->toArray();

                                return ['semua' => 'Semua Shift (Seluruh Cabang)'] + $distinctShifts;
                            })
                            ->default('semua'),
                        \Filament\Forms\Components\Select::make('user_id')
                            ->label('Filter Karyawan (Per Orang)')
                            ->options(function (callable $get) {
                                $cabangId = $get('cabang_id');
                                $query = \App\Models\User::role('karyawan')->orderBy('name');
                                if ($cabangId && $cabangId !== 'semua') {
                                    $query->where('cabang_id', $cabangId);
                                }
                                return ['semua' => 'Semua Karyawan'] + $query->pluck('name', 'id')->toArray();
                            })
                            ->searchable()
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
                            'shift_id' => $data['shift_nama'] ?? 'semua',
                            'shift_nama' => $data['shift_nama'] ?? 'semua',
                            'user_id' => $data['user_id'] ?? 'semua',
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
