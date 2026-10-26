<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AbsensiAutoLiburService;
use Carbon\Carbon;

class SyncAbsensiLibur extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absensi:sync-libur {--date= : Tanggal tertentu (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis mencatat status Libur bagi karyawan yang tidak masuk kerja setelah jam operasional shift berakhir';

    /**
     * Execute the console command.
     */
    public function handle(AbsensiAutoLiburService $service): int
    {
        $date = $this->option('date') ?? Carbon::today()->toDateString();
        $this->info("Menjalankan sinkronisasi status Libur untuk tanggal: {$date}...");

        $count = $service->syncLibur($date);

        $this->info("Sinkronisasi selesai. Sebanyak {$count} record karyawan ditandai/diperbarui sebagai 'Libur'.");

        return self::SUCCESS;
    }
}
