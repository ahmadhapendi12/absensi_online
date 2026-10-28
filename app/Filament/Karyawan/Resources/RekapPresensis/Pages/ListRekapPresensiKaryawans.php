<?php

namespace App\Filament\Karyawan\Resources\RekapPresensis\Pages;

use App\Filament\Karyawan\Resources\RekapPresensis\RekapPresensiKaryawanResource;
use App\Models\MasterShift;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListRekapPresensiKaryawans extends ListRecords
{
    protected static string $resource = RekapPresensiKaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Unduh Laporan PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->modalHeading('Unduh Rekap Presensi Mandiri (PDF)')
                ->modalDescription('Pilih periode tanggal, shift kerja, dan status kehadiran yang ingin diunduh ke format dokumen PDF.')
                ->modalSubmitActionLabel('Unduh Dokumen PDF')
                ->form([
                    Select::make('tipe_periode')
                        ->label('Jenis Periode')
                        ->options([
                            'bulan' => 'Periode Per Bulan',
                            'custom' => 'Rentang Tanggal Tertentu',
                        ])
                        ->default('bulan')
                        ->live()
                        ->required(),

                    Select::make('bulan')
                        ->label('Pilihan Bulan')
                        ->options([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                            4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ])
                        ->default(now()->month)
                        ->visible(fn (callable $get) => $get('tipe_periode') === 'bulan')
                        ->required(fn (callable $get) => $get('tipe_periode') === 'bulan'),

                    Select::make('tahun')
                        ->label('Pilihan Tahun')
                        ->options(function () {
                            $tahunSekarang = now()->year;
                            return [
                                $tahunSekarang => $tahunSekarang,
                                $tahunSekarang - 1 => $tahunSekarang - 1,
                            ];
                        })
                        ->default(now()->year)
                        ->visible(fn (callable $get) => $get('tipe_periode') === 'bulan')
                        ->required(fn (callable $get) => $get('tipe_periode') === 'bulan'),

                    DatePicker::make('tanggal_mulai')
                        ->label('Dari Tanggal')
                        ->default(now()->startOfMonth()->toDateString())
                        ->visible(fn (callable $get) => $get('tipe_periode') === 'custom')
                        ->required(fn (callable $get) => $get('tipe_periode') === 'custom'),

                    DatePicker::make('tanggal_selesai')
                        ->label('Sampai Tanggal')
                        ->default(now()->toDateString())
                        ->visible(fn (callable $get) => $get('tipe_periode') === 'custom')
                        ->required(fn (callable $get) => $get('tipe_periode') === 'custom'),

                    Select::make('shift_id')
                        ->label('Filter Shift Kerja')
                        ->options(function () {
                            $userId = Auth::id();
                            $shifts = MasterShift::whereHas('jadwal', function ($q) use ($userId) {
                                $q->where('user_id', $userId);
                            })->pluck('nama_shift', 'id')->toArray();
                            return ['semua' => 'Semua Shift'] + $shifts;
                        })
                        ->default('semua'),

                    Select::make('status_masuk')
                        ->label('Filter Status Kehadiran')
                        ->options([
                            'semua' => 'Semua Status',
                            'Hadir' => 'Hadir Tepat Waktu',
                            'Terlambat' => 'Terlambat',
                            'Izin' => 'Izin',
                            'Sakit' => 'Sakit',
                            'Cuti' => 'Cuti',
                            'Libur' => 'Libur',
                        ])
                        ->default('semua'),
                ])
                ->action(function (array $data) {
                    $params = [
                        'tipe_periode' => $data['tipe_periode'] ?? 'bulan',
                        'bulan' => $data['bulan'] ?? now()->month,
                        'tahun' => $data['tahun'] ?? now()->year,
                        'tanggal_mulai' => $data['tanggal_mulai'] ?? null,
                        'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
                        'shift_id' => $data['shift_id'] ?? 'semua',
                        'status_masuk' => $data['status_masuk'] ?? 'semua',
                    ];

                    $url = route('laporan.export-pdf', array_filter($params));
                    return redirect()->away($url);
                }),
        ];
    }
}
