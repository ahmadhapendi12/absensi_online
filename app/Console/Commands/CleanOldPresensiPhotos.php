<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanOldPresensiPhotos extends Command
{
    protected $signature = 'absensi:clean-old-photos {--days=60 : Hapus file foto presensi yang lebih lama dari X hari}';

    protected $description = 'Bersihkan berkas fisik foto presensi lama dari server untuk menghemat penyimpanan disk, tanpa menghapus riwayat jam kerja.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days)->toDateString();

        $this->info("Mencari berkas foto presensi sebelum tanggal {$cutoffDate} ({$days} hari lalu)...");

        $records = Absensi::where('tanggal', '<', $cutoffDate)
            ->where(function ($q) {
                $q->whereNotNull('foto_masuk')
                  ->orWhereNotNull('foto_pulang');
            })
            ->get();

        $deletedCount = 0;
        $bytesSaved = 0;

        foreach ($records as $record) {
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

        $mbSaved = round($bytesSaved / (1024 * 1024), 2);
        $this->info("Pembersihan selesai! {$deletedCount} berkas foto berhasil dihapus. Ruang penyimpanan dihemat sebesar {$mbSaved} MB.");

        return Command::SUCCESS;
    }
}
