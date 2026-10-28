<?php

namespace App\Filament\Karyawan\Resources\RekapPresensis\Tables;

use App\Models\MasterShift;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RekapPresensiKaryawansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->striped()
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->description(fn ($record) => Carbon::parse($record->tanggal)->translatedFormat('l'))
                    ->sortable(),

                TextColumn::make('jadwal.shift.nama_shift')
                    ->label('Shift Kerja')
                    ->getStateUsing(function ($record) {
                        return $record->jadwal?->shift?->nama_shift 
                            ?? $record->user?->jadwal?->shift?->nama_shift 
                            ?? 'Shift Standar';
                    })
                    ->badge()
                    ->color('gray')
                    ->visibleFrom('sm'),

                TextColumn::make('format_jam_kerja')
                    ->label('Jam Kerja')
                    ->placeholder('-')
                    ->badge()
                    ->color('info'),

                TextColumn::make('status_display')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Sedang Berlangsung' => 'info',
                        'Terlambat' => 'warning',
                        'Tidak Absen Pulang' => 'danger',
                        'Izin', 'Cuti' => 'info',
                        'Sakit' => 'danger',
                        'Libur' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('menit_terlambat')
                    ->label('Keterlambatan')
                    ->formatStateUsing(function ($state, $record) {
                        if (in_array($record->status_masuk, ['Izin', 'Libur', 'Sakit', 'Cuti']) || !$record->jam_masuk) {
                            return '-';
                        }
                        if (!$state || $state <= 0) {
                            return 'Tepat Waktu';
                        }
                        return "{$state} Menit";
                    })
                    ->badge()
                    ->color(function ($state, $record) {
                        if (in_array($record->status_masuk, ['Izin', 'Libur', 'Sakit', 'Cuti']) || !$record->jam_masuk) {
                            return 'gray';
                        }
                        return $state > 0 ? 'warning' : 'success';
                    })
                    ->visibleFrom('md')
                    ->toggleable(),

                ImageColumn::make('foto_masuk')
                    ->label('Foto Masuk')
                    ->circular()
                    ->disk('public')
                    ->placeholder('Tidak ada')
                    ->size(40)
                    ->toggleable(),

                ImageColumn::make('foto_pulang')
                    ->label('Foto Pulang')
                    ->circular()
                    ->disk('public')
                    ->placeholder('Tidak ada')
                    ->size(40)
                    ->toggleable(),
            ])
            ->actions([
                Action::make('detailFoto')
                    ->iconButton()
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat Detail & Foto Presensi')
                    ->color('gray')
                    ->modalHeading(fn ($record) => 'Detail Presensi: ' . Carbon::parse($record->tanggal)->translatedFormat('l, d F Y'))
                    ->modalContent(fn ($record) => view('filament.karyawan.modals.detail-presensi', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->filters([
                SelectFilter::make('status_masuk')
                    ->label('Status Kehadiran')
                    ->options([
                        'Hadir' => 'Hadir Tepat Waktu',
                        'Terlambat' => 'Terlambat',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                        'Cuti' => 'Cuti',
                        'Libur' => 'Libur',
                        'sedang_berlangsung' => 'Sedang Berlangsung',
                        'tidak_absen_pulang' => 'Tidak Absen Pulang',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (!$value) return $query;

                        if ($value === 'sedang_berlangsung') {
                            return $query->whereNotNull('jam_masuk')
                                ->whereNull('jam_pulang')
                                ->whereDate('tanggal', now()->toDateString());
                        }

                        if ($value === 'tidak_absen_pulang') {
                            return $query->whereNotNull('jam_masuk')
                                ->whereNull('jam_pulang')
                                ->whereDate('tanggal', '<', now()->toDateString());
                        }

                        return $query->where('status_masuk', $value);
                    }),

                SelectFilter::make('shift_id')
                    ->label('Shift Kerja')
                    ->options(function () {
                        $userId = Auth::id();
                        // Ambil shift yang pernah dipakai oleh karyawan ini
                        return MasterShift::whereHas('jadwal', function ($q) use ($userId) {
                            $q->where('user_id', $userId);
                        })->pluck('nama_shift', 'id')->toArray();
                    })
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (!$value) return $query;

                        return $query->whereHas('jadwal', function ($q) use ($value) {
                            $q->where('shift_id', $value);
                        });
                    }),

                Filter::make('bulan_filter')
                    ->form([
                        \Filament\Forms\Components\Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ])
                            ->placeholder('Semua Bulan'),
                        \Filament\Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(function () {
                                $tahun = now()->year;
                                return [
                                    $tahun => (string) $tahun,
                                    $tahun - 1 => (string) ($tahun - 1),
                                ];
                            })
                            ->default(now()->year),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $bulan = $data['bulan'] ?? null;
                        $tahun = $data['tahun'] ?? null;

                        if ($bulan && $tahun) {
                            $start = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
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
                            $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');
                            return "Periode: {$namaBulan}";
                        }
                        if ($tahun) {
                            return "Tahun: {$tahun}";
                        }
                        return null;
                    }),
            ]);
    }
}
