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

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Absensi::query()
                    ->with(['user.profile', 'user.cabang'])
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
                    ->formatStateUsing(fn ($state) => $state > 0 ? "{$state} menit" : 'Tepat Waktu')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
            ])
            ->paginated([5, 10]);
    }
}
