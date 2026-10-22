<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use App\Models\Absensi;
use Carbon\Carbon;

class LatestAbsensiWidget extends TableWidget
{
    protected static ?string $heading = 'Presensi Masuk Hari Ini';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Absensi::query()
                    ->with(['user.profile', 'user.cabang', 'jadwal.shift', 'user.jadwal.shift'])
                    ->where('tanggal', Carbon::today()->toDateString())
                    ->latest('jam_masuk')
            )
            ->columns([
                ImageColumn::make('user.avatar_url')
                    ->label('')
                    ->circular()
                    ->width(36)
                    ->height(36),
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->description(fn ($record) => 'NIK: ' . ($record->user?->profile?->nik ?? '-'))
                    ->sortable(),
                TextColumn::make('user.cabang.nama_cabang')
                    ->label('Cabang')
                    ->badge()
                    ->color('info'),
                TextColumn::make('shift_kerja')
                    ->label('Shift')
                    ->badge()
                    ->color('warning')
                    ->state(fn ($record) => $record->jadwal?->shift?->nama_shift ?? $record->user?->jadwal?->shift?->nama_shift ?? '-')
                    ->description(function ($record) {
                        $shift = $record->jadwal?->shift ?? $record->user?->jadwal?->shift;
                        if ($shift) {
                            return Carbon::parse($shift->jam_masuk)->format('H:i') . ' - ' . Carbon::parse($shift->jam_pulang)->format('H:i');
                        }
                        return null;
                    }),
                TextColumn::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->placeholder('-'),
                TextColumn::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->time('H:i')
                    ->placeholder('-'),
                TextColumn::make('status_masuk')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Tepat Waktu', 'Hadir' => 'success',
                        'Terlambat' => 'warning',
                        'Sakit', 'Izin', 'Cuti' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('menit_terlambat')
                    ->label('Keterlambatan')
                    ->formatStateUsing(function ($state, $record) {
                        if (in_array($record->status_masuk, ['Izin', 'Sakit', 'Cuti', 'Alpha']) || !$record->jam_masuk) {
                            return '-';
                        }
                        return $state > 0 ? "{$state} menit" : 'Tepat Waktu';
                    })
                    ->badge()
                    ->color(function ($state, $record) {
                        if (in_array($record->status_masuk, ['Izin', 'Sakit', 'Cuti', 'Alpha']) || !$record->jam_masuk) {
                            return 'gray';
                        }
                        return $state > 0 ? 'danger' : 'success';
                    }),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('cabang_id')
                    ->label('Cabang')
                    ->relationship('user.cabang', 'nama_cabang')
                    ->placeholder('Semua Cabang'),
            ])
            ->paginated([5, 10]);
    }
}
