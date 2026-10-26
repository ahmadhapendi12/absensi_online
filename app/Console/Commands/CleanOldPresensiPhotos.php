<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use App\Models\Lembur;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanOldPresensiPhotos extends Command
{
    protected $signature = 'absensi:clean-old-photos {--days=30 : Hapus file foto presensi & lembur yang lebih lama dari 30 hari (1 bulan)}';

    protected $description = 'Bersihkan berkas fisik foto presensi dan lembur lama dari server untuk menghemat penyimpanan disk, tanpa menghapus riwayat jam kerja.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days)->toDateString();

        $this->info("Mencari berkas foto presensi & lembur sebelum tanggal {$cutoffDate} ({$days} hari lalu)...");

        $deletedCount = 0;
        $bytesSaved = 0;

        // 1. Bersihkan foto presensi (absensi)
        $absensiRecords = Absensi::where('tanggal', '<', $cutoffDate)
            ->where(function ($q) {
                $q->whereNotNull('foto_masuk')
                  ->orWhereNotNull('foto_pulang');
            })
            ->get();

        foreach ($absensiRecords as $record) {
            if ($record->foto_masuk) {
                if (Storage::disk('public')->exists($record->foto_masuk)) {
                    $bytesSaved += Storage::disk('public')->size($record->foto_masuk);
                    Storage::disk('public')->delete($record->foto_masuk);
                    $deletedCount++;
                }
                $record->update(['foto_masuk' => null]);
            }

            if ($record->foto_pulang) {
                if (Storage::disk('public')->exists($record->foto_pulang)) {
                    $bytesSaved += Storage::disk('public')->size($record->foto_pulang);
                    Storage::disk('public')->delete($record->foto_pulang);
                    $deletedCount++;
                }
                $record->update(['foto_pulang' => null]);
            }
        }

        // 2. Bersihkan foto lembur (foto_mulai & foto_selesai)
        $lemburRecords = Lembur::where('tanggal', '<', $cutoffDate)
            ->where(function ($q) {
                $q->whereNotNull('foto_mulai')
                  ->orWhereNotNull('foto_selesai');
            })
            ->get();

        foreach ($lemburRecords as $record) {
            if ($record->foto_mulai) {
                if (Storage::disk('public')->exists($record->foto_mulai)) {
                    $bytesSaved += Storage::disk('public')->size($record->foto_mulai);
                    Storage::disk('public')->delete($record->foto_mulai);
                    $deletedCount++;
                }
                $record->update(['foto_mulai' => null]);
            }

            if ($record->foto_selesai) {
                if (Storage::disk('public')->exists($record->foto_selesai)) {
                    $bytesSaved += Storage::disk('public')->size($record->foto_selesai);
                    Storage::disk('public')->delete($record->foto_selesai);
                    $deletedCount++;
                }
                $record->update(['foto_selesai' => null]);
            }
        }

        $mbSaved = round($bytesSaved / (1024 * 1024), 2);
        $this->info("Pembersihan selesai! {$deletedCount} berkas foto berhasil dihapus (Presensi: {$absensiRecords->count()} rekap, Lembur: {$lemburRecords->count()} rekap). Ruang penyimpanan dihemat sebesar {$mbSaved} MB.");

        return Command::SUCCESS;
    }
}
