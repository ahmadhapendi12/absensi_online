<?php

namespace App\Filament\Karyawan\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KaryawanRecentAbsensiWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected static ?string $heading = 'Riwayat Presensi Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Absensi::query()
                    ->where('user_id', Auth::id())
                    ->with(['jadwal.shift'])
                    ->latest('tanggal')
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->description(fn ($record) => Carbon::parse($record->tanggal)->translatedFormat('l')),

                TextColumn::make('jadwal.shift.nama_shift')
                    ->label('Shift Kerja')
                    ->placeholder('Reguler')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i') . ' WIB' : '-')
                    ->description(fn ($record) => $record->status_masuk ?? '-'),

                TextColumn::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i') . ' WIB' : 'Belum Pulang')
                    ->description(fn ($record) => $record->status_pulang ?? '-'),

                TextColumn::make('status_display')
                    ->label('Status Kehadiran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Sedang Berlangsung' => 'info',
                        'Terlambat' => 'warning',
                        'Tidak Absen Pulang' => 'danger',
                        'Izin', 'Cuti' => 'info',
                        'Sakit' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                Action::make('lihatSemua')
                    ->label('Buka Rekap & Unduh PDF')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url('/karyawan/rekap-presensis/rekap-presensi-karyawans'),
            ]);
    }
}
